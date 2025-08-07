<?php
/**
 * Base Model class for Sandy Beauty Nails
 */

abstract class BaseModel {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->fetch($sql, [$id]);
    }
    
    public function findAll($conditions = [], $orderBy = null, $limit = null) {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $field => $value) {
                $where[] = "$field = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }
        
        if ($limit) {
            $sql .= " LIMIT $limit";
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function create($data) {
        $fields = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        
        $this->db->query($sql, array_values($data));
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data) {
        $fields = array_keys($data);
        $set = array_map(function($field) {
            return "$field = ?";
        }, $fields);
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $set) . " WHERE {$this->primaryKey} = ?";
        
        $params = array_values($data);
        $params[] = $id;
        
        return $this->db->query($sql, $params);
    }
    
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->query($sql, [$id]);
    }
    
    public function count($conditions = []) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $field => $value) {
                $where[] = "$field = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        $result = $this->db->fetch($sql, $params);
        return $result['total'];
    }
    
    public function paginate($page = 1, $perPage = 10, $conditions = [], $orderBy = null) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $field => $value) {
                $where[] = "$field = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }
        
        $sql .= " LIMIT $perPage OFFSET $offset";
        
        $data = $this->db->fetchAll($sql, $params);
        $total = $this->count($conditions);
        
        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage)
        ];
    }
    
    protected function query($sql, $params = []) {
        return $this->db->query($sql, $params);
    }
    
    protected function fetch($sql, $params = []) {
        return $this->db->fetch($sql, $params);
    }
    
    protected function fetchAll($sql, $params = []) {
        return $this->db->fetchAll($sql, $params);
    }
}