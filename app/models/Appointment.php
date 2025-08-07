<?php
/**
 * Appointment Model
 */

require_once __DIR__ . '/BaseModel.php';

class Appointment extends BaseModel {
    protected $table = 'appointments';
    
    public function getAppointmentsWithDetails($filters = [], $page = 1, $perPage = 20) {
        $sql = "SELECT a.*, c.name as client_name, c.phone as client_phone,
                       s.name as service_name, s.duration_minutes,
                       m.name as manicurist_name
                FROM {$this->table} a
                LEFT JOIN clients c ON a.client_id = c.id
                LEFT JOIN services s ON a.service_id = s.id
                LEFT JOIN manicurists m ON a.manicurist_id = m.id";
        
        $params = [];
        $where = [];
        
        // Apply filters
        if (!empty($filters['date_from'])) {
            $where[] = "a.appointment_date >= ?";
            $params[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $where[] = "a.appointment_date <= ?";
            $params[] = $filters['date_to'];
        }
        
        if (!empty($filters['manicurist_id'])) {
            $where[] = "a.manicurist_id = ?";
            $params[] = $filters['manicurist_id'];
        }
        
        if (!empty($filters['service_id'])) {
            $where[] = "a.service_id = ?";
            $params[] = $filters['service_id'];
        }
        
        if (!empty($filters['status'])) {
            $where[] = "a.status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        $sql .= " ORDER BY a.appointment_date DESC, a.appointment_time DESC";
        
        // Calculate pagination
        $offset = ($page - 1) * $perPage;
        $sql .= " LIMIT $perPage OFFSET $offset";
        
        $appointments = $this->fetchAll($sql, $params);
        
        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM {$this->table} a";
        if (!empty($where)) {
            $countSql .= " WHERE " . implode(' AND ', $where);
        }
        $total = $this->fetch($countSql, $params)['total'];
        
        return [
            'data' => $appointments,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage)
        ];
    }
    
    public function getTodaysAppointments() {
        $today = date('Y-m-d');
        $sql = "SELECT a.*, c.name as client_name, c.phone as client_phone,
                       s.name as service_name, m.name as manicurist_name
                FROM {$this->table} a
                LEFT JOIN clients c ON a.client_id = c.id
                LEFT JOIN services s ON a.service_id = s.id
                LEFT JOIN manicurists m ON a.manicurist_id = m.id
                WHERE a.appointment_date = ?
                ORDER BY a.appointment_time ASC";
        return $this->fetchAll($sql, [$today]);
    }
    
    public function getUpcomingAppointments($days = 7) {
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime("+$days days"));
        
        $sql = "SELECT a.*, c.name as client_name, c.phone as client_phone,
                       s.name as service_name, m.name as manicurist_name
                FROM {$this->table} a
                LEFT JOIN clients c ON a.client_id = c.id
                LEFT JOIN services s ON a.service_id = s.id
                LEFT JOIN manicurists m ON a.manicurist_id = m.id
                WHERE a.appointment_date BETWEEN ? AND ?
                AND a.status NOT IN ('cancelled', 'completed')
                ORDER BY a.appointment_date ASC, a.appointment_time ASC";
        return $this->fetchAll($sql, [$startDate, $endDate]);
    }
    
    public function getRevenueStats($period = 'month') {
        $dateFilter = '';
        $params = [];
        
        switch ($period) {
            case 'today':
                $dateFilter = "WHERE a.appointment_date = ?";
                $params[] = date('Y-m-d');
                break;
            case 'week':
                $dateFilter = "WHERE a.appointment_date >= ? AND a.appointment_date <= ?";
                $params[] = date('Y-m-d', strtotime('monday this week'));
                $params[] = date('Y-m-d', strtotime('sunday this week'));
                break;
            case 'month':
                $dateFilter = "WHERE YEAR(a.appointment_date) = ? AND MONTH(a.appointment_date) = ?";
                $params[] = date('Y');
                $params[] = date('m');
                break;
            case 'year':
                $dateFilter = "WHERE YEAR(a.appointment_date) = ?";
                $params[] = date('Y');
                break;
        }
        
        $sql = "SELECT COUNT(*) as total_appointments,
                       SUM(CASE WHEN a.status = 'paid' OR a.status = 'completed' THEN a.total_amount ELSE 0 END) as total_revenue,
                       AVG(CASE WHEN a.status = 'paid' OR a.status = 'completed' THEN a.total_amount ELSE NULL END) as avg_revenue
                FROM {$this->table} a
                $dateFilter";
        
        return $this->fetch($sql, $params);
    }
    
    public function isSlotAvailable($date, $time, $manicuristId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE appointment_date = ? AND appointment_time = ? 
                AND status NOT IN ('cancelled')";
        $params = [$date, $time];
        
        if ($manicuristId) {
            $sql .= " AND manicurist_id = ?";
            $params[] = $manicuristId;
        }
        
        $result = $this->fetch($sql, $params);
        return $result['count'] == 0;
    }
    
    public function updateStatus($id, $status) {
        return $this->update($id, ['status' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
    }
}