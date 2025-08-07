<?php
/**
 * API Controller for AJAX requests
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Client.php';
require_once __DIR__ . '/../models/Service.php';
require_once __DIR__ . '/../models/Manicurist.php';
require_once __DIR__ . '/../models/Appointment.php';

class ApiController extends BaseController {
    private $clientModel;
    private $serviceModel;
    private $manicuristModel;
    private $appointmentModel;
    
    public function __construct() {
        parent::__construct();
        $this->clientModel = new Client();
        $this->serviceModel = new Service();
        $this->manicuristModel = new Manicurist();
        $this->appointmentModel = new Appointment();
    }
    
    public function getServices() {
        $services = $this->serviceModel->getActiveServices();
        $this->json(['success' => true, 'data' => $services]);
    }
    
    public function getManicurists() {
        $manicurists = $this->manicuristModel->getActiveManicurists();
        $this->json(['success' => true, 'data' => $manicurists]);
    }
    
    public function getAvailableSlots() {
        $date = $_GET['date'] ?? null;
        $serviceId = $_GET['service_id'] ?? null;
        
        if (!$date) {
            $this->json(['success' => false, 'message' => 'Date is required'], 400);
            return;
        }
        
        // Validate date format
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $this->json(['success' => false, 'message' => 'Invalid date format'], 400);
            return;
        }
        
        // Check if date is in the past
        if (strtotime($date) < strtotime(date('Y-m-d'))) {
            $this->json(['success' => false, 'message' => 'Date cannot be in the past'], 400);
            return;
        }
        
        // Check if date is within business days (Monday to Saturday)
        $dayOfWeek = date('N', strtotime($date));
        if ($dayOfWeek == 7) { // Sunday
            $this->json(['success' => false, 'message' => 'Bookings not available on Sundays'], 400);
            return;
        }
        
        $availableSlots = $this->manicuristModel->getAvailableSlots($date, $serviceId);
        $this->json(['success' => true, 'data' => $availableSlots]);
    }
    
    public function getClientByPhone() {
        $phone = $_GET['phone'] ?? null;
        
        if (!$phone) {
            $this->json(['success' => false, 'message' => 'Phone is required'], 400);
            return;
        }
        
        $client = $this->clientModel->findByPhone($phone);
        
        if ($client) {
            $this->json(['success' => true, 'data' => $client]);
        } else {
            $this->json(['success' => false, 'message' => 'Client not found'], 404);
        }
    }
    
    public function validateSlot() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Method not allowed'], 405);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        $date = $input['date'] ?? null;
        $time = $input['time'] ?? null;
        $manicuristId = $input['manicurist_id'] ?? null;
        
        if (!$date || !$time) {
            $this->json(['success' => false, 'message' => 'Date and time are required'], 400);
            return;
        }
        
        $isAvailable = $this->appointmentModel->isSlotAvailable($date, $time, $manicuristId);
        
        $this->json([
            'success' => true,
            'available' => $isAvailable,
            'message' => $isAvailable ? 'Slot is available' : 'Slot is not available'
        ]);
    }
}