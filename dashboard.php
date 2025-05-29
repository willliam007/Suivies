<?php
session_start();
require 'db.php';
require 'fonctions.php';
check_login();

// Récupérer les informations de l'utilisateur connecté
$user_id = $_SESSION["user_id"];
$user_type = $_SESSION["user_type"];

// Vérifier que l'utilisateur est bien un parent
if ($user_type !== "parent") {
    header("Location: login.php");
    exit;
}

// Récupérer les élèves liés au parent
$stmt = $pdo->prepare("SELECT * FROM students WHERE user_id = ?");
$stmt->execute([$user_id]);
$students = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord Parent</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light" style="background: #ffecd2;">
    <div class="container-fluid">
        <a class="navbar-brand" href="#" style="color:#ff6a00;font-weight:bold;">Suivi Scolaire</a>
        <div class="d-flex">
            <a href="logout.php" class="btn btn-perso">Déconnexion</a>
        </div>
    </div>
</nav>
<div class="container mt-5">
    <div class="card p-4">
        <h2 class="mb-4 text-center">Bienvenue sur votre tableau de bord</h2>
        <?php if (count($students) > 0): ?>
            <h4 class="mb-3" style="color:#ee0979;">Vos enfants :</h4>
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Prénom</th>
                            <th>Nom</th>
                            <th>Fiche élève</th>
                            <th>Conseils</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student["prenom"]); ?></td>
                            <td><?php echo htmlspecialchars($student["nom"]); ?></td>
                            <td>
                                <a href="fiche_eleve.php?id=<?php echo $student["id"]; ?>" class="btn btn-perso btn-sm">Voir</a>
                            </td>
                            <td>
                                <a href="conseils.php?student_id=<?php echo $student["id"]; ?>" class="btn btn-outline-warning btn-sm" style="color:#ee0979;border-color:#ee0979;">Conseils</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                Aucun élève associé à votre compte.<br>
                Veuillez contacter l'administration.
            </div>
        <?php endif; ?>
        <div class="mt-4 text-center">
            <a href="messages.php" class="btn btn-perso">Accéder à la messagerie</a>
        </div>
    </div>
</div>
</body>
</html>