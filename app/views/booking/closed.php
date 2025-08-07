<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark text-center">
                    <h4 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Reservas Cerradas</h4>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="bi bi-clock text-warning" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h5 class="mb-3"><?= htmlspecialchars($message) ?></h5>
                    
                    <div class="alert alert-info">
                        <h6><i class="bi bi-info-circle"></i> Horarios de Atención</h6>
                        <p class="mb-0">
                            <strong>Lunes a Sábado:</strong> 8:00 AM - 7:00 PM<br>
                            <strong>Domingo:</strong> Cerrado
                        </p>
                    </div>
                    
                    <p class="text-muted">
                        Las reservas online están disponibles únicamente durante nuestros horarios de atención.
                        Puedes reservar tu cita cuando volvamos a estar abiertos.
                    </p>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="/" class="btn btn-primary">
                            <i class="bi bi-house"></i> Volver al Inicio
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>