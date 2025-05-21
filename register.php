<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (!$fullname || !$email || !$password || !$confirm_password) {
        $error = "Semua kolom wajib diisi.";
    } elseif ($password !== $confirm_password) {
        $error = "Password dan konfirmasi tidak cocok.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Email sudah terdaftar.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $insert = $conn->prepare("INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
            $insert->bind_param('sss', $fullname, $email, $hash);

            if ($insert->execute()) {
                header('Location: login.php');
                exit;
            } else {
                $error = "Terjadi kesalahan saat registrasi.";
            }
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">

<div class="bg-white p-10 rounded-2xl shadow-md w-full max-w-md">
    <h2 class="text-2xl font-bold mb-2">Register</h2>
    <p class="mb-6 text-gray-600">Welcome. Please enter your details.</p>

    <?php if (!empty($error)): ?>
        <div class="mb-4 text-red-600 text-sm"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <label class="block mb-2 text-sm font-medium">Fullname</label>
        <input type="text" name="fullname" placeholder="Enter your name here..."
               class="w-full px-4 py-2 mb-4 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>

        <label class="block mb-2 text-sm font-medium">Email address</label>
        <input type="email" name="email" placeholder="example@mail.com"
               class="w-full px-4 py-2 mb-4 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>

        <label class="block mb-2 text-sm font-medium">Password</label>
        <input type="password" name="password"
               class="w-full px-4 py-2 mb-4 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>

        <label class="block mb-2 text-sm font-medium">Confirm Password</label>
        <input type="password" name="confirm_password"
               class="w-full px-4 py-2 mb-6 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>

        <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-md transition">
            Register
        </button>
    </form>

    <p class="mt-6 text-sm text-center text-gray-600">
        Have an account?
        <a href="login.php" class="text-blue-600 hover:underline">Login</a>
    </p>
</div>

</body>
</html>
