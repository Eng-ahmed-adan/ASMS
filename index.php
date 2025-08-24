<?php
include 'config.php';
session_start();
$message = "";

if (isset($_POST['login'])) {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email=? AND status='active' AND confirmed=1 LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user'] = $row;

            if (isset($_POST['remember'])) {
                setcookie("email", $email, time() + (86400 * 30), "/");
            }

            header("Location: dashboard.php");
            exit;
        } else {
            $message = "<div class='alert alert-danger border-0 shadow-sm'>Invalid password!</div>";
        }
    } else {
        $message = "<div class='alert alert-warning border-0 shadow-sm'>User not found or not confirmed!</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>ASMS Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    :root{
      --brand: #0ea5e9;
      --brand-2: #22d3ee;
      --card-radius: 1.25rem;
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
      <!-- Left: Info -->
      <div class="col-12 col-lg-5 auth-hero p-4 d-flex flex-column justify-content-between">
        <div>
          <div class="d-flex align-items-center gap-3 mb-4">
            <div class="brand-badge">AS</div>
            <div>
              <div class="fw-bold">ASMS</div>
              <div class="small" style="opacity:.85">Academic Student Management</div>
            </div>
          </div>
          <h3 class="fw-bold mb-2">Welcome back 👋</h3>
          <p class="mb-0" style="opacity:.9">Login to manage your classes, teachers, and students efficiently.</p>
        </div>
        <div class="pt-4 small" style="opacity:.9">
          Don’t have an account? <a class="link-light text-decoration-underline" href="register.php">Sign up</a>
        </div>
      </div>

      <!-- Right: Form -->
      <div class="col-12 col-lg-7 p-4 p-lg-5">
        <?php if (!empty($message)) : ?>
          <div class="mb-3"><?= $message ?></div>
        <?php endif; ?>

        <form method="post" novalidate>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" 
                   value="<?= htmlspecialchars($_COOKIE['email'] ?? '') ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <div class="input-group">
              <input type="password" id="password" name="password" class="form-control" required>
              <button type="button" class="btn btn-outline-secondary" onclick="togglePass(this)">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>
          <div class="form-check mb-3">
            <input type="checkbox" name="remember" class="form-check-input" id="remember">
            <label class="form-check-label" for="remember">Remember Me</label>
          </div>
          <button type="submit" name="login" class="btn btn-primary w-100 py-2">
            <i class="bi bi-box-arrow-in-right me-2"></i>Login
          </button>
        </form>
      </div>
    </div>
  </div>

  <script>
    function togglePass(btn){
      const input = document.getElementById("password");
      const icon  = btn.querySelector("i");
      if (input.type === "password") {
        input.type = "text";
        icon.classList.replace("bi-eye","bi-eye-slash");
      } else {
        input.type = "password";
        icon.classList.replace("bi-eye-slash","bi-eye");
      }
    }
  </script>
</body>
</html>
