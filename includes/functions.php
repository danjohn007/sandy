<?php
/**
 * Funciones auxiliares para Sandy Beauty Nails
 */

// Incluir configuración de la aplicación si existe
if (file_exists(__DIR__ . '/../config/app.php')) {
    require_once __DIR__ . '/../config/app.php';
}

// Iniciar sesión si no está iniciada
function startSession() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

// Limpiar y validar entrada de datos
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Generar token CSRF
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verificar token CSRF
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Verificar formato de email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Verificar fortaleza de contraseña
function isStrongPassword($password) {
    // Mínimo 6 caracteres para Sandy (simplificado)
    return strlen($password) >= 6;
}

// Generar token aleatorio
function generateRandomToken($length = 32) {
    return bin2hex(random_bytes($length));
}

// Formatear fecha
function formatDate($date, $format = 'd/m/Y H:i') {
    return date($format, strtotime($date));
}

/**
 * Obtener URL base del sistema
 */
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['SCRIPT_NAME']);
    
    // Remove /public from path if present
    if (substr($path, -7) === '/public') {
        $path = substr($path, 0, -7);
    }
    
    return rtrim($protocol . '://' . $host . $path, '/');
}

/**
 * Construir URL del sistema
 */
function url($path = '') {
    $baseUrl = getBaseUrl();
    return $baseUrl . '/' . ltrim($path, '/');
}

/**
 * Redireccionar con mensaje flash
 * 
 * @param string $url URL de destino
 * @param string $message Mensaje flash a mostrar
 * @param string $type Tipo de mensaje: 'info', 'success', 'warning', 'error'
 */
function redirectWithMessage($url, $message, $type = 'info') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
    
    // Si la URL no empieza con http, usar url() helper
    if (!preg_match('/^https?:\/\//', $url)) {
        $url = url($url);
    }
    
    header("Location: $url");
    exit();
}

// Mostrar mensaje flash
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'info';
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}

// Verificar permisos de usuario
function hasPermission($userRole, $requiredRoles) {
    return in_array($userRole, $requiredRoles);
}

// Obtener jerarquía de roles para Sandy
function getRoleHierarchy() {
    return [
        'superadmin' => 2,
        'manicurist' => 1
    ];
}

// Verificar si un rol tiene mayor o igual nivel que otro
function hasRoleLevel($userRole, $minimumRole) {
    $hierarchy = getRoleHierarchy();
    return ($hierarchy[$userRole] ?? 0) >= ($hierarchy[$minimumRole] ?? 0);
}

// Enviar email (función básica)
function sendEmail($to, $subject, $message, $from = 'noreply@sandybeautynails.com') {
    $headers = [
        'From' => $from,
        'Reply-To' => $from,
        'X-Mailer' => 'PHP/' . phpversion(),
        'Content-Type' => 'text/html; charset=UTF-8'
    ];
    
    return mail($to, $subject, $message, implode("\r\n", array_map(
        function($k, $v) { return "$k: $v"; },
        array_keys($headers),
        $headers
    )));
}

// Logging mejorado con manejo de errores
function logActivity($message, $level = 'INFO') {
    try {
        $logFile = __DIR__ . '/../logs/system.log';
        $logDir = dirname($logFile);
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] [$level] $message" . PHP_EOL;
        
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
        
        // Para errores críticos, también registrar en error_log
        if ($level === 'ERROR') {
            error_log("Sandy Beauty Nails - $level: $message");
        }
    } catch (Exception $e) {
        // Si falla el logging, usar error_log como respaldo
        error_log("Logging failed - Original: $message | Error: " . $e->getMessage());
    }
}

// Formatear teléfono para mostrar
function formatPhone($phone) {
    if (strlen($phone) === 10) {
        return sprintf('(%s) %s-%s', 
            substr($phone, 0, 3),
            substr($phone, 3, 3),
            substr($phone, 6, 4)
        );
    }
    return $phone;
}

// Formatear precio
function formatPrice($price) {
    return '$' . number_format($price, 2);
}

// Obtener estado de cita con color
function getAppointmentStatusBadge($status) {
    $badges = [
        'pending' => '<span class="badge bg-warning">Pendiente</span>',
        'confirmed' => '<span class="badge bg-success">Confirmada</span>',
        'completed' => '<span class="badge bg-primary">Completada</span>',
        'cancelled' => '<span class="badge bg-danger">Cancelada</span>',
        'no_show' => '<span class="badge bg-secondary">No se presentó</span>'
    ];
    
    return $badges[$status] ?? '<span class="badge bg-light">Desconocido</span>';
}

// Obtener nombre de servicio
function getServiceName($serviceId) {
    $services = [
        1 => 'Manicure',
        2 => 'Pedicure', 
        3 => 'Uñas Acrílicas',
        4 => 'Manicure + Pedicure'
    ];
    
    return $services[$serviceId] ?? 'Servicio desconocido';
}
?>