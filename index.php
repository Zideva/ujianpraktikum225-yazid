<?php
session_start();
require 'db.php';

$sql = "SELECT posts.id, posts.title, posts.created_at, Fullname AS author 
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        ORDER BY posts.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Blog App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color: #f8f9fa;">

<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="index.php">Blog App</a>
        <div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php" class="btn btn-outline-light me-2">Dashboard</a>
                <a href="logout.php" class="btn btn-outline-light">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline-light me-2">Login</a>
                <a href="register.php" class="btn btn-outline-light">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container my-5">
    <!-- Header + Tombol Add Post jika login -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Recent Post</h2>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="create_post.php" class="btn btn-primary">Add Post</a>
        <?php endif; ?>
    </div>

    <!-- Kartu Postingan -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title fw-bold"><?= htmlspecialchars($row['title']) ?></h5>
                        <p class="card-text text-muted mb-1"><?= htmlspecialchars($row['author']) ?></p>
                        <p class="card-text text-muted" style="font-size: 0.9em;"><?= $row['created_at'] ?></p>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <a href="post.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Read More</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

</body>
</html>
