<?php
/**
 * Configuration de base de données et constantes
 * Hotel Transport Management System
 */

// Configuration de la base de données MySQL
define('DB_HOST', getenv('DB_HOST') ?: 'mysql');
define('DB_NAME', getenv('DB_NAME') ?: 'hotel_transport');
define('DB_USER', getenv('DB_USER') ?: 'hotel_user');
define('DB_PASS', getenv('DB_PASS') ?: 'hotel_pass');
define('DB_CHARSET', 'utf8mb4');

// Configuration MongoDB
define('MONGO_HOST', getenv('MONGO_HOST') ?: 'mongodb');
define('MONGO_PORT', getenv('MONGO_PORT') ?: '27017');
define('MONGO_USER', getenv('MONGO_USER') ?: 'mongo_admin');
define('MONGO_PASS', getenv('MONGO_PASS') ?: 'mongo_pass');
define('MONGO_DB', 'hotel_transport_logs');

// Configuration de l'application
define('APP_NAME', 'Hotel Transport Management');
define('APP_VERSION', '1.0.0');
define('APP_ENV', getenv('APP_ENV') ?: 'development');
define('APP_DEBUG', APP_ENV === 'development');

// URLs
define('BASE_URL', getenv('BASE_URL') ?: 'http://localhost');
define('API_URL', getenv('API_URL') ?: 'http://localhost:8000/api');

// Sécurité
define('JWT_SECRET', getenv('JWT_SECRET') ?: 'your-secret-key-change-in-production');
define('SESSION_LIFETIME', 7200); // 2 heures
define('PASSWORD_MIN_LENGTH', 8);

// Email
define('SMTP_HOST', getenv('SMTP_HOST') ?: 'smtp.gmail.com');
define('SMTP_PORT', getenv('SMTP_PORT') ?: 587);
define('SMTP_USER', getenv('SMTP_USER') ?: '');
define('SMTP_PASS', getenv('SMTP_PASS') ?: '');
define('SMTP_FROM', getenv('SMTP_FROM') ?: 'noreply@hoteltransport.ma');
define('SMTP_FROM_NAME', 'HTM Transport');

// SMS Configuration (example with Twilio)
define('SMS_PROVIDER', getenv('SMS_PROVIDER') ?: 'twilio');
define('SMS_API_KEY', getenv('SMS_API_KEY') ?: '');
define('SMS_API_SECRET', getenv('SMS_API_SECRET') ?: '');
define('SMS_FROM_NUMBER', getenv('SMS_FROM_NUMBER') ?: '');

// Uploads
define('UPLOAD_MAX_SIZE', 5242880); // 5MB
define('UPLOAD_ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf']);
define('UPLOAD_PATH', __DIR__ . '/uploads/');

// Timezone
date_default_timezone_set('Africa/Casablanca');

// Error reporting
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', !APP_DEBUG);

/**
 * Connexion à la base de données MySQL avec PDO
 */
function getDbConnection() {
    static $pdo = null;

    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];

            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            if (APP_DEBUG) {
                die("Erreur de connexion à la base de données: " . $e->getMessage());
            } else {
                die("Erreur de connexion à la base de données. Veuillez réessayer plus tard.");
            }
        }
    }

    return $pdo;
}

/**
 * Connexion MongoDB
 */
function getMongoConnection() {
    try {
        $connectionString = sprintf(
            "mongodb://%s:%s@%s:%s/%s?authSource=admin",
            MONGO_USER,
            MONGO_PASS,
            MONGO_HOST,
            MONGO_PORT,
            MONGO_DB
        );

        $client = new MongoDB\Driver\Manager($connectionString);
        return $client;
    } catch (Exception $e) {
        if (APP_DEBUG) {
            die("Erreur de connexion MongoDB: " . $e->getMessage());
        }
        return null;
    }
}

/**
 * Fonction pour logger les activités
 */
