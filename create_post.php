<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$sqlCreate = "CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if (!$conn->query($sqlCreate)) {
    die("Gagal membuat tabel categories: " . $conn->error);
}

$defaultCategories = ['HTML', 'CSS', 'JavaScript', 'Java', 'Python', 'PHP', 'C++', 'C#', 'Ruby', 'Go'];
$stmtInsert = $conn->prepare("INSERT IGNORE INTO categories (name) VALUES (?)");
foreach ($defaultCategories as $catName) {
    $stmtInsert->bind_param('s', $catName);
    $stmtInsert->execute();
}
$stmtInsert->close();

$categories = [];
$result = $conn->query("SELECT id, name FROM categories ORDER BY name");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category = intval($_POST['category']);
    $user_id = $_SESSION['user_id'];

    if ($title && $category && $content) {
        $valid_category = false;
        foreach ($categories as $cat) {
            if ($cat['id'] == $category) {
                $valid_category = true;
                break;
            }
        }

        if ($valid_category) {
            $stmt = $conn->prepare("INSERT INTO posts (user_id, title, content, category_id, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param('issi', $user_id, $title, $content, $category);
            $stmt->execute();
            $stmt->close();
            header("Location: index.php");
            exit;
        } else {
            $error = "Kategori tidak valid.";
        }
    } else {
        $error = "Judul, konten, dan kategori harus diisi.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buat Post Baru</title>
    <style>
        body {
            font-family: sans-serif;
            background: #f5f6f8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
            width: 500px;
        }
        h2 {
            text-align: center;
        }
        label {
            display: block;
            margin-top: 1rem;
            font-weight: bold;
        }
        input[type="text"], select, textarea {
            width: 100%;
            padding: 0.6rem;
            margin-top: 0.3rem;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        textarea {
            height: 150px;
            resize: vertical;
        }
        button {
            margin-top: 1rem;
            padding: 0.6rem 1.2rem;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        a {
            display: inline-block;
            margin-top: 1rem;
            color: purple;
            text-decoration: underline;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>Buat Post Baru</h2>

    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

    <form method="post">
        <label for="title">Judul:</label>
        <input type="text" id="title" name="title" required>

        <label for="category">Kategori:</label>
        <select name="category" id="category" required>
            <option value="">-- Pilih kategori --</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat['id']) ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="content">Konten:</label>
        <textarea id="content" name="content" required></textarea>

        <button type="submit">Simpan</button>
    </form>

    <a href="index.php">&larr; Kembali ke Dashboard</a>
</div>

</body>
</html>
