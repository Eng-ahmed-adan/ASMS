<?php
include 'config.php';
$message = "";

if (isset($_POST['register'])) {
    $fullname = $_POST['fullname'];
    $email    = $_POST['email'];
    $mobile   = $_POST['mobile'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = $_POST['role'];

    $sql = "INSERT INTO users (fullname, email, mobile, password, role, status, confirmed) 
            VALUES ('$fullname','$email','$mobile','$password','$role','active',1)";

    if ($conn->query($sql) === TRUE) {
        $message = "Registration successful! You can now <a href='login.php'>Login</a>";
    } else {
        $message = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="card p-4 shadow">
    <h2 class="mb-3">Register</h2>
    <form method="post">
      <div class="mb-3">
        <label>Full Name</label>
        <input type="text" name="fullname" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Mobile</label>
        <input type="text" name="mobile" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Role</label>
        <select name="role" class="form-select">
          <option value="student">Student</option>
          <option value="teacher">Teacher</option>
          <option value="staff">Staff</option>
        </select>
      </div>
      <button type="submit" name="register" class="btn btn-primary w-100">Register</button>
    </form>
    <p class="mt-3">Already have an account? <a href="index.php">Login</a></p>
    <p class="text-success"><?= $message ?></p>
  </div>
</div>
</body>
</html>
