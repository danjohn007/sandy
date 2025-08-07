<?php
/**
 * Service Model
 */

require_once __DIR__ . '/BaseModel.php';

class Service extends BaseModel {
    protected $table = 'services';
    
    public function getActiveServices() {
        return $this->findAll(['is_active' => 1], 'name ASC');
    }
    
    public function getServiceStats() {
        $sql = "SELECT s.name, COUNT(a.id) as booking_count, 
                       SUM(a.total_amount) as total_revenue
                FROM {$this->table} s
                LEFT JOIN appointments a ON s.id = a.service_id
                WHERE s.is_active = 1
                GROUP BY s.id, s.name
                ORDER BY booking_count DESC";
        return $this->fetchAll($sql);
    }
}