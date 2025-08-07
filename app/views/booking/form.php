<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0"><i class="bi bi-calendar-plus"></i> Reservar Cita</h4>
                    <p class="mb-0">Completa el formulario para reservar tu cita</p>
                </div>
                <div class="card-body">
                    
                    <!-- Display errors -->
                    <?php if (isset($_SESSION['booking_errors'])): ?>
                        <div class="alert alert-danger">
                            <h6><i class="bi bi-exclamation-triangle"></i> Por favor corrige los siguientes errores:</h6>
                            <ul class="mb-0">
                                <?php foreach ($_SESSION['booking_errors'] as $field => $errors): ?>
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php unset($_SESSION['booking_errors']); ?>
                    <?php endif; ?>
                    
                    <form id="bookingForm" method="POST" action="/book">
                        <!-- Phone Number -->
                        <div class="mb-3">
                            <label for="phone" class="form-label">
                                <i class="bi bi-telephone"></i> Teléfono de Contacto *
                            </label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   placeholder="809-555-0000" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}"
                                   value="<?= $_SESSION['booking_data']['phone'] ?? '' ?>" required>
                            <div class="form-text">Formato: XXX-XXX-XXXX</div>
                            <div id="client-info" class="mt-2"></div>
                        </div>
                        
                        <!-- Client Information -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">
                                        <i class="bi bi-person"></i> Nombre Completo *
                                    </label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?= $_SESSION['booking_data']['name'] ?? '' ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        <i class="bi bi-envelope"></i> Correo Electrónico
                                    </label>
                                    <input type="email" class="form-control" id="email" name="email"
                                           value="<?= $_SESSION['booking_data']['email'] ?? '' ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="cedula" class="form-label">
                                <i class="bi bi-card-text"></i> Número de Cédula (Opcional)
                            </label>
                            <input type="text" class="form-control" id="cedula" name="cedula" 
                                   placeholder="001-1234567-8" 
                                   value="<?= $_SESSION['booking_data']['cedula'] ?? '' ?>">
                        </div>
                        
                        <!-- Service Selection -->
                        <div class="mb-3">
                            <label for="service_id" class="form-label">
                                <i class="bi bi-star"></i> Servicio *
                            </label>
                            <select class="form-select" id="service_id" name="service_id" required>
                                <option value="">Selecciona un servicio</option>
                                <?php if (is_array($services) && !empty($services)): ?>
                                    <?php foreach ($services as $service): ?>
                                        <option value="<?= $service['id'] ?>" 
                                                data-price="<?= $service['price'] ?>"
                                                data-duration="<?= $service['duration_minutes'] ?>"
                                                <?= (($_SESSION['booking_data']['service_id'] ?? '') == $service['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($service['name']) ?> - $<?= number_format($service['price'], 2) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="">No hay servicios disponibles</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        
                        <!-- Manicurist Selection -->
                        <div class="mb-3">
                            <label for="manicurist_id" class="form-label">
                                <i class="bi bi-person-badge"></i> Manicurista (Opcional)
                            </label>
                            <select class="form-select" id="manicurist_id" name="manicurist_id">
                                <option value="">Sin preferencia</option>
                                <?php if (is_array($manicurists) && !empty($manicurists)): ?>
                                    <?php foreach ($manicurists as $manicurist): ?>
                                        <option value="<?= $manicurist['id'] ?>"
                                                <?= (($_SESSION['booking_data']['manicurist_id'] ?? '') == $manicurist['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($manicurist['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="">No hay manicuristas disponibles</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        
                        <!-- Date Selection -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="appointment_date" class="form-label">
                                        <i class="bi bi-calendar"></i> Fecha *
                                    </label>
                                    <input type="date" class="form-control" id="appointment_date" name="appointment_date"
                                           min="<?= date('Y-m-d') ?>" 
                                           value="<?= $_SESSION['booking_data']['appointment_date'] ?? '' ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="appointment_time" class="form-label">
                                        <i class="bi bi-clock"></i> Hora *
                                    </label>
                                    <select class="form-select" id="appointment_time" name="appointment_time" required>
                                        <option value="">Selecciona una fecha primero</option>
                                    </select>
                                    <div id="time-loading" class="loading text-center mt-2">
                                        <div class="spinner-border spinner-border-sm" role="status">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                        Cargando horarios disponibles...
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Notes -->
                        <div class="mb-3">
                            <label for="notes" class="form-label">
                                <i class="bi bi-chat-left-text"></i> Notas Adicionales
                            </label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" 
                                      placeholder="Cualquier comentario o solicitud especial..."><?= $_SESSION['booking_data']['notes'] ?? '' ?></textarea>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle"></i> Confirmar Reserva
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.getElementById('phone');
    const clientInfoDiv = document.getElementById('client-info');
    const dateInput = document.getElementById('appointment_date');
    const timeSelect = document.getElementById('appointment_time');
    const serviceSelect = document.getElementById('service_id');
    const manicuristSelect = document.getElementById('manicurist_id');
    const timeLoading = document.getElementById('time-loading');
    
    // Format phone number as user types
    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length >= 6) {
            value = value.substring(0,3) + '-' + value.substring(3,6) + '-' + value.substring(6,10);
        } else if (value.length >= 3) {
            value = value.substring(0,3) + '-' + value.substring(3);
        }
        e.target.value = value;
    });
    
    // Check for existing client when phone changes
    phoneInput.addEventListener('blur', function() {
        const phone = this.value;
        if (phone.match(/^\d{3}-\d{3}-\d{4}$/)) {
            fetch(`/api/client?phone=${encodeURIComponent(phone)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const client = data.data;
                        document.getElementById('name').value = client.name || '';
                        document.getElementById('email').value = client.email || '';
                        document.getElementById('cedula').value = client.cedula || '';
                        
                        clientInfoDiv.innerHTML = `
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> Cliente encontrado: ${client.name}
                            </div>
                        `;
                    } else {
                        clientInfoDiv.innerHTML = '';
                    }
                })
                .catch(error => {
                    console.error('Error checking client:', error);
                });
        }
    });
    
    // Load available times when date, service, or manicurist changes
    function loadAvailableTimes() {
        const date = dateInput.value;
        const serviceId = serviceSelect.value;
        const manicuristId = manicuristSelect.value;
        
        if (!date) {
            timeSelect.innerHTML = '<option value="">Selecciona una fecha primero</option>';
            return;
        }
        
        // Check if selected date is Sunday
        const selectedDate = new Date(date);
        if (selectedDate.getDay() === 0) {
            timeSelect.innerHTML = '<option value="">No hay atención los domingos</option>';
            return;
        }
        
        timeLoading.style.display = 'block';
        timeSelect.disabled = true;
        
        const params = new URLSearchParams();
        params.append('date', date);
        if (serviceId) params.append('service_id', serviceId);
        
        fetch(`/api/available-slots?${params}`)
            .then(response => response.json())
            .then(data => {
                timeLoading.style.display = 'none';
                timeSelect.disabled = false;
                
                if (data.success) {
                    timeSelect.innerHTML = '<option value="">Selecciona un horario</option>';
                    
                    // If specific manicurist selected
                    if (manicuristId && data.data[manicuristId]) {
                        const slots = data.data[manicuristId].slots;
                        slots.forEach(slot => {
                            const option = document.createElement('option');
                            option.value = slot;
                            option.textContent = formatTime(slot);
                            timeSelect.appendChild(option);
                        });
                    } else {
                        // Show all available slots from all manicurists
                        const allSlots = new Set();
                        Object.values(data.data).forEach(manicurist => {
                            manicurist.slots.forEach(slot => allSlots.add(slot));
                        });
                        
                        Array.from(allSlots).sort().forEach(slot => {
                            const option = document.createElement('option');
                            option.value = slot;
                            option.textContent = formatTime(slot);
                            timeSelect.appendChild(option);
                        });
                    }
                    
                    if (timeSelect.children.length === 1) {
                        timeSelect.innerHTML = '<option value="">No hay horarios disponibles</option>';
                    }
                } else {
                    timeSelect.innerHTML = '<option value="">Error cargando horarios</option>';
                }
            })
            .catch(error => {
                console.error('Error loading times:', error);
                timeLoading.style.display = 'none';
                timeSelect.disabled = false;
                timeSelect.innerHTML = '<option value="">Error cargando horarios</option>';
            });
    }
    
    function formatTime(time24) {
        const [hours, minutes] = time24.split(':');
        const hour = parseInt(hours);
        const ampm = hour >= 12 ? 'PM' : 'AM';
        const hour12 = hour % 12 || 12;
        return `${hour12}:${minutes} ${ampm}`;
    }
    
    // Prevent selecting past dates
    dateInput.addEventListener('change', function() {
        const selectedDate = new Date(this.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (selectedDate < today) {
            alert('No puedes seleccionar una fecha pasada');
            this.value = '';
            return;
        }
        
        loadAvailableTimes();
    });
    
    serviceSelect.addEventListener('change', loadAvailableTimes);
    manicuristSelect.addEventListener('change', loadAvailableTimes);
    
    // Load times if date is already selected (for form persistence)
    if (dateInput.value) {
        loadAvailableTimes();
    }
});
</script>

<?php 
// Clear session data after displaying
unset($_SESSION['booking_data']); 
?>