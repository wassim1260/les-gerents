<?php
// Récupérer le nom de l'utilisateur depuis l'URL
$nom = $_GET['nom'] ?? 'Utilisateur';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue</title>
    <style>
        /* Réinitialiser les marges et les paddings */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Définir une police moderne */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f7fc;
            color: #333;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Conteneur principal */
        .welcome-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 100%;
            max-width: 500px;
        }

        /* Titre principal */
        h1 {
            font-size: 2.5rem;
            color: #4A90E2;
            margin-bottom: 20px;
        }

        /* Texte du message */
        p {
            font-size: 1.1rem;
            color: #555;
            margin-bottom: 30px;
        }

        /* Bouton retour */
        .btn-back {
            display: inline-block;
            font-size: 1.1rem;
            color: #fff;
            background-color: #4A90E2;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 30px;
            transition: background-color 0.3s ease;
        }

        .btn-back:hover {
            background-color: #357ABD;
        }

        .btn-back:focus {
            outline: none;
            box-shadow: 0 0 5px rgba(72, 144, 226, 0.7);
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <h1>Bienvenue, <?php echo htmlspecialchars($nom); ?> !</h1>
        <p>Merci de vous être inscrit. Nous avons bien reçu vos informations et nous vous contacterons prochainement.</p>
        <a href="about.html" class="btn-back">Retour à l'accueil</a>
    </div>
</body>
</html>
