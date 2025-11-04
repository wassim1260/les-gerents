<?php
/**
 * API d'authentification
 * Gère la connexion, l'inscription et la gestion des tokens
 */

require_once __DIR__ . '/../config.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'register':
        if ($method === 'POST') {
            handleRegister();
        }
        break;

    case 'login':
        if ($method === 'POST') {
            handleLogin();
        }
        break;

    case 'logout':
        if ($method === 'POST') {
            handleLogout();
        }
        break;

    case 'verify-email':
        if ($method === 'GET') {
            handleVerifyEmail();
        }
        break;

    case 'forgot-password':
        if ($method === 'POST') {
            handleForgotPassword();
        }
        break;

    case 'reset-password':
        if ($method === 'POST') {
            handleResetPassword();
        }
        break;

    case 'refresh-token':
        if ($method === 'POST') {
            handleRefreshToken();
        }
        break;

    default:
        handleError('Action non reconnue', 404);
}

/**
 * Inscription d'un nouveau client
 */
function handleRegister() {
    $data = json_decode(file_get_contents('php://input'), true);

    // Validation des données
    $required = ['first_name', 'last_name', 'email', 'phone', 'password'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            handleError("Le champ $field est requis");
        }
    }

    $firstName = cleanInput($data['first_name']);
    $lastName = cleanInput($data['last_name']);
    $email = cleanInput($data['email']);
    $phone = cleanInput($data['phone']);
    $password = $data['password'];
    $cityId = $data['city_id'] ?? null;
    $companyName = $data['company_name'] ?? null;

    // Validation de l'email
    if (!validateEmail($email)) {
        handleError('Email invalide');
    }

    // Validation du téléphone
    if (!validateMoroccanPhone($phone)) {
        handleError('Numéro de téléphone invalide. Format attendu: +212XXXXXXXXX ou 06XXXXXXXX');
    }

    // Validation du mot de passe
    if (strlen($password) < PASSWORD_MIN_LENGTH) {
        handleError('Le mot de passe doit contenir au moins ' . PASSWORD_MIN_LENGTH . ' caractères');
    }

    $pdo = getDbConnection();

    try {
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            handleError('Cet email est déjà utilisé');
        }

        // Vérifier si le téléphone existe déjà
        $stmt = $pdo->prepare("SELECT id FROM users WHERE phone = ?");
        $stmt->execute([$phone]);
        if ($stmt->fetch()) {
            handleError('Ce numéro de téléphone est déjà utilisé');
        }

        // Commencer une transaction
        $pdo->beginTransaction();

        // Insérer l'utilisateur (role_id = 3 pour client)
        $hashedPassword = hashPassword($password);
        $stmt = $pdo->prepare("
            INSERT INTO users (role_id, email, password, first_name, last_name, phone)
            VALUES (3, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$email, $hashedPassword, $firstName, $lastName, $phone]);
        $userId = $pdo->lastInsertId();

        // Créer le profil client
        $stmt = $pdo->prepare("
            INSERT INTO client_profiles (user_id, company_name, city_id)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$userId, $companyName, $cityId]);

        // Générer un token de vérification d'email
        $verificationToken = generateToken();
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

        $stmt = $pdo->prepare("
            INSERT INTO password_reset_tokens (user_id, token, expires_at)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$userId, $verificationToken, $expiresAt]);

        // Logger l'activité
        logActivity($userId, 'register', 'user', $userId);

        // Envoyer un email de vérification
        $verificationLink = BASE_URL . "/api/auth.php?action=verify-email&token=$verificationToken";
        $emailBody = "
            <h2>Bienvenue sur HTM Transport !</h2>
            <p>Bonjour $firstName $lastName,</p>
            <p>Merci de vous être inscrit. Veuillez cliquer sur le lien ci-dessous pour vérifier votre adresse email :</p>
            <p><a href='$verificationLink'>Vérifier mon email</a></p>
            <p>Ce lien expirera dans 24 heures.</p>
        ";
        sendEmail($email, 'Vérification de votre compte HTM Transport', $emailBody);

        // Commit de la transaction
        $pdo->commit();

        // Générer un JWT token
        $token = generateJWT($userId, 3, $email);

        handleSuccess([
            'user_id' => $userId,
            'token' => $token,
            'email' => $email,
            'name' => "$firstName $lastName",
            'role' => 'client'
        ], 'Inscription réussie ! Un email de vérification a été envoyé.');

    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Erreur lors de l'inscription: " . $e->getMessage());
        handleError('Erreur lors de l\'inscription. Veuillez réessayer.', 500);
    }
}

/**
 * Connexion d'un utilisateur
 */
