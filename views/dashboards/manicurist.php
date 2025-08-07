<!-- Manicurist Dashboard Header -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard - Manicurista</h1>
    <div class="text-muted">
        <i class="bi bi-person-circle"></i> <?= $_SESSION['admin_name'] ?? 'Manicurista' ?>
    </div>
</div>

<!-- Manicurist Statistics -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Mis Citas Hoy</h5>
                        <h3 class="mb-0"><?= $todayStats['my_appointments'] ?? 0 ?></h3>
                        <small>Programadas</small>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-calendar-day" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Esta Semana</h5>
                        <h3 class="mb-0"><?= $weekStats['my_appointments'] ?? 0 ?></h3>
                        <small>Completadas</small>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-calendar-week" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Próxima Cita</h5>
                        <h6 class="mb-0"><?= $nextAppointment['time'] ?? 'Sin citas' ?></h6>
                        <small><?= $nextAppointment['client'] ?? '' ?></small>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-clock" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Today's Appointments -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-calendar-day"></i> Mis Citas de Hoy
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($todayAppointments)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Hora</th>
                                    <th>Cliente</th>
                                    <th>Servicio</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($todayAppointments as $appointment): ?>
                                <tr>
                                    <td><?= date('g:i A', strtotime($appointment['appointment_time'])) ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($appointment['client_name']) ?></strong><br>
                                        <small class="text-muted"><?= formatPhone($appointment['client_phone']) ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($appointment['service_name']) ?></td>
                                    <td><?= getAppointmentStatusBadge($appointment['status']) ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= url('admin/appointments/' . $appointment['id']) ?>" class="btn btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <?php if ($appointment['status'] === 'confirmed'): ?>
                                            <button class="btn btn-outline-success" onclick="updateStatus(<?= $appointment['id'] ?>, 'completed')">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-calendar-x" style="font-size: 3rem;"></i>
                        <p class="mt-2">No tienes citas programadas para hoy</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function updateStatus(appointmentId, status) {
    if (confirm('¿Estás seguro de cambiar el estado de esta cita?')) {
        // Implementation for status update
        fetch(`<?= url('admin/appointments/') ?>${appointmentId}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({status: status})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error al actualizar el estado');
            }
        });
    }
}
</script>