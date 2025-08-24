<?php
include 'config.php';
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['role']!=='staff') header("Location: login.php");
$user=$_SESSION['user'];

$msg="";

// Delete teacher
if(isset($_GET['delete'])){
    $id=intval($_GET['delete']);
    $conn->query("DELETE FROM teachers WHERE id=$id");
    $msg="<div class='alert alert-success shadow-sm border-0'>Teacher deleted successfully!</div>";
}

// Fetch teachers
$result=$conn->query("SELECT * FROM teachers ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Teachers | ASMS</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
:root{--brand:#0ea5e9; --brand-2:#22d3ee;}
body{min-height:100vh; background:linear-gradient(135deg,#f0f4f8,#dbeafe); display:flex; justify-content:center; padding:24px;}
.auth-card{width:100%; max-width:1200px; display:flex; border-radius:1.25rem; box-shadow:0 20px 60px rgba(2,6,23,.08); overflow:hidden; background:#fff;}
.auth-hero{background:linear-gradient(135deg,var(--brand),var(--brand-2)); color:#fff; padding:2rem; flex:1; display:flex; flex-direction:column; justify-content:space-between;}
.brand-badge{width:48px; height:48px; border-radius:16px; display:grid; place-items:center; font-weight:700; background: rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.25);}
.form-area{flex:2; padding:2rem;}
.table thead{background:#f1f5f9;}
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
      <h4 class="fw-bold mb-2">Teachers List</h4>
      <p style="opacity:.9">Edit or delete teachers. Search, sort, and pagination available.</p>
    </div>
    <div class="pt-4 small" style="opacity:.9">
      Logged in as <b><?= htmlspecialchars($user['fullname']) ?></b> (<?= $user['role'] ?>)
    </div>
  </div>

  <div class="form-area">
    <?= $msg ?>
    <a href="add_teacher.php" class="btn btn-success mb-3"><i class="bi bi-plus-circle me-2"></i>Add Teacher</a>
    <div class="table-responsive">
      <table id="teachersTable" class="table table-striped table-bordered">
        <thead>
          <tr>
            <th>ID</th>
            <th>Profile</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Mobile</th>
            <th>Gender</th>
            <th>DOB</th>
            <th>Subject</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php while($t=$result->fetch_assoc()): ?>
          <tr>
            <td><?= $t['id'] ?></td>
            <td><img src="uploads/<?= $t['profile'] ?: 'default.png' ?>" width="40" height="40" class="rounded-circle"></td>
            <td><?= htmlspecialchars($t['fullname']) ?></td>
            <td><?= htmlspecialchars($t['email']) ?></td>
            <td><?= htmlspecialchars($t['mobile']) ?></td>
            <td><?= $t['gender'] ?></td>
            <td><?= $t['dob'] ?></td>
            <td><?= htmlspecialchars($t['subject']) ?></td>
            <td class="text-nowrap">
              <button class="btn btn-warning btn-sm"><i class="bi bi-pencil-square"></i></button>
              <a href="?delete=<?= $t['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this teacher?')"><i class="bi bi-trash"></i></a>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function(){
    $('#teachersTable').DataTable({paging:true, searching:true, ordering:true, info:true});
});
</script>
</body>
</html>
