<?php
include 'config.php';

$message = "";
$errors  = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    // Gather input
    $fullname = trim($_POST['fullname'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $mobile   = trim($_POST['mobile'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';
    $role     = $_POST['role'] ?? 'student';

    // Basic validation
    if ($fullname === '') $errors[] = "Full name is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
    if ($password === '' || strlen($password) < 6) $errors[] = "Password must be at least 6 characters.";
    if ($password !== $confirm) $errors[] = "Passwords do not match.";
    if (!in_array($role, ['student','teacher','staff','admin'], true)) $errors[] = "Invalid role selected.";

    // Check email uniqueness
    if (!$errors) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) $errors[] = "Email is already registered.";
        $stmt->close();
    }

    // Handle profile upload (optional)
    $profile = "default.png";
    if (!$errors && !empty($_FILES['profile']['name'])) {
        $targetDir = __DIR__ . "/uploads/";
        if (!is_dir($targetDir)) @mkdir($targetDir, 0775, true);
        $ext = pathinfo($_FILES['profile']['name'], PATHINFO_EXTENSION);
        $safeName = time() . "_" . preg_replace('/[^a-zA-Z0-9_\.-]/','', basename($_FILES['profile']['name']));
        $target   = $targetDir . $safeName;

        // Simple whitelist
        $allowed = ['png','jpg','jpeg','gif','webp'];
        if (!in_array(strtolower($ext), $allowed, true)) {
            $errors[] = "Profile image must be PNG, JPG, GIF, or WEBP.";
        } elseif (!@move_uploaded_file($_FILES['profile']['tmp_name'], $target)) {
            $errors[] = "Failed to upload profile image.";
        } else {
            $profile = $safeName;
        }
    }

    // Insert user
    if (!$errors) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("
            INSERT INTO users (fullname, email, mobile, password, role, status, confirmed, profile)
            VALUES (?, ?, ?, ?, ?, 'active', 1, ?)
        ");
        $stmt->bind_param("ssssss", $fullname, $email, $mobile, $hash, $role, $profile);
        if ($stmt->execute()) {
            $message = "<div class='alert alert-success border-0 shadow-sm mb-0'>
                Registration successful! You can now <a href='index.php' class='alert-link'>Login</a>.
            </div>";
            // Clear posted values on success
            $fullname = $email = $mobile = "";
        } else {
            $errors[] = "Database error: " . htmlspecialchars($conn->error);
        }
        $stmt->close();
    }

    if ($errors) {
        $message = "<div class='alert alert-danger border-0 shadow-sm mb-0'><ul class='mb-0'>"
                 . "<li>" . implode("</li><li>", array_map('htmlspecialchars', $errors)) . "</li></ul></div>";
    }
}

// Helpers for sticky fields
function old($key) { return htmlspecialchars($_POST[$key] ?? "", ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create Account — ASMS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    :root{
      --brand: #0ea5e9;          /* sky-500 */
      --brand-2: #22d3ee;        /* cyan-400 */
      --card-radius: 1.25rem;    /* 20px */
    }
    body{
      min-height:100vh;
      background:
        radial-gradient(1200px 600px at 10% -10%, rgba(34,211,238,.15), transparent 60%),
        radial-gradient(1000px 500px at 110% 0%, rgba(14,165,233,.15), transparent 60%),
        linear-gradient(135deg, #f8fafc, #eef2ff);
      display:flex; align-items:center; justify-content:center; padding:24px;
    }
    .auth-card{
      width:100%; max-width: 720px;
      border-radius: var(--card-radius);
      border: 1px solid rgba(2,6,23,.06);
      box-shadow: 0 20px 60px rgba(2,6,23,.08);
      overflow:hidden;
    }
    .auth-hero{
      background: linear-gradient(135deg, var(--brand), var(--brand-2));
      color:#fff;
    }
    .brand-badge{
      width:48px; height:48px; border-radius:16px;
      display:grid; place-items:center; font-weight:700;
      background: rgba(255,255,255,.15);
      border: 1px solid rgba(255,255,255,.25);
    }
    .form-control, .form-select{
      border-radius: .9rem;
    }
    .input-group .btn{
      border-radius: .9rem;
    }
  </style>
</head>
<body>
  <div class="auth-card bg-white">
    <div class="row g-0">
      <!-- Left: Hero -->
      <div class="col-12 col-lg-5 auth-hero p-4 d-flex flex-column justify-content-between">
        <div>
          <div class="d-flex align-items-center gap-3 mb-4">
            <div class="brand-badge">AS</div>
            <div>
              <div class="fw-bold">ASMS</div>
              <div class="small" style="opacity:.85">Academic Student Management</div>
            </div>
          </div>
          <h3 class="fw-bold mb-2">Create your account</h3>
          <p class="mb-0" style="opacity:.9">Join the platform to manage classes, teachers, and students seamlessly.</p>
        </div>
        <div class="pt-4 small" style="opacity:.9">
          Already have an account? <a class="link-light text-decoration-underline" href="index.php">Login</a>
        </div>
      </div>

      <!-- Right: Form -->
      <div class="col-12 col-lg-7 p-4 p-lg-5">
        <?php if (!empty($message)) : ?>
          <div class="mb-3"><?= $message ?></div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" novalidate>
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">Full Name</label>
              <input type="text" name="fullname" class="form-control" value="<?= old('fullname') ?>" required>
            </div>
            <div class="col-12">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" value="<?= old('email') ?>" required>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label">Mobile</label>
              <input type="text" name="mobile" class="form-control" value="<?= old('mobile') ?>" required>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label">Role</label>
              <select name="role" class="form-select">
                <option value="student" <?= (old('role')==='student'?'selected':'') ?>>Student</option>
                <option value="teacher" <?= (old('role')==='teacher'?'selected':'') ?>>Teacher</option>
                <option value="staff"   <?= (old('role')==='staff'  ?'selected':'') ?>>Staff</option>
                <option value="admin"   <?= (old('role')==='admin'  ?'selected':'') ?>>Admin</option>
              </select>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Password</label>
              <div class="input-group">
                <input type="password" name="password" id="password" class="form-control" required minlength="6">
                <button type="button" class="btn btn-outline-secondary" onclick="togglePass('password', this)">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
              <div class="form-text">At least 6 characters.</div>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Confirm Password</label>
              <div class="input-group">
                <input type="password" name="confirm" id="confirm" class="form-control" required minlength="6">
                <button type="button" class="btn btn-outline-secondary" onclick="togglePass('confirm', this)">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
            </div>

            <div class="col-12">
              <label class="form-label">Profile Image (optional)</label>
              <input type="file" name="profile" class="form-control" accept=".png,.jpg,.jpeg,.gif,.webp">
              <div class="form-text">PNG, JPG, GIF, WEBP.</div>
            </div>

            <div class="col-12 pt-2">
              <button type="submit" name="register" class="btn btn-primary w-100 py-2">
                <i class="bi bi-person-plus me-2"></i>Create Account
              </button>
            </div>
          </div>
        </form>

        <div class="text-center small mt-3">
          By continuing, you agree to our <a href="#" class="text-decoration-underline">Terms</a>.
        </div>
      </div>
    </div>
  </div>

  <script>
    function togglePass(id, btn){
      const input = document.getElementById(id);
      const icon  = btn.querySelector('i');
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye'); icon.classList.add('bi-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash'); icon.classList.add('bi-eye');
      }
    }
  </script>
</body>
</html>
