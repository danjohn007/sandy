<?php
/**
 * Report Controller (SuperAdmin only)
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../models/Service.php';
require_once __DIR__ . '/../models/Manicurist.php';

class ReportController extends BaseController {
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
        $this->requireSuperAdmin();
        
        $this->view('admin/reports/index', [
            'title' => 'Reportes - Administración'
        ]);
    }
    
    public function finance() {
        $this->requireSuperAdmin();
        
        $period = $_GET['period'] ?? 'month';
        
        // Get revenue stats for different periods with safe defaults
        $todayStats = $this->appointmentModel->getRevenueStats('today');
        $weekStats = $this->appointmentModel->getRevenueStats('week');
        $monthStats = $this->appointmentModel->getRevenueStats('month');
        $yearStats = $this->appointmentModel->getRevenueStats('year');
        
        // Initialize with safe defaults
        $todayStats = $this->initializeStats($todayStats);
        $weekStats = $this->initializeStats($weekStats);
        $monthStats = $this->initializeStats($monthStats);
        $yearStats = $this->initializeStats($yearStats);
        
        // Get revenue by service with safe defaults
        $serviceStats = $this->serviceModel->getServiceStats();
        if (!is_array($serviceStats)) {
            $serviceStats = [];
        }
        
        // Get revenue by manicurist with safe defaults
        $manicuristStats = $this->manicuristModel->getManicuristStats();
        if (!is_array($manicuristStats)) {
            $manicuristStats = [];
        }
        
        // Get monthly revenue trend (last 12 months) with safe defaults
        $monthlyTrend = $this->db->fetchAll(
            "SELECT DATE_FORMAT(appointment_date, '%Y-%m') as month,
                    COUNT(*) as appointments,
                    SUM(CASE WHEN status IN ('paid', 'completed') THEN total_amount ELSE 0 END) as revenue
             FROM appointments 
             WHERE appointment_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
             GROUP BY DATE_FORMAT(appointment_date, '%Y-%m')
             ORDER BY month DESC"
        );
        if (!is_array($monthlyTrend)) {
            $monthlyTrend = [];
        }
        
        $this->view('admin/reports/finance', [
            'title' => 'Reportes Financieros - Administración',
            'todayStats' => $todayStats,
            'weekStats' => $weekStats,
            'monthStats' => $monthStats,
            'yearStats' => $yearStats,
            'serviceStats' => $serviceStats,
            'manicuristStats' => $manicuristStats,
            'monthlyTrend' => $monthlyTrend
        ]);
    }
    
    public function charts() {
        $this->requireSuperAdmin();
        
        // Get appointments by hour (most popular times) with safe defaults
        $hourlyStats = $this->db->fetchAll(
            "SELECT HOUR(appointment_time) as hour, COUNT(*) as count
             FROM appointments 
             WHERE appointment_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
             GROUP BY HOUR(appointment_time)
             ORDER BY hour"
        );
        if (!is_array($hourlyStats)) {
            $hourlyStats = [];
        }
        
        // Get appointments by day of week with safe defaults
        $weeklyStats = $this->db->fetchAll(
            "SELECT DAYNAME(appointment_date) as day_name, 
                    DAYOFWEEK(appointment_date) as day_num,
                    COUNT(*) as count
             FROM appointments 
             WHERE appointment_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
             GROUP BY DAYOFWEEK(appointment_date), DAYNAME(appointment_date)
             ORDER BY day_num"
        );
        if (!is_array($weeklyStats)) {
            $weeklyStats = [];
        }
        
        // Get appointments by status with safe defaults
        $statusStats = $this->db->fetchAll(
            "SELECT status, COUNT(*) as count
             FROM appointments 
             WHERE appointment_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
             GROUP BY status
             ORDER BY count DESC"
        );
        if (!is_array($statusStats)) {
            $statusStats = [];
        }
        
        $this->view('admin/reports/charts', [
            'title' => 'Gráficos y Estadísticas - Administración',
            'hourlyStats' => $hourlyStats,
            'weeklyStats' => $weeklyStats,
            'statusStats' => $statusStats
        ]);
    }
    
    /**
     * Initialize statistics array with safe defaults
     */
    private function initializeStats($stats) {
        if (!is_array($stats)) {
            $stats = [];
        }
        
        return [
            'total_appointments' => $stats['total_appointments'] ?? 0,
            'total_revenue' => $stats['total_revenue'] ?? 0.00,
            'avg_revenue' => $stats['avg_revenue'] ?? 0.00
        ];
    }
}