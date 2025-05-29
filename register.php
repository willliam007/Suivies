<?php
require 'db.php';
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST["nom"];
    $email = $_POST["email"];
    $mot_de_passe = password_hash($_POST["mot_de_passe"], PASSWORD_DEFAULT);
    $type = "parent"; // Forcer le type à parent

    $stmt = $pdo->prepare("INSERT INTO users (nom, email, mot_de_passe, type) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$nom, $email, $mot_de_passe, $type])) {
        $message = "Inscription réussie !";
    } else {
        $message = "Erreur lors de l'inscription.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - Suivi Scolaire</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container d-flex justify-content-center align-items-center" style="min-height:100vh;">
    <div class="card p-4" style="width:400px;">
        <h2 class="text-center mb-4">Inscription</h2>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Nom</label>  
                <input type="text" name="nom" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Mot de passe</label>
                <input type="password" name="mot_de_passe" class="form-control" required>
            </div>
            <div class="mb-3">
               
                </select>
            </div>
            <button type="submit" class="btn btn-perso w-100">S'inscrire</button>
        </form>
        <p class="mt-3 text-center text-success"><?php echo $message; ?></p>
        <p class="mt-2 text-center"><a href="login.php" style="color:#ee0979;">Déjà inscrit ? Se connecter</a></p>
    </div>
</div>
</body>
</html>