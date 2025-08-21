<?php
include 'config.php';
session_start();
$message = "";

if (isset($_POST['login'])) {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email' AND status='active' AND confirmed=1 LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user'] = $row;
            header("Location: dashboard.php");
            exit;
        } else {
            $message = "Invalid password!";
        }
    } else {
        $message = "User not found or not confirmed!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="card p-4 shadow">
    <h2 class="mb-3">Login</h2>
    <form method="post">
      <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
    </form>
    <p class="mt-3">Don't have an account? <a href="register.php">Sign Up</a></p>
    <p class="text-danger"><?= $message ?></p>
  </div>
</div>
</body>
</html>
