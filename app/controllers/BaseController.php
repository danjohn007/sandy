<?php
/**
 * Base Controller class for Sandy Beauty Nails
 */

abstract class BaseController {
    protected $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    protected function view($viewName, $data = []) {
        // Extract data for use in view
        extract($data);
        
        // Start output buffering
        ob_start();
        
        // Include the view file
        $viewPath = __DIR__ . '/../views/' . $viewName . '.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            throw new Exception("View not found: $viewName");
        }
        
        // Get content and clean buffer
        $content = ob_get_clean();
        
        // Include layout if not AJAX request
        if (!$this->isAjaxRequest()) {
            $this->includeLayout($content, $data);
        } else {
            echo $content;
        }
    }
    
    protected function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
    
    protected function redirect($url) {
        header("Location: $url");
        exit;
    }
    
    protected function isAjaxRequest() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    protected function requireAuth() {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect('/admin');
            exit;
        }
    }
    
    protected function requireSuperAdmin() {
        $this->requireAuth();
        if ($_SESSION['admin_role'] !== 'superadmin') {
            $this->redirect('/admin/dashboard');
            exit;
        }
    }
    
    private function includeLayout($content, $data = []) {
        extract($data);
        include __DIR__ . '/../views/layout/main.php';
    }
    
    protected function validateInput($rules, $data) {
        $errors = [];
        
        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            
            foreach ($fieldRules as $rule) {
                switch ($rule) {
                    case 'required':
                        if (empty($value)) {
                            $errors[$field][] = ucfirst($field) . ' is required';
                        }
                        break;
                    case 'email':
                        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field][] = ucfirst($field) . ' must be a valid email';
                        }
                        break;
                    case 'phone':
                        if (!empty($value) && !preg_match('/^\d{3}-\d{3}-\d{4}$/', $value)) {
                            $errors[$field][] = ucfirst($field) . ' must be in format XXX-XXX-XXXX';
                        }
                        break;
                }
            }
        }
        
        return $errors;
    }
    
    protected function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitizeInput'], $data);
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
}