<?php 
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!$email || !$password) {
        $error = "Email dan password harus diisi.";
    } else {
        $stmt = $conn->prepare("SELECT id, fullname, password FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($id, $fullname, $password_hash);
            $stmt->fetch();

            if (password_verify($password, $password_hash)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_fullname'] = $fullname;
                header('Location: index.php');
                exit;
            } else {
                $error = "Password salah.";
            }
        } else {
            $error = "Email tidak ditemukan.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">

<div class="bg-white p-10 rounded-2xl shadow-md w-full max-w-md">
    <h2 class="text-2xl font-bold mb-2">Login</h2>
    <p class="mb-6 text-gray-600">Welcome. Please enter your details.</p>

    <?php if (!empty($error)): ?>
        <div class="mb-4 text-red-600 text-sm"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <label class="block mb-2 text-sm font-medium">Email address</label>
        <input type="email" name="email" placeholder="example@mail.com"
               class="w-full px-4 py-2 mb-4 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>

        <label class="block mb-2 text-sm font-medium">Password</label>
        <input type="password" name="password"
               class="w-full px-4 py-2 mb-6 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>

        <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-md transition">
            Login
        </button>
    </form>

    <p class="mt-6 text-sm text-center text-gray-600">
        Belum punya akun?
        <a href="register.php" class="text-blue-600 hover:underline">Register</a>
    </p>
</div>

</body>
</html>
