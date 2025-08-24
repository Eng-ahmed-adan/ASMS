<?php
session_start();
// Strict guard: redirect if not logged in
if (!isset($_SESSION['user']) || !is_array($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
$user = $_SESSION['user'];

// Helper for safe output
function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

// Avatar path (fallback to default)
$avatarFile = !empty($user['profile']) ? ('uploads/' . basename($user['profile'])) : 'uploads/default.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    :root {
      --sidebar-bg: #0f172a;      /* slate-900 */
      --sidebar-hover: #1e293b;   /* slate-800 */
      --sidebar-accent: #22d3ee;  /* cyan-400 */
      --brand: #0ea5e9;           /* sky-500 */
      --card-radius: 1.25rem;     /* 20px */
      --sidebar-w: 260px;
      --sidebar-w-collapsed: 72px;
    }

    body { background: linear-gradient(135deg, #f8fafc, #eef2ff); min-height: 100vh; }

    /* Sidebar */
    .app-sidebar {
      position: fixed; inset: 0 auto 0 0; width: var(--sidebar-w); z-index: 1040;
      background: var(--sidebar-bg); color: #cbd5e1; transition: width .25s ease, transform .25s ease;
      box-shadow: 0 10px 30px rgba(2, 6, 23, .4);
      overflow: hidden;
    }
    .app-sidebar.collapsed { width: var(--sidebar-w-collapsed); }
    .app-brand { height: 64px; display: flex; align-items: center; gap: .75rem; padding: 0 1rem; color: #fff; text-decoration: none; }
    .app-brand .logo { width: 36px; height: 36px; border-radius: 12px; background: linear-gradient(135deg, #06b6d4, #3b82f6); display: grid; place-items: center; color: #fff; font-weight: 700; }
    .app-brand span { font-weight: 700; letter-spacing: .3px; }

    .nav-section { padding: .5rem; }
    .nav-link { color: #94a3b8; border-radius: .75rem; padding: .6rem .9rem; display: flex; align-items: center; gap: .75rem; margin: .15rem .35rem; text-decoration: none; transition: background .2s ease, color .2s ease; }
    .nav-link .bi { font-size: 1.15rem; }
    .nav-link:hover { background: var(--sidebar-hover); color: #e2e8f0; }
    .nav-link.active { background: rgba(34, 211, 238, .1); color: #e2e8f0; border: 1px solid rgba(34,211,238,.25); }

    .nav-text { white-space: nowrap; }
    .app-sidebar.collapsed .nav-text { display: none; }

    /* Content wrapper */
    .app-content { margin-left: var(--sidebar-w); transition: margin-left .25s ease; }
    .app-content.expanded { margin-left: var(--sidebar-w-collapsed); }

    /* Header */
    .app-header { position: sticky; top: 0; z-index: 1030; background: rgba(255,255,255,.85); backdrop-filter: blur(8px); padding: .75rem 1rem; box-shadow: 0 4px 20px rgba(2,6,23,.08); }
    .btn-icon { border-radius: 12px; }

    /* Cards */
    .card { border: 0; border-radius: var(--card-radius); box-shadow: 0 10px 30px rgba(2,6,23,.06); }

    /* Footer */
    .app-footer { color: #64748b; }

    /* Mobile behavior: sidebar becomes off-canvas */
    @media (max-width: 992px) {
      .app-sidebar { transform: translateX(-100%); }
      .app-sidebar.open { transform: translateX(0); }
      .app-content, .app-content.expanded { margin-left: 0; }
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <aside id="sidebar" class="app-sidebar">
    <a href="#" class="app-brand text-decoration-none">
      <div class="logo">AS</div>
      <span class="brand-text">ASMS</span>
    </a>

    <nav class="nav-section">
      <a class="nav-link active" href="dashboard.php" data-bs-toggle="tooltip" data-bs-placement="right" title="Dashboard">
        <i class="bi bi-speedometer2"></i>
        <span class="nav-text">Dashboard</span>
      </a>
      <a class="nav-link" href="profile.php" data-bs-toggle="tooltip" data-bs-placement="right" title="Profile">
        <i class="bi bi-person-circle"></i>
        <span class="nav-text">Profile</span>
      </a>
      <a class="nav-link" href="changepassword.php" data-bs-toggle="tooltip" data-bs-placement="right" title="Change Password">
        <i class="bi bi-key"></i>
        <span class="nav-text">Change Password</span>
      </a>
      <a class="nav-link" href="#" data-bs-toggle="tooltip" data-bs-placement="right" title="Courses">
        <i class="bi bi-journal-bookmark"></i>
        <span class="nav-text">Courses</span>
      </a>
      <a class="nav-link" href="#" data-bs-toggle="tooltip" data-bs-placement="right" title="Users">
        <i class="bi bi-people"></i>
        <span class="nav-text">Users</span>
      </a>
    </nav>

    <div class="nav-section mt-auto p-2">
      <a class="nav-link" href="logout.php" data-bs-toggle="tooltip" data-bs-placement="right" title="Logout">
        <i class="bi bi-box-arrow-right"></i>
        <span class="nav-text">Logout</span>
      </a>
    </div>
  </aside>

  <!-- Main content -->
  <div id="content" class="app-content">
    <!-- Header -->
    <header class="app-header shadow-sm">
      <div class="container-fluid d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
          <button id="sidebarToggle" class="btn btn-light btn-icon px-3"><i class="bi bi-list"></i></button>
          <h5 class="mb-0 fw-semibold text-secondary">Dashboard</h5>
        </div>
        <div class="d-flex align-items-center gap-3">
          <button class="btn btn-light position-relative btn-icon">
            <i class="bi bi-bell"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3</span>
          </button>
          <div class="dropdown">
            <button class="btn btn-light d-flex align-items-center gap-2" data-bs-toggle="dropdown" aria-expanded="false">
              <img src="<?= e($avatarFile) ?>" alt="avatar" class="rounded-circle" width="36" height="36" />
              <div class="text-start d-none d-sm-block">
                <div class="fw-semibold small">Welcome, <?= e($user['fullname'] ?? '') ?></div>
                <div class="text-muted small"><?= e(ucfirst($user['role'] ?? '')) ?></div>
              </div>
              <i class="bi bi-caret-down-fill small ms-1"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow">
              <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i>Profile</a></li>
              <li><a class="dropdown-item" href="changepassword.php"><i class="bi bi-key me-2"></i>Change Password</a></li>
              <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
            </ul>
          </div>
        </div>
      </div>
    </header>

    <!-- Body -->
    <main class="container py-4">
      <div class="row g-4">
        <div class="col-12 col-xl-8">
          <div class="card p-4">
            <div class="d-flex align-items-center gap-3 mb-3">
              <img src="<?= e($avatarFile) ?>" class="rounded-circle" width="56" height="56" alt="avatar" />
              <div>
                <h5 class="mb-1"><?= e($user['fullname'] ?? '') ?></h5>
                <div class="text-muted small">Role: <?= e(ucfirst($user['role'] ?? '')) ?></div>
              </div>
            </div>
            <div class="row g-3">
              <div class="col-md-6">
                <div class="border rounded-3 p-3 bg-white h-100">
                  <div class="text-muted small">Email</div>
                  <div class="fw-semibold"><?= e($user['email'] ?? '') ?></div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="border rounded-3 p-3 bg-white h-100">
                  <div class="text-muted small">Mobile</div>
                  <div class="fw-semibold"><?= e($user['mobile'] ?? '') ?></div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12 col-xl-4">
          <div class="card p-4">
            <h6 class="mb-3">Quick Actions</h6>
            <div class="d-grid gap-2">
              <a class="btn btn-outline-primary" href="profile.php"><i class="bi bi-person me-2"></i>Go to Profile</a>
              <a class="btn btn-outline-secondary" href="changepassword.php"><i class="bi bi-key me-2"></i>Change Password</a>
              <a class="btn btn-outline-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
            </div>
          </div>
        </div>
        <!-- Quick Actions -->
<div class="row g-3 my-4">
  <div class="col-12 col-md-4">
    <a href="class.php" class="btn btn-primary w-100 p-4 d-flex flex-column align-items-center justify-content-center shadow-sm rounded">
      <i class="bi bi-journal-text fs-2 mb-2"></i>
      <span>Classes</span>
    </a>
  </div>
  <div class="col-12 col-md-4">
    <a href="students.php" class="btn btn-success w-100 p-4 d-flex flex-column align-items-center justify-content-center shadow-sm rounded">
      <i class="bi bi-people fs-2 mb-2"></i>
      <span>Students</span>
    </a>
  </div>
  <div class="col-12 col-md-4">
    <a href="teachers.php" class="btn btn-warning w-100 p-4 d-flex flex-column align-items-center justify-content-center shadow-sm rounded">
      <i class="bi bi-person-badge fs-2 mb-2"></i>
      <span>Teachers</span>
    </a>
  </div>
  <div class="col-12 col-md-4 mb-3">
  <a href="subjects.php" class="btn btn-info w-100 p-4 d-flex flex-column align-items-center justify-content-center shadow-sm rounded">
    <i class="bi bi-journal-bookmark fs-2 mb-2"></i>
    <span>Subjects</span>
  </a>
</div>

</div>

      </div>
    </main>

    <footer class="app-footer py-4">
      <div class="container text-center small">&copy; <?= date('Y') ?> ASMS &middot; All rights reserved</div>
    </footer>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');
    const toggleBtn = document.getElementById('sidebarToggle');

    // Restore persisted state
    const isCollapsed = localStorage.getItem('sidebar-collapsed') === '1';
    if (isCollapsed) {
      sidebar.classList.add('collapsed');
      content.classList.add('expanded');
    }

    // Toggle behavior: desktop = collapse width; mobile = slide in/out
    function isMobile() { return window.matchMedia('(max-width: 992px)').matches; }

    function updateTooltips() {
      // Enable tooltips only when collapsed OR on mobile open state
      const enable = sidebar.classList.contains('collapsed');
      const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      tooltipTriggerList.forEach(el => {
        const existing = bootstrap.Tooltip.getInstance(el);
        if (enable) {
          if (!existing) new bootstrap.Tooltip(el);
        } else if (existing) {
          existing.dispose();
        }
      });
    }

    toggleBtn.addEventListener('click', () => {
      if (isMobile()) {
        sidebar.classList.toggle('open');
        return;
      }
      sidebar.classList.toggle('collapsed');
      content.classList.toggle('expanded');
      localStorage.setItem('sidebar-collapsed', sidebar.classList.contains('collapsed') ? '1' : '0');
      updateTooltips();
    });

    // Close mobile sidebar when clicking outside
    document.addEventListener('click', (e) => {
      if (!isMobile()) return;
      const clickInsideSidebar = sidebar.contains(e.target) || toggleBtn.contains(e.target);
      if (!clickInsideSidebar) sidebar.classList.remove('open');
    });

    // Init tooltips on load depending on state
    updateTooltips();

    // Handle resize changes (remove open on desktop)
    window.addEventListener('resize', () => {
      if (!isMobile()) sidebar.classList.remove('open');
      updateTooltips();
    });
  </script>
</body>
</html>
