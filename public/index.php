<?php
/**
 * Main entry point for Sandy Beauty Nails
 */

// Start session
session_start();

// Error reporting (disable in production)
if (getenv('APP_ENV') !== 'production') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Autoload dependencies
require_once __DIR__ . '/../config/database.php';

// Load base controller and models
require_once __DIR__ . '/../controllers/BaseController.php';
require_once __DIR__ . '/../models/BaseModel.php';

// Load router
$router = require_once __DIR__ . '/../routes/web.php';

// Get current URI and method
$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Remove base path if running in subdirectory
if (strpos($uri, '/sandy/') === 0) {
    $uri = substr($uri, 6); // Remove '/sandy'
}

// Dispatch request
$router->dispatch($uri, $method);