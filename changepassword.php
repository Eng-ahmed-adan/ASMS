<?php
include 'config.php';
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
$user = $_SESSION['user'];
$message = "";

if (isset($_POST['change'])) {
    $oldpass = $_POST['oldpass'];
    $newpass = $_POST['newpass'];
    $confirmpass = $_POST['confirmpass'];

    $sql = "SELECT password FROM users WHERE id=" . $user['id'];
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    if (!password_verify($oldpass, $row['password'])) {
        $message = "<div class='alert alert-danger'>Old password is incorrect!</div>";
    } elseif ($newpass !== $confirmpass) {
        $message = "<div class='alert alert-warning'>New passwords do not match!</div>";
    } else {
        $hashed = password_hash($newpass, PASSWORD_DEFAULT);
        $conn->query("UPDATE users SET password='$hashed' WHERE id=" . $user['id']);
        $message = "<div class='alert alert-success'>Password changed successfully!</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Change Password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">
<div class="card p-5 shadow col-md-4">
  <h3 class="mb-4 text-center">Change Password</h3>
  <?= $message ?>
  <form method="post">
    <div class="mb-3">
      <label>Old Password</label>
      <input type="password" name="oldpass" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>New Password</label>
      <input type="password" name="newpass" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Confirm Password</label>
      <input type="password" name="confirmpass" class="form-control" required>
    </div>
    <button type="submit" name="change" class="btn btn-primary w-100">Update Password</button>
  </form>
  <a href="dashboard.php" class="btn btn-secondary w-100 mt-2">Back</a>
</div>
</body>
</html>
