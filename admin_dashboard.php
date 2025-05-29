<?php
session_start();
require 'db.php';
require 'fonctions.php';
if (!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== "admin") {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light" style="background: #ffecd2;">
    <div class="container-fluid">
        <a class="navbar-brand" href="#" style="color:#ff6a00;font-weight:bold;">Suivi Scolaire - Admin</a>
        <div class="d-flex">
            <a href="logout.php" class="btn btn-perso">Logout</a>
        </div>
    </div>
</nav>
<div class="container mt-5">
    <div class="card p-4">
        <h2 class="mb-4 text-center">Admin Dashboard</h2>
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <a href="#" class="btn btn-perso w-100 py-3">Manage Users</a>
            </div>
            <div class="col-md-6 col-lg-4">
                <a href="#" class="btn btn-perso w-100 py-3">Manage Students</a>
            </div>
            <div class="col-md-6 col-lg-4">
                <a href="#" class="btn btn-perso w-100 py-3">Manage Notes</a>
            </div>
            <div class="col-md-6 col-lg-4">
                <a href="#" class="btn btn-perso w-100 py-3">Manage Advice</a>
            </div>
            <div class="col-md-6 col-lg-4">
                <a href="#" class="btn btn-perso w-100 py-3">View Messages</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>