function logActivity($userId, $action, $entityType = null, $entityId = null, $details = []) {
    $pdo = getDbConnection();

    try {
        $stmt = $pdo->prepare("
            INSERT INTO audit_logs (user_id, action, entity_type, entity_id, new_values, ip_address, user_agent)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $userId,
            $action,
            $entityType,
            $entityId,
            json_encode($details),
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    } catch (PDOException $e) {
        error_log("Erreur lors du logging: " . $e->getMessage());
    }
}

/**
 * Fonction pour envoyer une notification
 */
function sendNotification($userId, $type, $title, $message, $data = [], $link = null) {
    $pdo = getDbConnection();

    try {
        $stmt = $pdo->prepare("
            INSERT INTO notifications (user_id, type, title, message, data, link)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $userId,
            $type,
            $title,
            $message,
            json_encode($data),
            $link
        ]);

        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        error_log("Erreur lors de l'envoi de notification: " . $e->getMessage());
        return false;
    }
}

/**
 * Fonction pour hasher un mot de passe
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Fonction pour vérifier un mot de passe
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Fonction pour générer un token aléatoire
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Fonction pour nettoyer les entrées
 */
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Fonction pour valider un email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Fonction pour valider un numéro de téléphone marocain
 */
function validateMoroccanPhone($phone) {
    $phone = preg_replace('/[\s\-]/', '', $phone);
    return preg_match('/^(\+212|0)[5-7]\d{8}$/', $phone);
}

/**
 * Fonction pour envoyer un email
 */
function sendEmail($to, $subject, $body, $isHtml = true) {
    // Utiliser PHPMailer ou mail() selon la configuration
    // Implémentation basique avec mail()
    $headers = "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM . ">\r\n";
    $headers .= "Reply-To: " . SMTP_FROM . "\r\n";

    if ($isHtml) {
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    }

    return mail($to, $subject, $body, $headers);
}

/**
 * Fonction pour envoyer un SMS
 */
function sendSMS($to, $message) {
    // Implémentation basique - À adapter selon le provider
    // Exemple avec une API REST générique

    $apiUrl = "https://api.example.com/sms";
    $data = [
        'to' => $to,
        'message' => $message,
        'from' => SMS_FROM_NUMBER
    ];

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . SMS_API_KEY
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $httpCode === 200;
}

/**
 * Fonction pour upload un fichier
 */
function uploadFile($file, $allowedTypes = UPLOAD_ALLOWED_TYPES) {
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'error' => 'Paramètres invalides'];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Erreur lors de l\'upload'];
    }

    if ($file['size'] > UPLOAD_MAX_SIZE) {
        return ['success' => false, 'error' => 'Fichier trop volumineux'];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    if (!in_array($mimeType, $allowedTypes)) {
        return ['success' => false, 'error' => 'Type de fichier non autorisé'];
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = sprintf('%s.%s', bin2hex(random_bytes(16)), $extension);
    $destination = UPLOAD_PATH . $fileName;

    if (!is_dir(UPLOAD_PATH)) {
        mkdir(UPLOAD_PATH, 0755, true);
    }

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => true, 'filename' => $fileName, 'path' => $destination];
    }

    return ['success' => false, 'error' => 'Impossible de sauvegarder le fichier'];
}

/**
 * Fonction pour calculer la distance entre deux coordonnées (Haversine)
 */
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371; // km

    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a = sin($dLat/2) * sin($dLat/2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLon/2) * sin($dLon/2);

    $c = 2 * atan2(sqrt($a), sqrt(1-$a));

    return $earthRadius * $c;
}

/**
 * Fonction pour formater le prix
 */
function formatPrice($price) {
    return number_format($price, 2, ',', ' ') . ' MAD';
}

/**
 * Fonction de réponse JSON
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Fonction pour gérer les erreurs
 */
function handleError($message, $code = 400) {
    jsonResponse([
        'success' => false,
        'error' => $message,
        'code' => $code
    ], $code);
}

/**
 * Fonction pour gérer le succès
 */
function handleSuccess($data = [], $message = 'Opération réussie') {
    jsonResponse([
        'success' => true,
        'message' => $message,
        'data' => $data
    ], 200);
}

// Headers CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
