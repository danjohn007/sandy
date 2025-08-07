<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white text-center">
                    <h4 class="mb-0"><i class="bi bi-check-circle"></i> ¡Reserva Confirmada!</h4>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="bi bi-calendar-check text-success" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h5 class="mb-4">Tu cita ha sido reservada exitosamente</h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="text-primary"><i class="bi bi-person"></i> Cliente</h6>
                                    <p class="mb-0"><strong><?= htmlspecialchars($client['name']) ?></strong></p>
                                    <p class="mb-0"><?= htmlspecialchars($client['phone']) ?></p>
                                    <?php if ($client['email']): ?>
                                        <p class="mb-0"><?= htmlspecialchars($client['email']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="text-primary"><i class="bi bi-star"></i> Servicio</h6>
                                    <p class="mb-0"><strong><?= htmlspecialchars($service['name']) ?></strong></p>
                                    <p class="mb-0">Duración: <?= $service['duration_minutes'] ?> minutos</p>
                                    <p class="mb-0">Precio: $<?= number_format($service['price'], 2) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="text-primary"><i class="bi bi-calendar"></i> Fecha y Hora</h6>
                                    <p class="mb-0"><strong><?= date('l, F j, Y', strtotime($appointment['appointment_date'])) ?></strong></p>
                                    <p class="mb-0"><?= date('g:i A', strtotime($appointment['appointment_time'])) ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="text-primary"><i class="bi bi-person-badge"></i> Manicurista</h6>
                                    <p class="mb-0">
                                        <?php if ($manicurist): ?>
                                            <strong><?= htmlspecialchars($manicurist['name']) ?></strong>
                                        <?php else: ?>
                                            <em>Asignación automática</em>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mt-4">
                        <h6><i class="bi bi-info-circle"></i> Información Importante</h6>
                        <ul class="mb-0 text-start">
                            <li>Tu cita está confirmada pero aún pendiente de pago</li>
                            <li>Llega 10 minutos antes de tu cita</li>
                            <li>Para cancelar o reprogramar, contacta al salón</li>
                            <li>Recibirás un recordatorio el día anterior</li>
                        </ul>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                        <a href="/" class="btn btn-primary">
                            <i class="bi bi-house"></i> Volver al Inicio
                        </a>
                        <a href="/book" class="btn btn-outline-primary">
                            <i class="bi bi-plus-circle"></i> Reservar Otra Cita
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>