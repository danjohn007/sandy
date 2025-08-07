<?php
/**
 * Admin Controller
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Appointment.php';

class AdminController extends BaseController {
    private $appointmentModel;
    
    public function __construct() {
        parent::__construct();
        $this->appointmentModel = new Appointment();
    }
    
    public function login() {
        if (isset($_SESSION['admin_id'])) {
            $this->redirect('/admin/dashboard');
            return;
        }
        
        $this->view('admin/login', [
            'title' => 'Administración - Login'
        ]);
    }
    
    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin');
            return;
        }
        
        $username = $this->sanitizeInput($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            $_SESSION['login_error'] = 'Usuario y contraseña son requeridos';
            $this->redirect('/admin');
            return;
        }
        
        // Check admin user
        $admin = $this->db->fetch(
            "SELECT au.*, m.name as manicurist_name 
             FROM admin_users au 
             LEFT JOIN manicurists m ON au.manicurist_id = m.id 
             WHERE au.username = ? AND au.is_active = 1",
            [$username]
        );
        
        if (!$admin || !password_verify($password, $admin['password'])) {
            $_SESSION['login_error'] = 'Credenciales incorrectas';
            $this->redirect('/admin');
            return;
        }
        
        // Set session
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_role'] = $admin['role'];
        $_SESSION['admin_manicurist_id'] = $admin['manicurist_id'];
        $_SESSION['admin_manicurist_name'] = $admin['manicurist_name'];
        
        // Update last login
        $this->db->query(
            "UPDATE admin_users SET last_login = NOW() WHERE id = ?",
            [$admin['id']]
        );
        
        $this->redirect('/admin/dashboard');
    }
    
    public function dashboard() {
        $this->requireAuth();
        
        // Determine which dashboard to show based on role
        $role = $_SESSION['admin_role'] ?? 'manicurist';
        
        // Get dashboard statistics
        $todayStats = $this->appointmentModel->getRevenueStats('today');
        $weekStats = $this->appointmentModel->getRevenueStats('week');
        $monthStats = $this->appointmentModel->getRevenueStats('month');
        
        // Get today's appointments
        $todayAppointments = $this->appointmentModel->getTodaysAppointments();
        
        // Get upcoming appointments
        $upcomingAppointments = $this->appointmentModel->getUpcomingAppointments(7);
        
        // For manicurists, filter to their own appointments
        if ($role === 'manicurist' && isset($_SESSION['admin_manicurist_id'])) {
            $todayAppointments = array_filter($todayAppointments, function($appointment) {
                return $appointment['manicurist_id'] == $_SESSION['admin_manicurist_id'];
            });
            
            $upcomingAppointments = array_filter($upcomingAppointments, function($appointment) {
                return $appointment['manicurist_id'] == $_SESSION['admin_manicurist_id'];
            });
        }
        
        // Choose the appropriate dashboard view
        $dashboardView = $role === 'superadmin' ? 'dashboards/admin' : 'dashboards/manicurist';
        
        $this->view($dashboardView, [
            'title' => 'Dashboard - Administración',
            'todayStats' => $todayStats,
            'weekStats' => $weekStats,
            'monthStats' => $monthStats,
            'todayAppointments' => $todayAppointments,
            'upcomingAppointments' => $upcomingAppointments,
            'nextAppointment' => !empty($upcomingAppointments) ? $upcomingAppointments[0] : null
        ]);
    }
    
    public function logout() {
        session_destroy();
        $this->redirect('/admin');
    }
}