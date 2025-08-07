<?php
/**
 * URL Router for Sandy Beauty Nails
 */

class Router {
    private $routes = [];
    
    public function get($pattern, $controller, $action = 'index') {
        $this->routes['GET'][] = [
            'pattern' => $pattern,
            'controller' => $controller,
            'action' => $action
        ];
    }
    
    public function post($pattern, $controller, $action = 'store') {
        $this->routes['POST'][] = [
            'pattern' => $pattern,
            'controller' => $controller,
            'action' => $action
        ];
    }
    
    public function put($pattern, $controller, $action = 'update') {
        $this->routes['PUT'][] = [
            'pattern' => $pattern,
            'controller' => $controller,
            'action' => $action
        ];
    }
    
    public function delete($pattern, $controller, $action = 'delete') {
        $this->routes['DELETE'][] = [
            'pattern' => $pattern,
            'controller' => $controller,
            'action' => $action
        ];
    }
    
    public function dispatch($uri, $method = 'GET') {
        // Remove query string
        $uri = parse_url($uri, PHP_URL_PATH);
        
        // Remove leading/trailing slashes
        $uri = trim($uri, '/');
        
        if (!isset($this->routes[$method])) {
            $this->show404();
            return;
        }
        
        foreach ($this->routes[$method] as $route) {
            if ($this->matchRoute($route['pattern'], $uri)) {
                $this->callController($route['controller'], $route['action'], $uri);
                return;
            }
        }
        
        $this->show404();
    }
    
    private function matchRoute($pattern, $uri) {
        // Convert pattern to regex
        $pattern = str_replace('/', '\/', $pattern);
        $pattern = preg_replace('/\{([^}]+)\}/', '([^\/]+)', $pattern);
        $pattern = '/^' . $pattern . '$/';
        
        return preg_match($pattern, $uri);
    }
    
    private function callController($controllerName, $action, $uri) {
        $controllerFile = __DIR__ . '/../app/controllers/' . $controllerName . '.php';
        
        if (!file_exists($controllerFile)) {
            $this->show404();
            return;
        }
        
        require_once $controllerFile;
        
        if (!class_exists($controllerName)) {
            $this->show404();
            return;
        }
        
        $controller = new $controllerName();
        
        if (!method_exists($controller, $action)) {
            $this->show404();
            return;
        }
        
        // Extract parameters from URI
        $params = $this->extractParams($uri);
        call_user_func_array([$controller, $action], $params);
    }
    
    private function extractParams($uri) {
        $parts = explode('/', $uri);
        return array_slice($parts, 1); // Remove first empty element
    }
    
    private function show404() {
        http_response_code(404);
        echo "404 - Page Not Found";
    }
}

// Initialize router
$router = new Router();

// Public routes
$router->get('', 'HomeController', 'index');
$router->get('book', 'BookingController', 'index');
$router->post('book', 'BookingController', 'store');
$router->get('book/success', 'BookingController', 'success');
$router->get('book/cancel', 'BookingController', 'cancel');

// API routes
$router->get('api/services', 'ApiController', 'getServices');
$router->get('api/manicurists', 'ApiController', 'getManicurists');
$router->get('api/available-slots', 'ApiController', 'getAvailableSlots');
$router->get('api/client/{phone}', 'ApiController', 'getClientByPhone');
$router->post('api/validate-slot', 'ApiController', 'validateSlot');

// Admin routes
$router->get('admin', 'AdminController', 'login');
$router->post('admin/login', 'AdminController', 'authenticate');
$router->get('admin/dashboard', 'AdminController', 'dashboard');
$router->get('admin/logout', 'AdminController', 'logout');

// Admin - Appointments
$router->get('admin/appointments', 'AppointmentController', 'index');
$router->get('admin/appointments/{id}', 'AppointmentController', 'show');
$router->put('admin/appointments/{id}/status', 'AppointmentController', 'updateStatus');

// Admin - Clients
$router->get('admin/clients', 'ClientController', 'index');
$router->get('admin/clients/{id}', 'ClientController', 'show');

// Admin - Reports (SuperAdmin only)
$router->get('admin/reports', 'ReportController', 'index');
$router->get('admin/reports/finance', 'ReportController', 'finance');
$router->get('admin/reports/charts', 'ReportController', 'charts');

return $router;