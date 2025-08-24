<?php
include 'config.php';
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['role']!=='staff') header("Location: login.php");
$user=$_SESSION['user'];

$message="";

if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_student'])){
    $fullname = trim($_POST['fullname']);
    $email    = trim($_POST['email']);
    $mobile   = trim($_POST['mobile']);
    $gender   = $_POST['gender'];
    $dob      = $_POST['dob'];
    $father   = $_POST['father'];
    $father_mobile = $_POST['father_mobile'];
    $mother   = $_POST['mother'];
    $mother_mobile = $_POST['mother_mobile'];
    $guardian = $_POST['guardian'];
    $guardian_mobile = $_POST['guardian_mobile'];
    $class_id = $_POST['class_id'];

    // Handle profile image upload
    $profile = null;
    if(isset($_FILES['profile']) && $_FILES['profile']['name']){
        $ext = pathinfo($_FILES['profile']['name'], PATHINFO_EXTENSION);
        $profile = uniqid('stu_').'.'.$ext;
        move_uploaded_file($_FILES['profile']['tmp_name'], 'uploads/'.$profile);
    }

    // Insert student
    $stmt=$conn->prepare("INSERT INTO students (fullname,email,mobile,gender,dob,father,father_mobile,mother,mother_mobile,guardian,guardian_mobile,class_id,profile) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("sssssssssssis",$fullname,$email,$mobile,$gender,$dob,$father,$father_mobile,$mother,$mother_mobile,$guardian,$guardian_mobile,$class_id,$profile);
    if($stmt->execute()){
        $message="<div class='alert alert-success shadow-sm border-0'>Student added successfully!</div>";
    }else{
        $message="<div class='alert alert-danger shadow-sm border-0'>Error: ".$conn->error."</div>";
    }
    $stmt->close();
}

// Fetch classes for dropdown
$classes=$conn->query("SELECT id,name,academic_year FROM classes ORDER BY academic_year DESC,name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Student | ASMS</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
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
  <!-- Left ASMS info panel -->
  <div class="auth-hero">
    <div>
      <div class="d-flex align-items-center gap-3 mb-4">
        <div class="brand-badge">AS</div>
        <div>
          <div class="fw-bold">ASMS</div>
          <div class="small" style="opacity:.85">Academic Student Management</div>
        </div>
      </div>
      <h4 class="fw-bold mb-2">Add New Student</h4>
      <p style="opacity:.9">Fill in the full student details including family contacts and assign class.</p>
    </div>
    <div class="pt-4 small" style="opacity:.9">
      Logged in as <b><?= htmlspecialchars($user['fullname']) ?></b> (<?= $user['role'] ?>)
    </div>
  </div>

  <!-- Right form area -->
  <div class="form-area">
    <?= $message ?>
    <form method="post" enctype="multipart/form-data">
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
        <label>Gender</label>
        <select name="gender" class="form-select">
          <option value="Male">Male</option>
          <option value="Female">Female</option>
        </select>
      </div>
      <div class="mb-3">
        <label>Date of Birth</label>
        <input type="date" name="dob" class="form-control">
      </div>
      <div class="mb-3">
        <label>Father Name</label>
        <input type="text" name="father" class="form-control">
      </div>
      <div class="mb-3">
        <label>Father Mobile</label>
        <input type="text" name="father_mobile" class="form-control">
      </div>
      <div class="mb-3">
        <label>Mother Name</label>
        <input type="text" name="mother" class="form-control">
      </div>
      <div class="mb-3">
        <label>Mother Mobile</label>
        <input type="text" name="mother_mobile" class="form-control">
      </div>
      <div class="mb-3">
        <label>Guardian Name</label>
        <input type="text" name="guardian" class="form-control">
      </div>
      <div class="mb-3">
        <label>Guardian Mobile</label>
        <input type="text" name="guardian_mobile" class="form-control">
      </div>
      <div class="mb-3">
        <label>Assign Class</label>
        <select name="class_id" class="form-select" required>
          <option value="">--Select Class--</option>
          <?php while($c=$classes->fetch_assoc()): ?>
          <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name'].' ('.$c['academic_year'].')') ?></option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="mb-3">
        <label>Profile Image</label>
        <input type="file" name="profile" class="form-control">
      </div>
      <button type="submit" name="add_student" class="btn btn-primary w-100 mb-2"><i class="bi bi-plus-circle me-2"></i>Add Student</button>
      <a href="students.php" class="btn btn-secondary w-100"><i class="bi bi-arrow-left-circle me-2"></i>Back to Students</a>
    </form>
  </div>
</div>
</body>
</html>
