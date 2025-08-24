<?php
include 'config.php';
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['role']!=='staff') header("Location: login.php");
$user = $_SESSION['user'];

$msg = "";

// Delete subject
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    if($conn->query("DELETE FROM subjects WHERE id=$id")){
        $msg = "<div class='alert alert-success shadow-sm border-0'>Subject deleted successfully!</div>";
    } else {
        $msg = "<div class='alert alert-danger shadow-sm border-0'>Error: ".$conn->error."</div>";
    }
}

// Fetch subjects
$sql = "SELECT * FROM subjects ORDER BY id DESC";
$result = $conn->query($sql);
if(!$result) die("<div class='alert alert-danger'>Query failed: ".$conn->error."</div>");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Subjects | ASMS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
body{background:linear-gradient(135deg,#f0f4f8,#dbeafe); min-height:100vh; display:flex; justify-content:center; padding:24px;}
.card{width:100%; max-width:900px; border-radius:1rem; box-shadow:0 20px 60px rgba(2,6,23,.08);}
</style>
</head>
<body>
<div class="card p-4">
    <h3 class="mb-3 text-info">Subjects List</h3>
    <?= $msg ?>
    <a href="add_subjects.php" class="btn btn-info mb-3"><i class="bi bi-journal-bookmark me-2"></i>Add Subject</a>
    <div class="table-responsive">
        <table id="subjectsTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Subject Name</th>
                    <th>Academic Year</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($s = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $s['id'] ?></td>
                    <td><?= htmlspecialchars($s['name']) ?></td>
                    <td><?= htmlspecialchars($s['academic_year']) ?></td>
                    <td><?= htmlspecialchars($s['date']) ?></td>
                    <td class="text-nowrap">
                        <button class="btn btn-warning btn-sm"><i class="bi bi-pencil-square"></i></button>
                        <a href="?delete=<?= $s['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this subject?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function(){
    $('#subjectsTable').DataTable({paging:true, searching:true, ordering:true, info:true});
});
</script>
</body>
</html>
