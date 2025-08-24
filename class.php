<?php
include 'config.php';
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['role']!=='staff') header("Location: login.php");
$user=$_SESSION['user'];

$msg="";

// Handle Delete
if(isset($_GET['delete'])){
    $id=intval($_GET['delete']);
    $conn->query("DELETE FROM classes WHERE id=$id");
    $msg="<div class='alert alert-success shadow-sm border-0'>Class deleted successfully!</div>";
}

// Handle Update
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update_class'])){
    $id = $_POST['id'];
    $name = trim($_POST['name']);
    $year = trim($_POST['academic_year']);
    $start= $_POST['start_date'];
    $end  = $_POST['end_date'];

    // Duplicate check same year
    $stmt = $conn->prepare("SELECT id FROM classes WHERE name=? AND academic_year=? AND id<>? LIMIT 1");
    $stmt->bind_param("ssi",$name,$year,$id);
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows>0){
        $msg="<div class='alert alert-warning shadow-sm border-0'>Class already exists for this Academic Year!</div>";
    } else {
        $stmt_update=$conn->prepare("UPDATE classes SET name=?, academic_year=?, start_date=?, end_date=? WHERE id=?");
        $stmt_update->bind_param("ssssi",$name,$year,$start,$end,$id);
        $stmt_update->execute();
        $msg="<div class='alert alert-success shadow-sm border-0'>Class updated successfully!</div>";
        $stmt_update->close();
    }
    $stmt->close();
}

// Fetch classes
$result=$conn->query("SELECT * FROM classes ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Classes | ASMS</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
:root{--brand:#0ea5e9; --brand-2:#22d3ee;}
body{min-height:100vh; background: linear-gradient(135deg,#f0f4f8,#dbeafe); display:flex; justify-content:center; padding:24px;}
.auth-card{width:100%; max-width:1100px; display:flex; border-radius:1.25rem; box-shadow:0 20px 60px rgba(2,6,23,.08); overflow:hidden; background:#fff;}
.auth-hero{background:linear-gradient(135deg,var(--brand),var(--brand-2)); color:#fff; padding:2rem; flex:1; display:flex; flex-direction:column; justify-content:space-between;}
.brand-badge{width:48px; height:48px; border-radius:16px; display:grid; place-items:center; font-weight:700; background: rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.25);}
.form-area{flex:1.2; padding:2rem;}
.table thead{background:#f1f5f9;}
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
      <h4 class="fw-bold mb-2">Class Management</h4>
      <p style="opacity:.9">Edit or delete classes. Search, sort, and pagination available in table.</p>
    </div>
    <div class="pt-4 small" style="opacity:.9">
      Logged in as <b><?= htmlspecialchars($user['fullname']) ?></b> (<?= $user['role'] ?>)
    </div>
  </div>

  <!-- Right Table Area -->
  <div class="form-area">
    <?= $msg ?>
    <a href="add_class.php" class="btn btn-success mb-3"><i class="bi bi-plus-circle me-2"></i>Add New Class</a>
    <div class="table-responsive">
      <table id="classTable" class="table table-striped table-bordered">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Academic Year</th>
            <th>Start</th>
            <th>End</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php while($row=$result->fetch_assoc()): ?>
          <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['academic_year']) ?></td>
            <td><?= $row['start_date'] ?></td>
            <td><?= $row['end_date'] ?></td>
            <td class="text-nowrap">
              <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>"><i class="bi bi-pencil-square"></i></button>
              <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this class?')"><i class="bi bi-trash"></i></a>
            </td>
          </tr>

          <!-- Edit Modal -->
          <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="row g-0">
                  <!-- ASMS panel inside modal -->
                  <div class="col-12 col-md-4 auth-hero p-3 d-flex flex-column justify-content-between">
                    <div>
                      <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="brand-badge">AS</div>
                        <div class="fw-bold">ASMS</div>
                      </div>
                      <p>Edit class #<?= $row['id'] ?></p>
                    </div>
                    <div class="small" style="opacity:.9">Logged in as <b><?= htmlspecialchars($user['fullname']) ?></b></div>
                  </div>
                  <!-- Form area -->
                  <div class="col-12 col-md-8 p-3">
                    <form method="post">
                      <input type="hidden" name="id" value="<?= $row['id'] ?>">
                      <div class="mb-2">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($row['name']) ?>" required>
                      </div>
                      <div class="mb-2">
                        <label>Academic Year</label>
                        <input type="text" name="academic_year" class="form-control" value="<?= htmlspecialchars($row['academic_year']) ?>" required>
                      </div>
                      <div class="row g-2 mb-2">
                        <div class="col">
                          <label>Start Date</label>
                          <input type="date" name="start_date" class="form-control" value="<?= $row['start_date'] ?>">
                        </div>
                        <div class="col">
                          <label>End Date</label>
                          <input type="date" name="end_date" class="form-control" value="<?= $row['end_date'] ?>">
                        </div>
                      </div>
                      <div class="d-flex gap-2">
                        <button type="submit" name="update_class" class="btn btn-primary w-50"><i class="bi bi-check-circle me-1"></i>Update</button>
                        <button type="button" class="btn btn-secondary w-50" data-bs-dismiss="modal"><i class="bi bi-x-circle me-1"></i>Cancel</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>

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
    $('#classTable').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        info: true
    });
});
</script>
</body>
</html>
