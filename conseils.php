<?php
session_start();
require 'db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Si un élève est sélectionné, filtrer les conseils pour cet élève
$student_id = isset($_GET["student_id"]) ? $_GET["student_id"] : null;

if ($student_id) {
    $stmt = $pdo->prepare("SELECT students.prenom, conseils.message, conseils.date FROM conseils JOIN students ON conseils.student_id = students.id WHERE students.user_id = ? AND students.id = ? ORDER BY conseils.date DESC");
    $stmt->execute([$_SESSION["user_id"], $student_id]);
} else {
    $stmt = $pdo->prepare("SELECT students.prenom, conseils.message, conseils.date FROM conseils JOIN students ON conseils.student_id = students.id WHERE students.user_id = ? ORDER BY conseils.date DESC");
    $stmt->execute([$_SESSION["user_id"]]);
}
$conseils = $stmt->fetchAll();

// Récupérer la liste des enfants pour le menu de sélection
$stmt2 = $pdo->prepare("SELECT id, prenom, nom FROM students WHERE user_id = ?");
$stmt2->execute([$_SESSION["user_id"]]);
$students = $stmt2->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Conseils personnalisés</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light" style="background: #ffecd2;">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php" style="color:#ff6a00;font-weight:bold;">Suivi Scolaire</a>
        <div class="d-flex">
            <a href="logout.php" class="btn btn-perso">Déconnexion</a>
        </div>
    </div>
</nav>
<div class="container mt-5">
    <div class="card p-4">
        <h2 class="mb-4 text-center">Conseils personnalisés</h2>
        <form method="get" class="mb-4 text-center">
            <label for="student_id" class="form-label">Choisir un élève :</label>
            <select name="student_id" id="student_id" class="form-select d-inline-block w-auto" onchange="this.form.submit()">
                <option value="">Tous les élèves</option>
                <?php foreach ($students as $student): ?>
                    <option value="<?php echo $student['id']; ?>" <?php if ($student_id == $student['id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($student['prenom'] . ' ' . $student['nom']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
        <?php if (count($conseils) > 0): ?>
            <ul class="list-group">
                <?php foreach ($conseils as $conseil): ?>
                    <li class="list-group-item">
                        <strong style="color:#ee0979;"><?php echo htmlspecialchars($conseil["prenom"]); ?> :</strong>
                        <?php echo htmlspecialchars($conseil["message"]); ?>
                        <span class="text-muted float-end"><?php echo htmlspecialchars($conseil["date"]); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="alert alert-info text-center mt-4">
                Aucun conseil personnalisé pour cet élève.
            </div>
        <?php endif; ?>
        <div class="mt-4 text-center">
            <a href="dashboard.php" class="btn btn-perso">Retour au tableau de bord</a>
        </div>
    </div>
</div>
</body>
</html>