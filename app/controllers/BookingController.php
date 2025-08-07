<?php
/**
 * Booking Controller
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Client.php';
require_once __DIR__ . '/../models/Service.php';
require_once __DIR__ . '/../models/Manicurist.php';
require_once __DIR__ . '/../models/Appointment.php';

class BookingController extends BaseController {
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
    
    public function index() {
        // Check if booking is allowed (Monday to Saturday, 8 AM to 7 PM)
        $currentDay = date('N'); // 1=Monday, 7=Sunday
        $currentHour = (int)date('H');
        
        if ($currentDay == 7 || $currentHour < 8 || $currentHour >= 19) {
            $this->view('booking/closed', [
                'title' => 'Reservas Cerradas',
                'message' => 'Las reservas están disponibles de lunes a sábado de 8:00 AM a 7:00 PM'
            ]);
            return;
        }
        
        // Get services and manicurists with safe defaults
        $services = $this->serviceModel->getActiveServices();
        $manicurists = $this->manicuristModel->getActiveManicurists();
        
        // Ensure arrays are properly initialized
        if (!is_array($services)) {
            $services = [];
        }
        
        if (!is_array($manicurists)) {
            $manicurists = [];
        }
        
        $this->view('booking/form', [
            'title' => 'Reservar Cita',
            'services' => $services,
            'manicurists' => $manicurists
        ]);
    }
    
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/book');
            return;
        }
        
        // Sanitize input
        $input = $this->sanitizeInput($_POST);
        
        // Validate input
        $rules = [
            'phone' => ['required', 'phone'],
            'name' => ['required'],
            'email' => ['email'],
            'service_id' => ['required'],
            'appointment_date' => ['required'],
            'appointment_time' => ['required']
        ];
        
        $errors = $this->validateInput($rules, $input);
        
        if (!empty($errors)) {
            $_SESSION['booking_errors'] = $errors;
            $_SESSION['booking_data'] = $input;
            $this->redirect('/book');
            return;
        }
        
        // Additional validations
        $appointmentDate = $input['appointment_date'];
        $appointmentTime = $input['appointment_time'];
        $manicuristId = !empty($input['manicurist_id']) ? $input['manicurist_id'] : null;
        
        // Check if appointment date is in the future
        if (strtotime($appointmentDate) < strtotime(date('Y-m-d'))) {
            $_SESSION['booking_errors'] = ['appointment_date' => ['La fecha debe ser futura']];
            $_SESSION['booking_data'] = $input;
            $this->redirect('/book');
            return;
        }
        
        // Check if slot is available
        if (!$this->appointmentModel->isSlotAvailable($appointmentDate, $appointmentTime, $manicuristId)) {
            $_SESSION['booking_errors'] = ['appointment_time' => ['Este horario no está disponible']];
            $_SESSION['booking_data'] = $input;
            $this->redirect('/book');
            return;
        }
        
        try {
            // Find or create client
            $client = $this->clientModel->findByPhone($input['phone']);
            
            if (!$client) {
                $clientId = $this->clientModel->create([
                    'phone' => $input['phone'],
                    'name' => $input['name'],
                    'email' => $input['email'] ?? null,
                    'cedula' => $input['cedula'] ?? null
                ]);
            } else {
                $clientId = $client['id'];
                // Update client info if provided
                $updateData = [];
                if (!empty($input['name']) && $input['name'] !== $client['name']) {
                    $updateData['name'] = $input['name'];
                }
                if (!empty($input['email']) && $input['email'] !== $client['email']) {
                    $updateData['email'] = $input['email'];
                }
                if (!empty($input['cedula']) && $input['cedula'] !== $client['cedula']) {
                    $updateData['cedula'] = $input['cedula'];
                }
                if (!empty($updateData)) {
                    $this->clientModel->update($clientId, $updateData);
                }
            }
            
            // Get service price
            $service = $this->serviceModel->find($input['service_id']);
            if (!$service) {
                throw new Exception('Servicio no encontrado');
            }
            
            // Create appointment
            $appointmentData = [
                'client_id' => $clientId,
                'service_id' => $input['service_id'],
                'manicurist_id' => $manicuristId,
                'appointment_date' => $appointmentDate,
                'appointment_time' => $appointmentTime,
                'total_amount' => $service['price'],
                'status' => 'pending',
                'notes' => $input['notes'] ?? null
            ];
            
            $appointmentId = $this->appointmentModel->create($appointmentData);
            
            // Store appointment ID in session for payment
            $_SESSION['pending_appointment_id'] = $appointmentId;
            
            // Redirect to payment or success page
            $this->redirect('/book/success?id=' . $appointmentId);
            
        } catch (Exception $e) {
            $_SESSION['booking_errors'] = ['general' => [$e->getMessage()]];
            $_SESSION['booking_data'] = $input;
            $this->redirect('/book');
        }
    }
    
    public function success() {
        $appointmentId = $_GET['id'] ?? null;
        
        if (!$appointmentId) {
            $this->redirect('/book');
            return;
        }
        
        // Get appointment details
        $appointment = $this->appointmentModel->find($appointmentId);
        if (!$appointment) {
            $this->redirect('/book');
            return;
        }
        
        // Get related data
        $client = $this->clientModel->find($appointment['client_id']);
        $service = $this->serviceModel->find($appointment['service_id']);
        $manicurist = null;
        if ($appointment['manicurist_id']) {
            $manicurist = $this->manicuristModel->find($appointment['manicurist_id']);
        }
        
        $this->view('booking/success', [
            'title' => 'Reserva Confirmada',
            'appointment' => $appointment,
            'client' => $client,
            'service' => $service,
            'manicurist' => $manicurist
        ]);
    }
    
    public function cancel() {
        $this->view('booking/cancel', [
            'title' => 'Reserva Cancelada'
        ]);
    }
}