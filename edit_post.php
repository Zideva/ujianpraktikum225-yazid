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

// Ambil data post
$stmt = $conn->prepare("SELECT title, category_id, content FROM posts WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $post_id, $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($title, $category, $content);
$stmt->fetch();
$stmt->close();

// Ambil daftar kategori
$categories = [];
$cat_stmt = $conn->prepare("SELECT id, name FROM categories");
$cat_stmt->execute();
$cat_result = $cat_stmt->get_result();
while ($row = $cat_result->fetch_assoc()) {
    $categories[] = $row;
}
$cat_stmt->close();

// Proses update post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_title = trim($_POST['title']);
    $new_category = intval($_POST['category']);
    $new_content = trim($_POST['content']);

    if ($new_title && $new_category && $new_content) {
        $stmt = $conn->prepare("UPDATE posts SET title = ?, category_id = ?, content = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sisii", $new_title, $new_category, $new_content, $post_id, $_SESSION['user_id']);
        $stmt->execute();
        $stmt->close();
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Semua field harus diisi.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Post</title>
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

<!-- Form Edit Post -->
<div class="container d-flex justify-content-center align-items-center" style="min-height: 90vh;">
    <div class="bg-white p-5 rounded shadow" style="width: 100%; max-width: 600px;">
        <h2 class="text-center mb-4 fw-bold">Edit Post</h2>

        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label for="title" class="form-label fw-semibold">Judul:</label>
                <input type="text" name="title" id="title" class="form-control" required value="<?= htmlspecialchars($title) ?>">
            </div>

            <div class="mb-3">
                <label for="category" class="form-label fw-semibold">Kategori:</label>
                <select name="category" id="category" class="form-select" required>
                    <option value="">-- Pilih kategori --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $category ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="content" class="form-label fw-semibold">Konten:</label>
                <textarea name="content" id="content" rows="6" class="form-control" required><?= htmlspecialchars($content) ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="dashboard.php" class="d-block mt-3 text-decoration-none text-primary">&larr; Kembali ke Dashboard</a>
        </form>
    </div>
</div>

</body>
</html>
