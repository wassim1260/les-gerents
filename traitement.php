<?php
// Connexion à la base de données
$host = 'localhost';
$dbname = 'projet_gerants';
$user = 'root';
$pass = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupérer les données du formulaire
$nom = $_POST['nom'] ?? '';
$email = $_POST['email'] ?? '';
$telephone = $_POST['telephone'] ?? '';
$pays = $_POST['pays'] ?? '';
$entreprise = $_POST['entreprise'] ?? '';
$poste = $_POST['poste'] ?? '';
$objectif = $_POST['objectif'] ?? '';
$session = $_POST['session'] ?? '';
$message = $_POST['message'] ?? '';

// Préparer et exécuter la requête
try {
    $stmt = $pdo->prepare("INSERT INTO inscriptions 
        (nom, email, telephone, pays, entreprise, poste, objectif, session, message)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nom, $email, $telephone, $pays, $entreprise, $poste, $objectif, $session, $message]);

    // Rediriger vers la page de bienvenue
    header("Location: welcome.php?nom=" . urlencode($nom));
    exit;
} catch (PDOException $e) {
    echo "❌ Erreur lors de l'insertion : " . $e->getMessage();
}
?>
