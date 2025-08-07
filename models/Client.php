<?php
/**
 * Client Model
 */

require_once __DIR__ . '/BaseModel.php';

class Client extends BaseModel {
    protected $table = 'clients';
    
    public function findByPhone($phone) {
        $sql = "SELECT * FROM {$this->table} WHERE phone = ?";
        return $this->fetch($sql, [$phone]);
    }
    
    public function getAppointmentHistory($clientId, $limit = 10) {
        $sql = "SELECT a.*, s.name as service_name, m.name as manicurist_name 
                FROM appointments a 
                LEFT JOIN services s ON a.service_id = s.id 
                LEFT JOIN manicurists m ON a.manicurist_id = m.id 
                WHERE a.client_id = ? 
                ORDER BY a.appointment_date DESC, a.appointment_time DESC 
                LIMIT ?";
        return $this->fetchAll($sql, [$clientId, $limit]);
    }
    
    public function getFrequentClients($limit = 20) {
        $sql = "SELECT c.*, COUNT(a.id) as appointment_count,
                       SUM(a.total_amount) as total_spent
                FROM {$this->table} c
                LEFT JOIN appointments a ON c.id = a.client_id
                GROUP BY c.id
                HAVING appointment_count > 0
                ORDER BY appointment_count DESC, total_spent DESC
                LIMIT ?";
        return $this->fetchAll($sql, [$limit]);
    }
    
    public function searchClients($search) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE name LIKE ? OR phone LIKE ? OR email LIKE ?
                ORDER BY name";
        $searchTerm = "%$search%";
        return $this->fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm]);
    }
}