function handleLogin() {
    $data = json_decode(file_get_contents('php://input'), true);

    $email = cleanInput($data['email'] ?? '');
    $password = $data['password'] ?? '';
    $rememberMe = $data['remember_me'] ?? false;

    if (empty($email) || empty($password)) {
        handleError('Email et mot de passe requis');
    }

    $pdo = getDbConnection();

    try {
        $stmt = $pdo->prepare("
            SELECT u.*, r.name as role_name
            FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE u.email = ? AND u.is_active = 1
        ");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !verifyPassword($password, $user['password'])) {
            handleError('Email ou mot de passe incorrect');
        }

        // Mettre à jour last_login
        $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $stmt->execute([$user['id']]);

        // Générer un token de session
        $tokenExpiry = $rememberMe ? strtotime('+30 days') : strtotime('+2 hours');
        $token = generateJWT($user['id'], $user['role_id'], $user['email'], $tokenExpiry);

        // Sauvegarder la session
        $stmt = $pdo->prepare("
            INSERT INTO sessions (user_id, token, ip_address, user_agent, expires_at)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $user['id'],
            $token,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null,
            date('Y-m-d H:i:s', $tokenExpiry)
        ]);

        // Logger l'activité
        logActivity($user['id'], 'login', 'user', $user['id']);

        // Envoyer une notification
        sendNotification(
            $user['id'],
            'login',
            'Nouvelle connexion',
            'Vous venez de vous connecter à votre compte',
            ['ip' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown']
        );

        handleSuccess([
            'user_id' => $user['id'],
            'token' => $token,
            'email' => $user['email'],
            'name' => $user['first_name'] . ' ' . $user['last_name'],
            'role' => $user['role_name'],
            'avatar' => $user['avatar'],
            'email_verified' => (bool)$user['email_verified'],
            'phone_verified' => (bool)$user['phone_verified']
        ], 'Connexion réussie');

    } catch (PDOException $e) {
        error_log("Erreur lors de la connexion: " . $e->getMessage());
        handleError('Erreur lors de la connexion', 500);
    }
}

/**
 * Déconnexion
 */
function handleLogout() {
    $headers = getallheaders();
    $token = $headers['Authorization'] ?? '';
    $token = str_replace('Bearer ', '', $token);

    if (empty($token)) {
        handleError('Token manquant');
    }

    $pdo = getDbConnection();

    try {
        $stmt = $pdo->prepare("DELETE FROM sessions WHERE token = ?");
        $stmt->execute([$token]);

        handleSuccess([], 'Déconnexion réussie');
    } catch (PDOException $e) {
        error_log("Erreur lors de la déconnexion: " . $e->getMessage());
        handleError('Erreur lors de la déconnexion', 500);
    }
}

/**
 * Vérification d'email
 */
function handleVerifyEmail() {
    $token = $_GET['token'] ?? '';

    if (empty($token)) {
        handleError('Token manquant');
    }

    $pdo = getDbConnection();

    try {
        $stmt = $pdo->prepare("
            SELECT user_id FROM password_reset_tokens
            WHERE token = ? AND used = 0 AND expires_at > NOW()
        ");
        $stmt->execute([$token]);
        $result = $stmt->fetch();

        if (!$result) {
            handleError('Token invalide ou expiré');
        }

        // Marquer l'email comme vérifié
        $stmt = $pdo->prepare("UPDATE users SET email_verified = 1 WHERE id = ?");
        $stmt->execute([$result['user_id']]);

        // Marquer le token comme utilisé
        $stmt = $pdo->prepare("UPDATE password_reset_tokens SET used = 1 WHERE token = ?");
        $stmt->execute([$token]);

        logActivity($result['user_id'], 'email_verified', 'user', $result['user_id']);

        handleSuccess([], 'Email vérifié avec succès');

    } catch (PDOException $e) {
        error_log("Erreur lors de la vérification d'email: " . $e->getMessage());
        handleError('Erreur lors de la vérification', 500);
    }
}

/**
 * Mot de passe oublié
 */
function handleForgotPassword() {
    $data = json_decode(file_get_contents('php://input'), true);
    $email = cleanInput($data['email'] ?? '');

    if (empty($email) || !validateEmail($email)) {
        handleError('Email invalide');
    }

    $pdo = getDbConnection();

    try {
        $stmt = $pdo->prepare("SELECT id, first_name, last_name FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            // Ne pas révéler si l'email existe ou non
            handleSuccess([], 'Si cet email existe, un lien de réinitialisation a été envoyé');
        }

        // Générer un token de réinitialisation
        $resetToken = generateToken();
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt = $pdo->prepare("
            INSERT INTO password_reset_tokens (user_id, token, expires_at)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$user['id'], $resetToken, $expiresAt]);

        // Envoyer l'email
        $resetLink = BASE_URL . "/reset-password.html?token=$resetToken";
        $emailBody = "
            <h2>Réinitialisation de mot de passe</h2>
            <p>Bonjour {$user['first_name']} {$user['last_name']},</p>
            <p>Vous avez demandé à réinitialiser votre mot de passe. Cliquez sur le lien ci-dessous :</p>
            <p><a href='$resetLink'>Réinitialiser mon mot de passe</a></p>
            <p>Ce lien expirera dans 1 heure.</p>
            <p>Si vous n'avez pas fait cette demande, ignorez cet email.</p>
        ";
        sendEmail($email, 'Réinitialisation de votre mot de passe', $emailBody);

        logActivity($user['id'], 'password_reset_requested', 'user', $user['id']);

        handleSuccess([], 'Un lien de réinitialisation a été envoyé à votre email');

    } catch (PDOException $e) {
        error_log("Erreur lors de la demande de réinitialisation: " . $e->getMessage());
        handleError('Erreur lors de la demande', 500);
    }
}

