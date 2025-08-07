<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Sandy Beauty Nails' ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link href="/assets/css/style.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #d4af37;
            --primary-dark: #b8941f;
            --secondary-color: #f8f9fa;
            --accent-color: #e91e63;
        }
        
        .navbar-brand {
            font-weight: bold;
            color: var(--primary-color) !important;
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
        
        .bg-primary {
            background-color: var(--primary-color) !important;
        }
        
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            padding: 80px 0;
        }
        
        .card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: none;
        }
        
        .footer {
            background-color: #343a40;
            color: white;
            margin-top: 50px;
        }
        
        .loading {
            display: none;
        }
        
        .alert {
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="bi bi-gem"></i> Sandy Beauty Nails
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/book">Reservar Cita</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin">Administración</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <?= $content ?>
    </main>

    <!-- Footer -->
    <footer class="footer py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="bi bi-gem"></i> Sandy Beauty Nails</h5>
                    <p>Tu belleza en las mejores manos</p>
                </div>
                <div class="col-md-3">
                    <h6>Servicios</h6>
                    <ul class="list-unstyled">
                        <li>Manicure</li>
                        <li>Pedicure</li>
                        <li>Uñas Acrílicas</li>
                        <li>Diseños Artísticos</li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6>Horarios</h6>
                    <p>Lunes a Sábado<br>8:00 AM - 7:00 PM</p>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p>&copy; 2024 Sandy Beauty Nails. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="/assets/js/app.js"></script>
</body>
</html>