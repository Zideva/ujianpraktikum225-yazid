<?php
require 'db.php';

if (!isset($_GET['id'])) {
    die("Post tidak ditemukan.");
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT posts.title, posts.content, posts.created_at, users.Fullname AS author
                        FROM posts
                        JOIN users ON posts.user_id = users.id
                        WHERE posts.id = ?");
if (!$stmt) {
    die("Prepare statement gagal: " . $conn->error);
}
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

if (!$post) {
    die("Post tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($post['title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

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

<!-- Detail Konten -->
<div class="container mt-4">
    <a href="index.php" class="btn btn-secondary mb-3">← Kembali</a>
    <div class="card">
        <div class="card-body">
            <h2 class="card-title"><?= htmlspecialchars($post['title']) ?></h2>
            <p class="card-text text-muted mb-1"><?= htmlspecialchars($post['author']) ?></p>
            <p class="text-muted"><?= htmlspecialchars($post['created_at']) ?></p>
            <hr>
            <p class="card-text"><?= nl2br(htmlspecialchars($post['content'])) ?></p>
        </div>
    </div>
</div>

</body>
</html>
