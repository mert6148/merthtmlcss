<?php
echo '<link rel="stylesheet" href="../style-db.css">';
echo '<script src="../script-db.js"></script>';
/**
 * SQL Sorgu Oluşturucu
 * Merthtmlcss Projesi - Database Includes
 */

// Modern QueryBuilder - Hata yönetimi ve loglama
// Merthtmlcss Projesi
require_once __DIR__ . '/includes.php';
class QueryBuilder {
    private $pdo;
    private $table;
    private $select = '*';
    private $where = [];
    private $orderBy = [];
    private $limit = null;
    private $offset = null;
    private $join = [];
    private $groupBy = [];
    private $having = [];
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function table($table) {
        $this->table = $table;
        return $this;
    }
    
    public function select($columns = '*') {
        $this->select = is_array($columns) ? implode(', ', $columns) : $columns;
        return $this;
    }
    
    public function where($column, $operator, $value = null) {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        $this->where[] = [$column, $operator, $value];
        return $this;
    }
    
    public function whereIn($column, $values) {
        $placeholders = str_repeat('?,', count($values) - 1) . '?';
        $this->where[] = [$column, 'IN', "($placeholders)", $values];
        return $this;
    }
    
    public function whereBetween($column, $min, $max) {
        $this->where[] = [$column, 'BETWEEN', [$min, $max]];
        return $this;
    }
    
    public function whereNull($column) {
        $this->where[] = [$column, 'IS NULL'];
        return $this;
    }
    
    public function whereNotNull($column) {
        $this->where[] = [$column, 'IS NOT NULL'];
        return $this;
    }
    
    public function orderBy($column, $direction = 'ASC') {
        $this->orderBy[] = "$column $direction";
        return $this;
    }
    
    public function limit($limit) {
        $this->limit = $limit;
        return $this;
    }
    
    public function offset($offset) {
        $this->offset = $offset;
        return $this;
    }
    
    public function join($table, $first, $operator, $second, $type = 'INNER') {
        $this->join[] = "$type JOIN $table ON $first $operator $second";
        return $this;
    }
    
    public function leftJoin($table, $first, $operator, $second) {
        return $this->join($table, $first, $operator, $second, 'LEFT');
    }
    
    public function rightJoin($table, $first, $operator, $second) {
        return $this->join($table, $first, $operator, $second, 'RIGHT');
    }
    
    public function groupBy($columns) {
        $this->groupBy = is_array($columns) ? $columns : [$columns];
        return $this;
    }
    
    public function having($column, $operator, $value) {
        $this->having[] = [$column, $operator, $value];
        return $this;
    }
    
    private function buildWhere() {
        if (empty($this->where)) {
            return '';
        }
        
        $conditions = [];
        foreach ($this->where as $condition) {
            if (count($condition) === 2) {
                $conditions[] = $condition[0] . ' ' . $condition[1];
            } elseif (count($condition) === 3) {
                $conditions[] = $condition[0] . ' ' . $condition[1] . ' ?';
            } elseif (count($condition) === 4) {
                $conditions[] = $condition[0] . ' ' . $condition[1] . ' ' . $condition[2];
            }
        }
        
        return 'WHERE ' . implode(' AND ', $conditions);
    }
    
    private function buildQuery() {
        $query = "SELECT {$this->select} FROM {$this->table}";
        
        if (!empty($this->join)) {
            $query .= ' ' . implode(' ', $this->join);
        }
        
        $query .= ' ' . $this->buildWhere();
        
        if (!empty($this->groupBy)) {
            $query .= ' GROUP BY ' . implode(', ', $this->groupBy);
        }
        
        if (!empty($this->having)) {
            $havingConditions = [];
            foreach ($this->having as $condition) {
                $havingConditions[] = $condition[0] . ' ' . $condition[1] . ' ?';
            }
            $query .= ' HAVING ' . implode(' AND ', $havingConditions);
        }
        
        if (!empty($this->orderBy)) {
            $query .= ' ORDER BY ' . implode(', ', $this->orderBy);
        }
        
        if ($this->limit !== null) {
            $query .= ' LIMIT ' . $this->limit;
        }
        
        if ($this->offset !== null) {
            $query .= ' OFFSET ' . $this->offset;
        }
        
        return $query;
    }
    
    private function getParameters() {
        $params = [];
        foreach ($this->where as $condition) {
            if (count($condition) === 3) {
                $params[] = $condition[2];
            } elseif (count($condition) === 4) {
                $params = array_merge($params, $condition[3]);
            }
        }
        
        foreach ($this->having as $condition) {
            $params[] = $condition[2];
        }
        
        return $params;
    }
    
    public function get() {
        try {
            $query = $this->buildQuery();
            $params = $this->getParameters();
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            db_error($e->getMessage());
            return [];
        }
    }
    
    public function first() {
        $this->limit(1);
        $results = $this->get();
        return $results[0] ?? null;
    }
    
    public function count() {
        $originalSelect = $this->select;
        $this->select('COUNT(*) as count');
        
        $result = $this->first();
        $this->select($originalSelect);
        
        return $result['count'] ?? 0;
    }
    
    public function exists() {
        return $this->count() > 0;
    }
    
    public function reset() {
        $this->select = '*';
        $this->where = [];
        $this->orderBy = [];
        $this->limit = null;
        $this->offset = null;
        $this->join = [];
        $this->groupBy = [];
        $this->having = [];
        return $this;
    }
}

// Kullanım örneği:
// $query = new QueryBuilder($pdo);
// $users = $query->table('users')
//     ->select(['id', 'name', 'email'])
//     ->where('status', 'active')
//     ->whereIn('role', ['admin', 'editor'])
//     ->orderBy('created_at', 'DESC')
//     ->limit(10)
//     ->get();
?>