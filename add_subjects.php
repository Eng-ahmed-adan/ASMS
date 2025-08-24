<?php
include 'config.php';
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['role']!=='staff') header("Location: login.php");
$user=$_SESSION['user'];

$message="";

if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_subject'])){
    $name = trim($_POST['name']);
    $class_id = $_POST['class_id'];
    $teacher_ids = $_POST['teacher_ids'] ?? [];
    $academic_year = trim($_POST['academic_year']);
    $description = trim($_POST['description']);

    // Prevent duplicate subject for same class and academic year
    $check = $conn->prepare("SELECT id FROM subjects WHERE name=? AND class_id=? AND academic_year=? LIMIT 1");
    $check->bind_param("sis",$name,$class_id,$academic_year);
    $check->execute();
    $check->store_result();
    if($check->num_rows>0){
        $message="<div class='alert alert-warning'>Subject already exists for this class and academic year!</div>";
    } else {
        // Insert subject
        $stmt = $conn->prepare("INSERT INTO subjects (name,class_id,academic_year,description) VALUES (?,?,?,?)");
        $stmt->bind_param("siss",$name,$class_id,$academic_year,$description);
        if($stmt->execute()){
            $subject_id = $stmt->insert_id;
            // Insert into pivot table
            if(count($teacher_ids)>0){
                $stmt2 = $conn->prepare("INSERT INTO subject_teachers (subject_id,teacher_id) VALUES (?,?)");
                foreach($teacher_ids as $tid){
                    $stmt2->bind_param("ii",$subject_id,$tid);
                    $stmt2->execute();
                }
                $stmt2->close();
            }
            $message="<div class='alert alert-success'>Subject added successfully!</div>";
        } else {
            $message="<div class='alert alert-danger'>Error: ".$stmt->error."</div>";
        }
        $stmt->close();
    }
    $check->close();
}

// Fetch classes and teachers
$classes=$conn->query("SELECT id,name,academic_year FROM classes ORDER BY academic_year DESC,name ASC");
$teachers=$conn->query("SELECT id,fullname FROM teachers ORDER BY fullname ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Subject | ASMS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
body{min-height:100vh; background:linear-gradient(135deg,#f0f4f8,#dbeafe); display:flex; justify-content:center; padding:24px;}
.auth-card{width:100%; max-width:900px; display:flex; border-radius:1.25rem; box-shadow:0 20px 60px rgba(2,6,23,.08); overflow:hidden; background:#fff;}
.auth-hero{background:linear-gradient(135deg,#0ea5e9,#22d3ee); color:#fff; padding:2rem; flex:1; display:flex; flex-direction:column; justify-content:space-between;}
.brand-badge{width:48px; height:48px; border-radius:16px; display:grid; place-items:center; font-weight:700; background: rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.25);}
.form-area{flex:2; padding:2rem;}
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
      <h4 class="fw-bold mb-2">Add Subject</h4>
      <p style="opacity:.9">Fill subject details, assign one or more teachers.</p>
    </div>
    <div class="pt-4 small" style="opacity:.9">
      Logged in as <b><?= htmlspecialchars($user['fullname']) ?></b> (<?= $user['role'] ?>)
    </div>
  </div>

  <div class="form-area">
    <?= $message ?>
    <form method="post">
      <div class="mb-3"><label>Subject Name</label><input type="text" name="name" class="form-control" required></div>
      <div class="mb-3"><label>Class</label>
        <select name="class_id" class="form-select" required>
          <option value="">--Select Class--</option>
          <?php while($c=$classes->fetch_assoc()): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name'].' ('.$c['academic_year'].')') ?></option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="mb-3"><label>Teacher(s)</label>
        <select name="teacher_ids[]" class="form-select" multiple required>
          <?php while($t=$teachers->fetch_assoc()): ?>
            <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['fullname']) ?></option>
          <?php endwhile; ?>
        </select>
        <small class="text-muted">Hold Ctrl (Windows) or Cmd (Mac) to select multiple teachers</small>
      </div>
      <div class="mb-3"><label>Academic Year</label><input type="text" name="academic_year" class="form-control" required></div>
      <div class="mb-3"><label>Description</label><textarea name="description" class="form-control" rows="3"></textarea></div>
      <button type="submit" name="add_subject" class="btn btn-info w-100 mb-2"><i class="bi bi-journal-bookmark me-2"></i>Add Subject</button>
      <a href="subjects.php" class="btn btn-secondary w-100"><i class="bi bi-arrow-left-circle me-2"></i>Back to Subjects</a>
    </form>
  </div>
</div>
</body>
</html>
