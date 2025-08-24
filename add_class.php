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
    $mother   = $_POST['mother'];
    $mother_mobile = $_POST['mother_mobile'];
    $father_mobile = $_POST['father_mobile'];

    $guardian_type = $_POST['guardian_type'];
    $guardian = '';
    $guardian_mobile = '';

    if($guardian_type == 'Father') {
        $guardian = 'Father';
        $guardian_mobile = $father_mobile;
    } elseif($guardian_type == 'Mother') {
        $guardian = 'Mother';
        $guardian_mobile = $mother_mobile;
    } else {
        $guardian = trim($_POST['guardian_name'] ?? '');
        $guardian_mobile = trim($_POST['guardian_mobile'] ?? '');
    }

    $class_id = $_POST['class_id'];

    // Handle profile image upload
    $profile = null;
    if(isset($_FILES['profile']) && $_FILES['profile']['name']){
        $ext = pathinfo($_FILES['profile']['name'], PATHINFO_EXTENSION);
        $profile = uniqid('stu_').'.'.$ext;
        move_uploaded_file($_FILES['profile']['tmp_name'], 'uploads/'.$profile);
    }

    // Prepare statement
    $stmt = $conn->prepare("INSERT INTO students (fullname,email,mobile,gender,dob,mother,mother_mobile,father_mobile,guardian,guardian_mobile,class_id,profile) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
    if(!$stmt){
        $message="<div class='alert alert-danger'>Prepare failed: ".$conn->error."</div>";
    } else {
        $stmt->bind_param("ssssssssssis",$fullname,$email,$mobile,$gender,$dob,$mother,$mother_mobile,$father_mobile,$guardian,$guardian_mobile,$class_id,$profile);
        if($stmt->execute()){
            $message="<div class='alert alert-success shadow-sm border-0'>Student added successfully!</div>";
        } else {
            $message="<div class='alert alert-danger shadow-sm border-0'>Error: ".$stmt->error."</div>";
        }
        $stmt->close();
    }
}

// Fetch classes
$classes=$conn->query("SELECT id,name,academic_year FROM classes ORDER BY academic_year DESC,name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Student | ASMS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<style>
#guardian_other_fields{display:none;}
</style>
</head>
<body>
<div class="container mt-5">
    <?= $message ?>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3"><label>Full Name</label><input type="text" name="fullname" class="form-control" required></div>
        <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
        <div class="mb-3"><label>Mobile</label><input type="text" name="mobile" class="form-control" required></div>
        <div class="mb-3"><label>Gender</label>
            <select name="gender" class="form-select"><option>Male</option><option>Female</option></select>
        </div>
        <div class="mb-3"><label>Date of Birth</label><input type="date" name="dob" class="form-control"></div>
        <div class="mb-3"><label>Mother Name</label><input type="text" name="mother" class="form-control"></div>
        <div class="mb-3"><label>Mother Mobile</label><input type="text" id="mother_mobile" name="mother_mobile" class="form-control"></div>
        <div class="mb-3"><label>Father Mobile</label><input type="text" id="father_mobile" name="father_mobile" class="form-control"></div>

        <div class="mb-3">
            <label>Guardian</label><br>
            <div class="form-check form-check-inline"><input type="radio" class="form-check-input guardian_radio" name="guardian_type" value="Father" checked>Father</div>
            <div class="form-check form-check-inline"><input type="radio" class="form-check-input guardian_radio" name="guardian_type" value="Mother">Mother</div>
            <div class="form-check form-check-inline"><input type="radio" class="form-check-input guardian_radio" name="guardian_type" value="Uncle">Uncle</div>
            <div class="form-check form-check-inline"><input type="radio" class="form-check-input guardian_radio" name="guardian_type" value="Grandfather">Grandfather</div>
            <div class="form-check form-check-inline"><input type="radio" class="form-check-input guardian_radio" name="guardian_type" value="Grandmother">Grandmother</div>
            <div class="form-check form-check-inline"><input type="radio" class="form-check-input guardian_radio" name="guardian_type" value="Other">Other</div>
        </div>

        <div id="guardian_other_fields">
            <div class="mb-3"><label>Guardian Name</label><input type="text" name="guardian_name" class="form-control"></div>
            <div class="mb-3"><label>Guardian Mobile</label><input type="text" name="guardian_mobile" class="form-control"></div>
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

        <div class="mb-3"><label>Profile Image</label><input type="file" name="profile" class="form-control"></div>
        <button type="submit" name="add_student" class="btn btn-primary">Add Student</button>
    </form>
</div>

<script>
function updateGuardianFields(){
    let val = $('input[name="guardian_type"]:checked').val();
    if(val=="Father"){
        $('input[name="guardian_name"]').val('Father').prop('disabled',true);
        $('input[name="guardian_mobile"]').val($('#father_mobile').val()).prop('disabled',true);
        $('#guardian_other_fields').hide();
    } else if(val=="Mother"){
        $('input[name="guardian_name"]').val('Mother').prop('disabled',true);
        $('input[name="guardian_mobile"]').val($('#mother_mobile').val()).prop('disabled',true);
        $('#guardian_other_fields').hide();
    } else {
        $('input[name="guardian_name"]').val('').prop('disabled',false);
        $('input[name="guardian_mobile"]').val('').prop('disabled',false);
        $('#guardian_other_fields').show();
    }
}

$(document).ready(function(){
    updateGuardianFields();
    $('.guardian_radio').change(updateGuardianFields);
    $('#father_mobile,#mother_mobile').on('input',updateGuardianFields);
});
</script>
</body>
</html>
