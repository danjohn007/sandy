<?php
/**
 * Sistema de autenticación para Sandy Beauty Nails
 */

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/../config/database.php';

class Auth {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
        startSession();
    }
    
    // Iniciar sesión
    public function login($username, $password) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM admin_users WHERE username = ? AND status = 'active'");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                $_SESSION['admin_role'] = $user['role'];
                $_SESSION['admin_name'] = $user['name'];
                
                // Actualizar último acceso
                $this->updateLastAccess($user['id']);
                
                logActivity("Usuario {$user['username']} inició sesión");
                return ['success' => true, 'user' => $user];
            } else {
                logActivity("Intento de login fallido para: $username", 'WARNING');
                return ['success' => false, 'error' => 'Credenciales incorrectas'];
            }
        } catch (Exception $e) {
            logActivity("Error en login: " . $e->getMessage(), 'ERROR');
            return ['success' => false, 'error' => 'Error del sistema'];
        }
    }
    
    // Cerrar sesión
    public function logout() {
        if (isset($_SESSION['admin_username'])) {
            logActivity("Usuario {$_SESSION['admin_username']} cerró sesión");
        }
        
        session_destroy();
        return true;
    }
    
    // Verificar si el usuario está autenticado
    public function isLoggedIn() {
        return isset($_SESSION['admin_id']);
    }
    
    // Obtener usuario actual
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        try {
            $stmt = $this->db->prepare("SELECT * FROM admin_users WHERE id = ?");
            $stmt->execute([$_SESSION['admin_id']]);
            return $stmt->fetch();
        } catch (Exception $e) {
            logActivity("Error al obtener usuario actual: " . $e->getMessage(), 'ERROR');
            return null;
        }
    }
    
    // Verificar permisos
    public function checkPermission($requiredRoles) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        return hasPermission($_SESSION['admin_role'], $requiredRoles);
    }
    
    // Actualizar último acceso
    private function updateLastAccess($userId) {
        try {
            $stmt = $this->db->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
            $stmt->execute([$userId]);
        } catch (Exception $e) {
            logActivity("Error al actualizar último acceso: " . $e->getMessage(), 'ERROR');
        }
    }
    
    /**
     * Requerir autenticación - redirige a login si no está autenticado
     * 
     * @param string $redirectUrl URL de redirección
     */
    public function requireAuth($redirectUrl = '/admin') {
        if (!$this->isLoggedIn()) {
            header("Location: " . url($redirectUrl));
            exit();
        }
    }
    
    /**
     * Requerir rol específico - verifica autenticación y permisos
     * 
     * @param array $roles Roles permitidos (ej: ['superadmin'])
     * @param string $redirectUrl URL de redirección en caso de no tener permisos
     */
    public function requireRole($roles, $redirectUrl = '') {
        $this->requireAuth();
        
        if (!$this->checkPermission($roles)) {
            redirectWithMessage($redirectUrl ?: '/admin/dashboard', 'No tiene permisos para acceder a esta página', 'error');
        }
    }
}

// Función global para obtener instancia de Auth
function getAuth() {
    static $auth = null;
    if ($auth === null) {
        $auth = new Auth();
    }
    return $auth;
}
?>