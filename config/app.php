<?php
/**
 * Configuración de aplicación para Sandy Beauty Nails
 */

// Configuración general de la aplicación
define('APP_NAME', 'Sandy Beauty Nails');
define('APP_VERSION', '1.0.0');
define('APP_DESCRIPTION', 'Sistema de gestión de citas para salón de belleza');

// URLs y rutas base
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$basePath = dirname($_SERVER['SCRIPT_NAME'] ?? '/');

// Remover /public del path si existe
if (substr($basePath, -7) === '/public') {
    $basePath = substr($basePath, 0, -7);
}

// Constantes de URL
define('BASE_URL', rtrim($protocol . '://' . $host . $basePath, '/'));
define('BASE_PATH', rtrim($basePath, '/'));

// Función global para construcción de URLs
function url($path = '') {
    return BASE_URL . '/' . ltrim($path, '/');
}

// Timezone
date_default_timezone_set('America/Mexico_City');

// Configuración de errores (solo en desarrollo)
$environment = getenv('APP_ENV') ?: 'development';
if ($environment === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}

// Configuración de sesión
ini_set('session.gc_maxlifetime', 7200); // 2 horas
ini_set('session.cookie_lifetime', 7200);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));

// Configuración de la aplicación Sandy
$app_config = [
    'name' => APP_NAME,
    'version' => APP_VERSION,
    'description' => APP_DESCRIPTION,
    'environment' => $environment,
    'timezone' => 'America/Mexico_City',
    
    // Configuración de horarios del salón
    'business_hours' => [
        'monday' => ['start' => '08:00', 'end' => '19:00'],
        'tuesday' => ['start' => '08:00', 'end' => '19:00'],
        'wednesday' => ['start' => '08:00', 'end' => '19:00'],
        'thursday' => ['start' => '08:00', 'end' => '19:00'],
        'friday' => ['start' => '08:00', 'end' => '19:00'],
        'saturday' => ['start' => '08:00', 'end' => '19:00'],
        'sunday' => null // Cerrado los domingos
    ],
    
    // Duración de servicios (en minutos)
    'service_durations' => [
        1 => 60,  // Manicure
        2 => 90,  // Pedicure
        3 => 120, // Uñas Acrílicas
        4 => 150  // Manicure + Pedicure
    ],
    
    // Roles del sistema
    'roles' => [
        'superadmin' => 'SuperAdmin',
        'manicurist' => 'Manicurista'
    ],
    
    // Estados de citas
    'appointment_status' => [
        'pending' => 'Pendiente',
        'confirmed' => 'Confirmada',
        'completed' => 'Completada',
        'cancelled' => 'Cancelada',
        'no_show' => 'No se presentó'
    ]
];

// Hacer configuración disponible globalmente
$GLOBALS['app_config'] = $app_config;

// Función helper para obtener configuración
function app_config($key = null, $default = null) {
    global $app_config;
    
    if ($key === null) {
        return $app_config;
    }
    
    return $app_config[$key] ?? $default;
}

// Función helper para obtener horarios de negocio
function getBusinessHours($day = null) {
    $hours = app_config('business_hours');
    
    if ($day === null) {
        return $hours;
    }
    
    return $hours[strtolower($day)] ?? null;
}

// Función helper para verificar si está abierto
function isBusinessOpen($datetime = null) {
    if ($datetime === null) {
        $datetime = new DateTime();
    } elseif (is_string($datetime)) {
        $datetime = new DateTime($datetime);
    }
    
    $dayName = strtolower($datetime->format('l'));
    $hours = getBusinessHours($dayName);
    
    if ($hours === null) {
        return false; // Cerrado este día
    }
    
    $currentTime = $datetime->format('H:i');
    return $currentTime >= $hours['start'] && $currentTime <= $hours['end'];
}
?>