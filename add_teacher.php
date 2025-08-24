<?php
include 'config.php';
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['role']!=='staff') header("Location: login.php");
$user=$_SESSION['user'];

$message="";

if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_teacher'])){
    $fullname = trim($_POST['fullname']);
    $email    = trim($_POST['email']);
    $mobile   = trim($_POST['mobile']);
    $gender   = $_POST['gender'];
    $dob      = $_POST['dob'];
    $subject  = trim($_POST['subject']);

    // Handle profile image upload
    $profile = null;
    if(isset($_FILES['profile']) && $_FILES['profile']['name']){
        $ext = pathinfo($_FILES['profile']['name'], PATHINFO_EXTENSION);
        $profile = uniqid('tea_').'.'.$ext;
        move_uploaded_file($_FILES['profile']['tmp_name'], 'uploads/'.$profile);
    }

    $stmt = $conn->prepare("INSERT INTO teachers (fullname,email,mobile,gender,dob,subject,profile) VALUES (?,?,?,?,?,?,?)");
    if(!$stmt){
        $message="<div class='alert alert-danger'>Prepare failed: ".$conn->error."</div>";
    } else {
        $stmt->bind_param("sssssss",$fullname,$email,$mobile,$gender,$dob,$subject,$profile);
        if($stmt->execute()){
            $message="<div class='alert alert-success shadow-sm border-0'>Teacher added successfully!</div>";
        } else {
            $message="<div class='alert alert-danger shadow-sm border-0'>Error: ".$stmt->error."</div>";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Teacher | ASMS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
:root{--brand:#0ea5e9; --brand-2:#22d3ee;}
body{min-height:100vh; background:linear-gradient(135deg,#f0f4f8,#dbeafe); display:flex; justify-content:center; padding:24px;}
.auth-card{width:100%; max-width:1100px; display:flex; border-radius:1.25rem; box-shadow:0 20px 60px rgba(2,6,23,.08); overflow:hidden; background:#fff;}
.auth-hero{background:linear-gradient(135deg,var(--brand),var(--brand-2)); color:#fff; padding:2rem; flex:1; display:flex; flex-direction:column; justify-content:space-between;}
.brand-badge{width:48px; height:48px; border-radius:16px; display:grid; place-items:center; font-weight:700; background: rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.25);}
.form-area{flex:1.2; padding:2rem;}
.form-control, .form-select{border-radius:.8rem;}
</style>
</head>
<body>
<div class="auth-card">
  <div class="auth-hero">
    <div>
      <div class="d-flex align-items-center gap-3 mb-4">
        <div class="brand-badge">AS</div>
        <div><div class="fw-bold">ASMS</div><div class="small" style="opacity:.85">Academic Student Management</div></div>
      </div>
      <h4 class="fw-bold mb-2">Add Teacher</h4>
      <p style="opacity:.9">Fill in full teacher details including subject.</p>
    </div>
    <div class="pt-4 small" style="opacity:.9">
      Logged in as <b><?= htmlspecialchars($user['fullname']) ?></b> (<?= $user['role'] ?>)
    </div>
  </div>

  <div class="form-area">
    <?= $message ?>
    <form method="post" enctype="multipart/form-data">
      <div class="mb-3"><label>Full Name</label><input type="text" name="fullname" class="form-control" required></div>
      <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
      <div class="mb-3"><label>Mobile</label><input type="text" name="mobile" class="form-control" required></div>
      <div class="mb-3"><label>Gender</label>
        <select name="gender" class="form-select">
          <option>Male</option>
          <option>Female</option>
        </select>
      </div>
      <div class="mb-3"><label>Date of Birth</label><input type="date" name="dob" class="form-control"></div>
      <div class="mb-3"><label>Subject</label><input type="text" name="subject" class="form-control"></div>
      <div class="mb-3"><label>Profile Image</label><input type="file" name="profile" class="form-control"></div>
      <button type="submit" name="add_teacher" class="btn btn-primary w-100 mb-2"><i class="bi bi-plus-circle me-2"></i>Add Teacher</button>
      <a href="teachers.php" class="btn btn-secondary w-100"><i class="bi bi-arrow-left-circle me-2"></i>Back to Teachers</a>
    </form>
  </div>
</div>
</body>
</html>
