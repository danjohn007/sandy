<?php
/**
 * Register page for Sandy Beauty Nails
 * Currently not used - admin users are managed internally
 */
require_once __DIR__ . '/../includes/functions.php';

startSession();
?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sandy Beauty Nails - Registro</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center p-5">
                        <i class="bi bi-gem text-primary" style="font-size: 3rem;"></i>
                        <h3 class="mt-3">Sandy Beauty Nails</h3>
                        <p class="text-muted">Registro de usuarios no disponible</p>
                        <p>Los usuarios administrativos son gestionados internamente por el SuperAdmin.</p>
                        <hr>
                        <div class="d-grid gap-2">
                            <a href="<?= url('login.php') ?>" class="btn btn-primary">
                                <i class="bi bi-box-arrow-in-right"></i> Ir a Login
                            </a>
                            <a href="<?= url('') ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-house"></i> Volver al Inicio
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>