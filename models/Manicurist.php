<?php
/**
 * Manicurist Model
 */

require_once __DIR__ . '/BaseModel.php';

class Manicurist extends BaseModel {
    protected $table = 'manicurists';
    
    public function getActiveManicurists() {
        return $this->findAll(['is_active' => 1], 'name ASC');
    }
    
    public function getAvailableSlots($date, $serviceId = null) {
        // Get business hours for the day
        $dayOfWeek = date('N', strtotime($date)); // 1=Monday, 7=Sunday
        
        $businessHours = $this->fetch(
            "SELECT * FROM business_hours WHERE day_of_week = ? AND is_active = 1",
            [$dayOfWeek]
        );
        
        if (!$businessHours) {
            return []; // No business hours for this day
        }
        
        // Get service duration
        $serviceDuration = 60; // Default 60 minutes
        if ($serviceId) {
            $service = $this->fetch("SELECT duration_minutes FROM services WHERE id = ?", [$serviceId]);
            if ($service) {
                $serviceDuration = $service['duration_minutes'];
            }
        }
        
        // Generate time slots
        $openTime = strtotime($businessHours['open_time']);
        $closeTime = strtotime($businessHours['close_time']);
        $slotDuration = $serviceDuration * 60; // Convert to seconds
        
        $slots = [];
        for ($time = $openTime; $time < $closeTime; $time += $slotDuration) {
            $slots[] = date('H:i:s', $time);
        }
        
        // Get occupied slots
        $occupiedSlots = $this->fetchAll(
            "SELECT appointment_time, manicurist_id FROM appointments 
             WHERE appointment_date = ? AND status NOT IN ('cancelled')",
            [$date]
        );
        
        // Get blocked slots
        $blockedSlots = $this->fetchAll(
            "SELECT time, manicurist_id FROM blocked_slots WHERE date = ?",
            [$date]
        );
        
        // Get active manicurists
        $manicurists = $this->getActiveManicurists();
        
        $availableSlots = [];
        foreach ($manicurists as $manicurist) {
            $availableSlots[$manicurist['id']] = [
                'name' => $manicurist['name'],
                'slots' => []
            ];
            
            foreach ($slots as $slot) {
                $isOccupied = false;
                
                // Check if slot is occupied
                foreach ($occupiedSlots as $occupied) {
                    if ($occupied['appointment_time'] === $slot && 
                        $occupied['manicurist_id'] == $manicurist['id']) {
                        $isOccupied = true;
                        break;
                    }
                }
                
                // Check if slot is blocked
                foreach ($blockedSlots as $blocked) {
                    if ($blocked['time'] === $slot && 
                        $blocked['manicurist_id'] == $manicurist['id']) {
                        $isOccupied = true;
                        break;
                    }
                }
                
                if (!$isOccupied) {
                    $availableSlots[$manicurist['id']]['slots'][] = $slot;
                }
            }
        }
        
        return $availableSlots;
    }
    
    public function getManicuristStats() {
        $sql = "SELECT m.name, COUNT(a.id) as appointment_count,
                       SUM(a.total_amount) as total_revenue
                FROM {$this->table} m
                LEFT JOIN appointments a ON m.id = a.manicurist_id
                WHERE m.is_active = 1
                GROUP BY m.id, m.name
                ORDER BY appointment_count DESC";
        return $this->fetchAll($sql);
    }
}