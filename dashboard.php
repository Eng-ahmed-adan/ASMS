<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="card p-4 shadow">
    <h2>Welcome, <?= $user['fullname'] ?>!</h2>
    <p>Email: <?= $user['email'] ?></p>
    <p>Mobile: <?= $user['mobile'] ?></p>
    <p>Role: <?= ucfirst($user['role']) ?></p>
    <a href="logout.php" class="btn btn-danger">Logout</a>
  </div>
</div>
</body>
</html>
