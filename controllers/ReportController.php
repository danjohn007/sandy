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
        
        // Get revenue stats for different periods
        $todayStats = $this->appointmentModel->getRevenueStats('today');
        $weekStats = $this->appointmentModel->getRevenueStats('week');
        $monthStats = $this->appointmentModel->getRevenueStats('month');
        $yearStats = $this->appointmentModel->getRevenueStats('year');
        
        // Get revenue by service
        $serviceStats = $this->serviceModel->getServiceStats();
        
        // Get revenue by manicurist
        $manicuristStats = $this->manicuristModel->getManicuristStats();
        
        // Get monthly revenue trend (last 12 months)
        $monthlyTrend = $this->db->fetchAll(
            "SELECT DATE_FORMAT(appointment_date, '%Y-%m') as month,
                    COUNT(*) as appointments,
                    SUM(CASE WHEN status IN ('paid', 'completed') THEN total_amount ELSE 0 END) as revenue
             FROM appointments 
             WHERE appointment_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
             GROUP BY DATE_FORMAT(appointment_date, '%Y-%m')
             ORDER BY month DESC"
        );
        
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
        
        // Get appointments by hour (most popular times)
        $hourlyStats = $this->db->fetchAll(
            "SELECT HOUR(appointment_time) as hour, COUNT(*) as count
             FROM appointments 
             WHERE appointment_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
             GROUP BY HOUR(appointment_time)
             ORDER BY hour"
        );
        
        // Get appointments by day of week
        $weeklyStats = $this->db->fetchAll(
            "SELECT DAYNAME(appointment_date) as day_name, 
                    DAYOFWEEK(appointment_date) as day_num,
                    COUNT(*) as count
             FROM appointments 
             WHERE appointment_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
             GROUP BY DAYOFWEEK(appointment_date), DAYNAME(appointment_date)
             ORDER BY day_num"
        );
        
        // Get appointments by status
        $statusStats = $this->db->fetchAll(
            "SELECT status, COUNT(*) as count
             FROM appointments 
             WHERE appointment_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
             GROUP BY status
             ORDER BY count DESC"
        );
        
        $this->view('admin/reports/charts', [
            'title' => 'Gráficos y Estadísticas - Administración',
            'hourlyStats' => $hourlyStats,
            'weeklyStats' => $weeklyStats,
            'statusStats' => $statusStats
        ]);
    }
}