/**
 * Réinitialisation de mot de passe
 */
function handleResetPassword() {
    $data = json_decode(file_get_contents('php://input'), true);

    $token = $data['token'] ?? '';
    $newPassword = $data['password'] ?? '';

    if (empty($token) || empty($newPassword)) {
        handleError('Token et nouveau mot de passe requis');
    }

    if (strlen($newPassword) < PASSWORD_MIN_LENGTH) {
        handleError('Le mot de passe doit contenir au moins ' . PASSWORD_MIN_LENGTH . ' caractères');
    }

    $pdo = getDbConnection();

    try {
        $stmt = $pdo->prepare("
            SELECT user_id FROM password_reset_tokens
            WHERE token = ? AND used = 0 AND expires_at > NOW()
        ");
        $stmt->execute([$token]);
        $result = $stmt->fetch();

        if (!$result) {
            handleError('Token invalide ou expiré');
        }

        // Mettre à jour le mot de passe
        $hashedPassword = hashPassword($newPassword);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashedPassword, $result['user_id']]);

        // Marquer le token comme utilisé
        $stmt = $pdo->prepare("UPDATE password_reset_tokens SET used = 1 WHERE token = ?");
        $stmt->execute([$token]);

        // Invalider toutes les sessions existantes
        $stmt = $pdo->prepare("DELETE FROM sessions WHERE user_id = ?");
        $stmt->execute([$result['user_id']]);

        logActivity($result['user_id'], 'password_reset', 'user', $result['user_id']);

        handleSuccess([], 'Mot de passe réinitialisé avec succès');

    } catch (PDOException $e) {
        error_log("Erreur lors de la réinitialisation: " . $e->getMessage());
        handleError('Erreur lors de la réinitialisation', 500);
    }
}

/**
 * Rafraîchir le token
 */
function handleRefreshToken() {
    $headers = getallheaders();
    $token = $headers['Authorization'] ?? '';
    $token = str_replace('Bearer ', '', $token);

    if (empty($token)) {
        handleError('Token manquant');
    }

    $pdo = getDbConnection();

    try {
        $stmt = $pdo->prepare("
            SELECT s.user_id, u.role_id, u.email
            FROM sessions s
            JOIN users u ON s.user_id = u.id
            WHERE s.token = ? AND s.expires_at > NOW()
        ");
        $stmt->execute([$token]);
        $session = $stmt->fetch();

        if (!$session) {
            handleError('Session invalide ou expirée', 401);
        }

        // Générer un nouveau token
        $newToken = generateJWT($session['user_id'], $session['role_id'], $session['email']);

        // Mettre à jour la session
        $stmt = $pdo->prepare("
            UPDATE sessions
            SET token = ?, expires_at = ?
            WHERE token = ?
        ");
        $stmt->execute([
            $newToken,
            date('Y-m-d H:i:s', strtotime('+2 hours')),
            $token
        ]);

        handleSuccess(['token' => $newToken], 'Token rafraîchi');

    } catch (PDOException $e) {
        error_log("Erreur lors du rafraîchissement du token: " . $e->getMessage());
        handleError('Erreur lors du rafraîchissement', 500);
    }
}

/**
 * Générer un JWT token (simplifié)
 */
function generateJWT($userId, $roleId, $email, $expiry = null) {
    if ($expiry === null) {
        $expiry = strtotime('+2 hours');
    }

    $payload = [
        'user_id' => $userId,
        'role_id' => $roleId,
        'email' => $email,
        'exp' => $expiry,
        'iat' => time()
    ];

    // Version simplifiée - utiliser une vraie librairie JWT en production
    $header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
    $payload = base64_encode(json_encode($payload));
    $signature = hash_hmac('sha256', "$header.$payload", JWT_SECRET);

    return "$header.$payload." . base64_encode($signature);
}
