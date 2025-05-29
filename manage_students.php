<?php
session_start();
require 'db.php';
require 'fonctions.php';
if (!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== "admin") {
    header("Location: login.php");
    exit;
}

// Handle Add/Edit/Delete actions
$edit_mode = false;
$edit_student = null;
$message = "";

// Add Student
if (isset($_POST['add_student'])) {
    $prenom = trim($_POST['prenom']);
    $nom = trim($_POST['nom']);
    $user_id = $_POST['user_id'] !== '' ? intval($_POST['user_id']) : null;
    if ($prenom && $nom) {
        $stmt = $pdo->prepare("INSERT INTO students (prenom, nom, user_id) VALUES (?, ?, ?)");
        $stmt->execute([$prenom, $nom, $user_id]);
        $message = "Student added successfully.";
    } else {
        $message = "Please fill in all fields.";
    }
}

// Edit Student (show form)
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_student = $stmt->fetch();
}

// Update Student
if (isset($_POST['update_student'])) {
    $id = intval($_POST['id']);
    $prenom = trim($_POST['prenom']);
    $nom = trim($_POST['nom']);
    $user_id = $_POST['user_id'] !== '' ? intval($_POST['user_id']) : null;
    if ($prenom && $nom) {
        $stmt = $pdo->prepare("UPDATE students SET prenom = ?, nom = ?, user_id = ? WHERE id = ?");
        $stmt->execute([$prenom, $nom, $user_id, $id]);
        $message = "Student updated successfully.";
        $edit_mode = false;
    } else {
        $message = "Please fill in all fields.";
        $edit_mode = true;
        $edit_student = ['id' => $id, 'prenom' => $prenom, 'nom' => $nom, 'user_id' => $user_id];
    }
}

// Delete Student
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    $message = "Student deleted successfully.";
}

// Fetch all students
$students = $pdo->query("SELECT students.*, users.nom AS parent_nom FROM students LEFT JOIN users ON students.user_id = users.id")->fetchAll();
// Fetch all parents
$parents = $pdo->query("SELECT id, nom FROM users WHERE type = 'parent'")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Students</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light" style="background: #ffecd2;">
    <div class="container-fluid">
        <a class="navbar-brand" href="admin_dashboard.php" style="color:#ff6a00;font-weight:bold;">Suivi Scolaire - Admin</a>
        <div class="d-flex">
            <a href="logout.php" class="btn btn-perso">Logout</a>
        </div>
    </div>
</nav>
<div class="container mt-5">
    <div class="card p-4">
        <h2 class="mb-4 text-center">Manage Students</h2>
        <?php if ($message): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <!-- Add/Edit Student Form -->
        <div class="mb-4">
            <form method="post" class="row g-3">
                <?php if ($edit_mode && $edit_student): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_student['id']; ?>">
                <?php endif; ?>
                <div class="col-md-4">
                    <input type="text" name="prenom" class="form-control" placeholder="First Name" value="<?php echo $edit_mode && $edit_student ? htmlspecialchars($edit_student['prenom']) : ''; ?>">
                </div>
                <div class="col-md-4">
                    <input type="text" name="nom" class="form-control" placeholder="Last Name" value="<?php echo $edit_mode && $edit_student ? htmlspecialchars($edit_student['nom']) : ''; ?>">
                </div>
                <div class="col-md-4">
                    <select name="user_id" class="form-select">
                        <option value="">No Parent Assigned</option>
                        <?php foreach ($parents as $parent): ?>
                            <option value="<?php echo $parent['id']; ?>" <?php if ($edit_mode && $edit_student && $edit_student['user_id'] == $parent['id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($parent['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <?php if ($edit_mode && $edit_student): ?>
                        <button type="submit" name="update_student" class="btn btn-warning">Update Student</button>
                        <a href="manage_students.php" class="btn btn-secondary">Cancel</a>
                    <?php else: ?>
                        <button type="submit" name="add_student" class="btn btn-success">Add Student</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        <!-- Students Table -->
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Parent</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?php echo $student['id']; ?></td>
                        <td><?php echo htmlspecialchars($student['prenom']); ?></td>
                        <td><?php echo htmlspecialchars($student['nom']); ?></td>
                        <td><?php echo $student['parent_nom'] ? htmlspecialchars($student['parent_nom']) : '<span class="text-muted">None</span>'; ?></td>
                        <td>
                            <a href="manage_students.php?edit=<?php echo $student['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="manage_students.php?delete=<?php echo $student['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>