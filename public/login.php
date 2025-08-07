<?php
/**
 * Login page for Sandy Beauty Nails
 */
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

startSession();

// Redirect if already logged in
$auth = getAuth();
if ($auth->isLoggedIn()) {
    header('Location: ' . url('admin/dashboard'));
    exit;
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = cleanInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $_SESSION['login_error'] = 'Usuario y contraseña son requeridos';
    } else {
        $result = $auth->login($username, $password);
        if ($result['success']) {
            header('Location: ' . url('admin/dashboard'));
            exit;
        } else {
            $_SESSION['login_error'] = $result['error'];
        }
    }
}
?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sandy Beauty Nails - Login</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-color: #d4af37;
            --primary-dark: #b8941f;
        }
        
        body {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            min-height: 100vh;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: none;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
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
    </style>
</head>
<body class="d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card login-card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="bi bi-gem text-primary" style="font-size: 3rem;"></i>
                            <h3 class="mt-2 text-primary">Sandy Beauty Nails</h3>
                            <p class="text-muted">Acceso Administrativo</p>
                        </div>
                        
                        <?php if (isset($_SESSION['login_error'])): ?>
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle"></i>
                                <?= htmlspecialchars($_SESSION['login_error']) ?>
                            </div>
                            <?php unset($_SESSION['login_error']); ?>
                        <?php endif; ?>
                        
                        <form method="POST" action="<?= url('login.php') ?>">
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="bi bi-person"></i> Usuario
                                </label>
                                <input type="text" class="form-control form-control-lg" id="username" name="username" required>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label">
                                    <i class="bi bi-lock"></i> Contraseña
                                </label>
                                <input type="password" class="form-control form-control-lg" id="password" name="password" required>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                                </button>
                            </div>
                        </form>
                        
                        <hr class="my-4">
                        
                        <div class="text-center">
                            <a href="<?= url('') ?>" class="text-muted text-decoration-none">
                                <i class="bi bi-arrow-left"></i> Volver al sitio público
                            </a>
                        </div>
                        
                        <div class="mt-4">
                            <small class="text-muted">
                                <strong>Credenciales de prueba:</strong><br>
                                <strong>SuperAdmin:</strong> admin / admin123<br>
                                <strong>Manicurista:</strong> sandy / admin123
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>