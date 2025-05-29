<?php
session_start();
require 'db.php';
require 'fonctions.php';
check_login();

if (!isset($_GET["id"])) {
    header("Location: dashboard.php");
    exit;
}

$student_id = $_GET["id"];
$user_id = $_SESSION["user_id"];
$user_type = $_SESSION["user_type"];

// Vérifier que l'élève appartient bien au parent connecté
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ? AND user_id = ?");
$stmt->execute([$student_id, $user_id]);
$student = $stmt->fetch();

if (!$student) {
    header("Location: dashboard.php");
    exit;
}

// Récupérer les notes de l'élève
$stmt = $pdo->prepare("SELECT * FROM notes WHERE student_id = ? ORDER BY date ASC");
$stmt->execute([$student_id]);
$notes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche Élève - <?php echo htmlspecialchars($student["prenom"] . " " . $student["nom"]); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        <h2 class="mb-4 text-center">Fiche de <?php echo htmlspecialchars($student["prenom"] . " " . $student["nom"]); ?></h2>
        <h5 class="mb-3" style="color:#ee0979;">Notes :</h5>
        <?php if (count($notes) > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Matière</th>
                            <th>Note</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($notes as $note): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($note["matiere"]); ?></td>
                            <td><?php echo htmlspecialchars($note["note"]); ?></td>
                            <td><?php echo htmlspecialchars($note["date"]); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="my-4">
                <canvas id="progressionChart"></canvas>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                Aucune note enregistrée pour cet élève.
            </div>
        <?php endif; ?>
        <div class="mt-4 text-center">
            <a href="dashboard.php" class="btn btn-perso">Retour au tableau de bord</a>
        </div>
    </div>
</div>
<?php if (count($notes) > 0): ?>
<script>
    // Préparer les données pour Chart.js
    const labels = <?php echo json_encode(array_map(function($n){return $n["matiere"] . " (" . $n["date"] . ")";}, $notes)); ?>;
    const data = <?php echo json_encode(array_map(function($n){return floatval($n["note"]);}, $notes)); ?>;
    const ctx = document.getElementById('progressionChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Progression des notes',
                data: data,
                fill: false,
                borderColor: '#ee0979',
                backgroundColor: '#ff6a00',
                tension: 0.2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, max: 20 }
            }
        }
    });
</script>
<?php endif; ?>
</body>
</html>