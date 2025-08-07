<!-- Dashboard Header -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
    <div class="text-muted">
        <i class="bi bi-calendar"></i> <?= date('l, F j, Y') ?>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Hoy</h5>
                        <h3 class="mb-0"><?= $todayStats['total_appointments'] ?></h3>
                        <small>Citas</small>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-calendar-day" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Esta Semana</h5>
                        <h3 class="mb-0"><?= $weekStats['total_appointments'] ?></h3>
                        <small>Citas</small>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-calendar-week" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Este Mes</h5>
                        <h3 class="mb-0"><?= $monthStats['total_appointments'] ?></h3>
                        <small>Citas</small>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-calendar-month" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Ingresos Hoy</h5>
                        <h3 class="mb-0">$<?= number_format($todayStats['total_revenue'] ?? 0, 2) ?></h3>
                        <small>Total</small>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-currency-dollar" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Today's Appointments -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-calendar-day"></i> Citas de Hoy</h5>
                <a href="/admin/appointments" class="btn btn-sm btn-outline-primary">Ver Todas</a>
            </div>
            <div class="card-body">
                <?php if (empty($todayAppointments)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-calendar-x" style="font-size: 3rem;"></i>
                        <p class="mt-2">No hay citas programadas para hoy</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Hora</th>
                                    <th>Cliente</th>
                                    <th>Servicio</th>
                                    <th>Manicurista</th>
                                    <th>Estado</th>
                                    <th>Precio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($todayAppointments as $appointment): ?>
                                    <tr>
                                        <td><?= date('g:i A', strtotime($appointment['appointment_time'])) ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($appointment['client_name']) ?></strong><br>
                                            <small class="text-muted"><?= htmlspecialchars($appointment['client_phone']) ?></small>
                                        </td>
                                        <td><?= htmlspecialchars($appointment['service_name']) ?></td>
                                        <td><?= htmlspecialchars($appointment['manicurist_name'] ?? 'Sin asignar') ?></td>
                                        <td>
                                            <?php
                                            $statusClasses = [
                                                'pending' => 'bg-warning',
                                                'confirmed' => 'bg-info',
                                                'paid' => 'bg-success',
                                                'completed' => 'bg-primary',
                                                'cancelled' => 'bg-danger'
                                            ];
                                            $statusNames = [
                                                'pending' => 'Pendiente',
                                                'confirmed' => 'Confirmada',
                                                'paid' => 'Pagada',
                                                'completed' => 'Completada',
                                                'cancelled' => 'Cancelada'
                                            ];
                                            ?>
                                            <span class="badge <?= $statusClasses[$appointment['status']] ?? 'bg-secondary' ?> status-badge">
                                                <?= $statusNames[$appointment['status']] ?? $appointment['status'] ?>
                                            </span>
                                        </td>
                                        <td>$<?= number_format($appointment['total_amount'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Quick Stats -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-graph-up"></i> Resumen Rápido</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Ingresos Semana:</span>
                        <strong class="text-success">$<?= number_format($weekStats['total_revenue'] ?? 0, 2) ?></strong>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Ingresos Mes:</span>
                        <strong class="text-success">$<?= number_format($monthStats['total_revenue'] ?? 0, 2) ?></strong>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Promedio por Cita:</span>
                        <strong>$<?= number_format($monthStats['avg_revenue'] ?? 0, 2) ?></strong>
                    </div>
                </div>
                
                <hr>
                
                <h6 class="text-muted mb-3">Próximas Citas (7 días)</h6>
                
                <?php if (empty($upcomingAppointments)): ?>
                    <p class="text-muted">No hay citas próximas</p>
                <?php else: ?>
                    <?php foreach (array_slice($upcomingAppointments, 0, 5) as $appointment): ?>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <small class="text-muted"><?= date('M j', strtotime($appointment['appointment_date'])) ?></small>
                                <div><?= htmlspecialchars($appointment['client_name']) ?></div>
                            </div>
                            <small class="text-muted"><?= date('g:i A', strtotime($appointment['appointment_time'])) ?></small>
                        </div>
                    <?php endforeach; ?>
                    
                    <?php if (count($upcomingAppointments) > 5): ?>
                        <div class="text-center mt-2">
                            <a href="/admin/appointments" class="btn btn-sm btn-outline-primary">
                                Ver <?= count($upcomingAppointments) - 5 ?> más
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>