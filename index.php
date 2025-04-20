<?php
// Connexion PDO
$host = "localhost";
$dbname = "Projet_Gerants;";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur: " . $e->getMessage());
}

// INSERT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_user'])) {
        $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email) VALUES (?, ?)");
        $stmt->execute([$_POST['nom'], $_POST['email']]);
    } elseif (isset($_POST['add_product'])) {
        $stmt = $pdo->prepare("INSERT INTO produits (nom, prix) VALUES (?, ?)");
        $stmt->execute([$_POST['produit_nom'], $_POST['prix']]);
    } elseif (isset($_POST['update_user'])) {
        $stmt = $pdo->prepare("UPDATE utilisateurs SET nom=?, email=? WHERE id=?");
        $stmt->execute([$_POST['nom'], $_POST['email'], $_POST['id']]);
    } elseif (isset($_POST['update_product'])) {
        $stmt = $pdo->prepare("UPDATE produits SET nom=?, prix=? WHERE id=?");
        $stmt->execute([$_POST['nom'], $_POST['prix'], $_POST['id']]);
    }
    header("Location: index.php");
    exit();
}

// DELETE
if (isset($_GET['delete']) && isset($_GET['type']) && isset($_GET['id'])) {
    if ($_GET['type'] === 'user') {
        $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id=?");
    } else {
        $stmt = $pdo->prepare("DELETE FROM produits WHERE id=?");
    }
    $stmt->execute([$_GET['id']]);
    header("Location: index.php");
    exit();
}

// UPDATE FORMS
$edit_user = null;
$edit_product = null;

if (isset($_GET['edit']) && isset($_GET['type']) && isset($_GET['id'])) {
    if ($_GET['type'] === 'user') {
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id=?");
        $stmt->execute([$_GET['id']]);
        $edit_user = $stmt->fetch();
    } else {
        $stmt = $pdo->prepare("SELECT * FROM produits WHERE id=?");
        $stmt->execute([$_GET['id']]);
        $edit_product = $stmt->fetch();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion Utilisateurs et Produits (PDO)</title>
    <style>
        body { font-family: Arial; margin: 40px; }
        form { margin-bottom: 20px; }
        input, button { margin: 5px; padding: 5px; }
        h2 { margin-top: 40px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 30px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        a { margin-right: 10px; }
    </style>
</head>
<body>

<h2><?= $edit_user ? "Modifier Utilisateur" : "Ajouter Utilisateur" ?></h2>
<form method="post">
    <input type="text" name="nom" placeholder="Nom" value="<?= $edit_user['nom'] ?? '' ?>" required>
    <input type="email" name="email" placeholder="Email" value="<?= $edit_user['email'] ?? '' ?>" required>
    <?php if ($edit_user): ?>
        <input type="hidden" name="id" value="<?= $edit_user['id'] ?>">
        <button type="submit" name="update_user">Mettre à jour</button>
    <?php else: ?>
        <button type="submit" name="add_user">Ajouter</button>
    <?php endif; ?>
</form>

<h2><?= $edit_product ? "Modifier Produit" : "Ajouter Produit" ?></h2>
<form method="post">
    <input type="text" name="nom" placeholder="Nom du produit" value="<?= $edit_product['nom'] ?? '' ?>" required>
    <input type="number" step="0.01" name="prix" placeholder="Prix" value="<?= $edit_product['prix'] ?? '' ?>" required>
    <?php if ($edit_product): ?>
        <input type="hidden" name="id" value="<?= $edit_product['id'] ?>">
        <button type="submit" name="update_product">Mettre à jour</button>
    <?php else: ?>
        <button type="submit" name="add_product">Ajouter</button>
    <?php endif; ?>
</form>

<h2>Liste des Utilisateurs</h2>
<table>
    <tr><th>ID</th><th>Nom</th><th>Email</th><th>Actions</th></tr>
    <?php
    $users = $pdo->query("SELECT * FROM utilisateurs");
    foreach ($users as $u) {
        echo "<tr>
                <td>{$u['id']}</td>
                <td>{$u['nom']}</td>
                <td>{$u['email']}</td>
                <td>
                    <a href='?edit=1&type=user&id={$u['id']}'>Modifier</a>
                    <a href='?delete=1&type=user&id={$u['id']}' onclick='return confirm(\"Supprimer ?\")'>Supprimer</a>
                </td>
              </tr>";
    }
    ?>
</table>

<h2>Liste des Produits</h2>
<table>
    <tr><th>ID</th><th>Nom</th><th>Prix (€)</th><th>Actions</th></tr>
    <?php
    $products = $pdo->query("SELECT * FROM produits");
    foreach ($products as $p) {
        echo "<tr>
                <td>{$p['id']}</td>
                <td>{$p['nom']}</td>
                <td>{$p['prix']}</td>
                <td>
                    <a href='?edit=1&type=product&id={$p['id']}'>Modifier</a>
                    <a href='?delete=1&type=product&id={$p['id']}' onclick='return confirm(\"Supprimer ?\")'>Supprimer</a>
                </td>
              </tr>";
    }
    ?>
</table>

</body>
</html>