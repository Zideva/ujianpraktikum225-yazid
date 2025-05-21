<?php 
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$post_id = $_GET['id'] ?? null;
if (!$post_id) {
    header("Location: index.php");
    exit;
}

$stmt = $conn->prepare("SELECT title, category_id, created_at, content FROM posts WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $post_id, $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($title, $category_id, $created_at, $content);
$stmt->fetch();
$stmt->close();

// Ambil nama kategori
$cat_stmt = $conn->prepare("SELECT name FROM categories WHERE id = ?");
$cat_stmt->bind_param("i", $category_id);
$cat_stmt->execute();
$cat_stmt->bind_result($category_name);
$cat_stmt->fetch();
$cat_stmt->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>View Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color: #f7f9fb;">

<!-- Navbar -->
<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="index.php">Blog App</a>
        <div>
            <a href="dashboard.php" class="btn btn-outline-light me-2">Dashboard</a>
            <a href="logout.php" class="btn btn-outline-light">Logout</a>
        </div>
    </div>
</nav>

<!-- Konten Post -->
<div class="container mt-5">
    <div class="bg-white p-5 rounded shadow-sm" style="max-width: 800px; margin: auto;">
        <h2 class="fw-bold mb-4"><?= htmlspecialchars($title) ?></h2>
        <p class="mb-2"><strong>Kategori:</strong> <?= htmlspecialchars($category_name) ?></p>
        <p class="mb-4"><strong>Ditulis pada:</strong> <?= htmlspecialchars($created_at) ?></p>
        <div class="border-top pt-3">
            <p><?= nl2br(htmlspecialchars($content)) ?></p>
        </div>
        <a href="dashboard.php" class="btn btn-secondary mt-4">Kembali ke Dashboard</a>
    </div>
</div>

</body>
</html>
