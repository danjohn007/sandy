<?php
/**
 * Appointment Controller (Admin)
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../models/Service.php';
require_once __DIR__ . '/../models/Manicurist.php';

class AppointmentController extends BaseController {
    private $appointmentModel;
    private $serviceModel;
    private $manicuristModel;
    
    public function __construct() {
        parent::__construct();
        $this->appointmentModel = new Appointment();
        $this->serviceModel = new Service();
        $this->manicuristModel = new Manicurist();
    }
    
    public function index() {
        $this->requireAuth();
        
        // Get filters
        $filters = [
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? '',
            'manicurist_id' => $_GET['manicurist_id'] ?? '',
            'service_id' => $_GET['service_id'] ?? '',
            'status' => $_GET['status'] ?? ''
        ];
        
        // If manicurist role, filter by their appointments only
        if ($_SESSION['admin_role'] === 'manicurist' && $_SESSION['admin_manicurist_id']) {
            $filters['manicurist_id'] = $_SESSION['admin_manicurist_id'];
        }
        
        $page = (int)($_GET['page'] ?? 1);
        $perPage = 20;
        
        // Get appointments with pagination and safe defaults
        $appointments = $this->appointmentModel->getAppointmentsWithDetails($filters, $page, $perPage);
        if (!is_array($appointments)) {
            $appointments = [];
        }
        
        // Get filter options with safe defaults
        $services = $this->serviceModel->getActiveServices();
        if (!is_array($services)) {
            $services = [];
        }
        
        $manicurists = $this->manicuristModel->getActiveManicurists();
        if (!is_array($manicurists)) {
            $manicurists = [];
        }
        
        $this->view('admin/appointments/index', [
            'title' => 'Citas - Administración',
            'appointments' => $appointments,
            'filters' => $filters,
            'services' => $services,
            'manicurists' => $manicurists
        ]);
    }
    
    public function show() {
        $this->requireAuth();
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirect('/admin/appointments');
            return;
        }
        
        // Get appointment with details
        $appointment = $this->db->fetch(
            "SELECT a.*, c.name as client_name, c.phone as client_phone, c.email as client_email,
                    s.name as service_name, s.duration_minutes, s.description as service_description,
                    m.name as manicurist_name, m.phone as manicurist_phone
             FROM appointments a
             LEFT JOIN clients c ON a.client_id = c.id
             LEFT JOIN services s ON a.service_id = s.id
             LEFT JOIN manicurists m ON a.manicurist_id = m.id
             WHERE a.id = ?",
            [$id]
        );
        
        if (!$appointment) {
            $this->redirect('/admin/appointments');
            return;
        }
        
        // Check if manicurist can view this appointment
        if ($_SESSION['admin_role'] === 'manicurist' && 
            $_SESSION['admin_manicurist_id'] != $appointment['manicurist_id']) {
            $this->redirect('/admin/appointments');
            return;
        }
        
        $this->view('admin/appointments/show', [
            'title' => 'Detalle de Cita - Administración',
            'appointment' => $appointment
        ]);
    }
    
    public function updateStatus() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Method not allowed'], 405);
            return;
        }
        
        $id = $_GET['id'] ?? null;
        $input = json_decode(file_get_contents('php://input'), true);
        $status = $input['status'] ?? '';
        
        if (!$id || !$status) {
            $this->json(['success' => false, 'message' => 'ID and status are required'], 400);
            return;
        }
        
        // Validate status
        $validStatuses = ['pending', 'confirmed', 'paid', 'completed', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            $this->json(['success' => false, 'message' => 'Invalid status'], 400);
            return;
        }
        
        // Get appointment to check permissions
        $appointment = $this->appointmentModel->find($id);
        if (!$appointment) {
            $this->json(['success' => false, 'message' => 'Appointment not found'], 404);
            return;
        }
        
        // Check if manicurist can update this appointment
        if ($_SESSION['admin_role'] === 'manicurist' && 
            $_SESSION['admin_manicurist_id'] != $appointment['manicurist_id']) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
            return;
        }
        
        // Update status
        if ($this->appointmentModel->updateStatus($id, $status)) {
            $this->json(['success' => true, 'message' => 'Status updated successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to update status'], 500);
        }
    }
}