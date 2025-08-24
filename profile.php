<?php
include 'config.php';
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
$user = $_SESSION['user'];
$message = "";

if (isset($_POST['update'])) {
    $fullname = $_POST['fullname'];
    $email    = $_POST['email'];
    $mobile   = $_POST['mobile'];

    // Handle image upload
    $profile = $user['profile'];
    if (!empty($_FILES['profile']['name'])) {
        $targetDir = "uploads/";
        $filename = time() . "_" . basename($_FILES["profile"]["name"]);
        $targetFile = $targetDir . $filename;
        if (move_uploaded_file($_FILES["profile"]["tmp_name"], $targetFile)) {
            $profile = $filename;
        }
    }

    $sql = "UPDATE users SET fullname='$fullname', email='$email', mobile='$mobile', profile='$profile' WHERE id=" . $user['id'];
    if ($conn->query($sql)) {
        $_SESSION['user'] = array_merge($user, [
            "fullname" => $fullname,
            "email" => $email,
            "mobile" => $mobile,
            "profile" => $profile
        ]);
        $user = $_SESSION['user'];
        $message = "<div class='alert alert-success'>Profile updated!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">
<div class="card p-5 shadow col-md-5">
  <h3 class="mb-4 text-center">My Profile</h3>
  <?= $message ?>
  <form method="post" enctype="multipart/form-data">
 <?php
// Pick profile image or fallback to default
$profileImage = (!empty($user['profile']) && file_exists("uploads/" . $user['profile']))
    ? "uploads/" . $user['profile']
    : "uploads/default.png";
?>
<div class="text-center mb-3">
  <img src="<?= $profileImage ?>" alt="Profile" class="rounded-circle border shadow" width="120" height="120">
</div>

    <div class="mb-3">
      <label>Full Name</label>
      <input type="text" name="fullname" class="form-control" value="<?= $user['fullname'] ?>" required>
    </div>
    <div class="mb-3">
      <label>Email</label>
      <input type="email" name="email" class="form-control" value="<?= $user['email'] ?>" required>
    </div>
    <div class="mb-3">
      <label>Mobile</label>
      <input type="text" name="mobile" class="form-control" value="<?= $user['mobile'] ?>" required>
    </div>
    <div class="mb-3">
      <label>Profile Image</label>
      <input type="file" name="profile" class="form-control">
    </div>
    <button type="submit" name="update" class="btn btn-success w-100">Update Profile</button>
  </form>
  <a href="dashboard.php" class="btn btn-secondary w-100 mt-2">Back</a>
</div>
</body>
</html>
