<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Sandy Beauty Nails - Admin' ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-color: #d4af37;
            --primary-dark: #b8941f;
            --sidebar-bg: #343a40;
        }
        
        .sidebar {
            min-height: 100vh;
            background-color: var(--sidebar-bg);
        }
        
        .sidebar .nav-link {
            color: #adb5bd;
            padding: 0.75rem 1rem;
        }
        
        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255,255,255,0.1);
        }
        
        .sidebar .nav-link.active {
            color: #fff;
            background-color: var(--primary-color);
        }
        
        .main-content {
            margin-left: 0;
        }
        
        @media (min-width: 768px) {
            .main-content {
                margin-left: 250px;
            }
        }
        
        .navbar-brand {
            color: var(--primary-color) !important;
            font-weight: bold;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }
        
        .text-primary {
            color: var(--primary-color) !important;
        }
        
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid rgba(0,0,0,.125);
        }
        
        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        
        .table th {
            border-top: none;
            font-weight: 600;
            color: #495057;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar position-fixed d-none d-md-block">
        <div class="position-sticky">
            <div class="d-flex align-items-center p-3 text-white">
                <i class="bi bi-gem me-2"></i>
                <span class="fs-6 fw-bold">Sandy Beauty Admin</span>
            </div>
            
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/admin/dashboard') !== false) ? 'active' : '' ?>" href="/admin/dashboard">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/admin/appointments') !== false) ? 'active' : '' ?>" href="/admin/appointments">
                        <i class="bi bi-calendar-check me-2"></i> Citas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/admin/clients') !== false) ? 'active' : '' ?>" href="/admin/clients">
                        <i class="bi bi-people me-2"></i> Clientes
                    </a>
                </li>
                
                <?php if ($_SESSION['admin_role'] === 'superadmin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/admin/reports') !== false) ? 'active' : '' ?>" href="/admin/reports">
                        <i class="bi bi-graph-up me-2"></i> Reportes
                    </a>
                </li>
                <?php endif; ?>
                
                <li class="nav-item mt-auto">
                    <a class="nav-link" href="/admin/logout">
                        <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
            <div class="container-fluid">
                <button class="btn btn-outline-secondary d-md-none me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas">
                    <i class="bi bi-list"></i>
                </button>
                
                <span class="navbar-brand mb-0 h1">Administración</span>
                
                <div class="navbar-nav ms-auto">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i>
                            <?= htmlspecialchars($_SESSION['admin_username']) ?>
                            <?php if ($_SESSION['admin_role'] === 'manicurist'): ?>
                                <small class="text-muted">(<?= htmlspecialchars($_SESSION['admin_manicurist_name']) ?>)</small>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/admin/logout"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="container-fluid py-4">
            <?= $content ?>
        </main>
    </div>

    <!-- Offcanvas Sidebar for Mobile -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarOffcanvas">
        <div class="offcanvas-header bg-dark text-white">
            <h5 class="offcanvas-title">
                <i class="bi bi-gem me-2"></i>Sandy Beauty Admin
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0 bg-dark">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link text-light" href="/admin/dashboard">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-light" href="/admin/appointments">
                        <i class="bi bi-calendar-check me-2"></i> Citas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-light" href="/admin/clients">
                        <i class="bi bi-people me-2"></i> Clientes
                    </a>
                </li>
                
                <?php if ($_SESSION['admin_role'] === 'superadmin'): ?>
                <li class="nav-item">
                    <a class="nav-link text-light" href="/admin/reports">
                        <i class="bi bi-graph-up me-2"></i> Reportes
                    </a>
                </li>
                <?php endif; ?>
                
                <li class="nav-item">
                    <a class="nav-link text-light" href="/admin/logout">
                        <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Chart.js for reports -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Auto-hide alerts after 5 seconds
        document.querySelectorAll('.alert').forEach(function(alert) {
            setTimeout(function() {
                if (alert.classList.contains('alert-success')) {
                    alert.style.opacity = '0';
                    setTimeout(function() { alert.remove(); }, 300);
                }
            }, 5000);
        });
    </script>
</body>
</html>