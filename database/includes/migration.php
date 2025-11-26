<?php
echo '<link rel="stylesheet" href="../style-db.css">';
echo '<script src="../script-db.js"></script>';
/**
 * Veritabanı Migration Sistemi
 * Merthtmlcss Projesi - Database Includes
 */

// Modern Migration Sistemi - Hata yönetimi ve loglama
// Merthtmlcss Projesi
require_once __DIR__ . '/includes.php';
class DatabaseMigration {
    private $pdo;
    private $migrations_table = 'migrations';
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->createMigrationsTable();
    }
    
    private function createMigrationsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->migrations_table} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            batch INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        try {
            $this->pdo->exec($sql);
        } catch (Exception $e) {
            db_error("Migration tablosu oluşturulamadı: " . $e->getMessage());
        }
    }
    
    public function runMigrations($migrations = []) {
        $batch = $this->getNextBatchNumber();
        $ran = 0;
        
        foreach ($migrations as $migration) {
            if (!$this->hasRun($migration['name'])) {
                try {
                    $this->pdo->beginTransaction();
                    
                    // Migration'ı çalıştır
                    $this->pdo->exec($migration['up']);
                    
                    // Migration kaydını ekle
                    $stmt = $this->pdo->prepare("INSERT INTO {$this->migrations_table} (migration, batch) VALUES (?, ?)");
                    $stmt->execute([$migration['name'], $batch]);
                    
                    $this->pdo->commit();
                    $ran++;
                    
                    echo "✅ Migration çalıştırıldı: {$migration['name']}\n";
                } catch (Exception $e) {
                    $this->pdo->rollBack();
                    db_error("Migration hatası ({$migration['name']}): " . $e->getMessage());
                }
            }
        }
        
        return $ran;
    }
    
    public function rollbackMigrations($steps = 1) {
        $lastBatch = $this->getLastBatchNumber();
        $targetBatch = max(0, $lastBatch - $steps);
        
        $migrations = $this->getMigrationsByBatch($lastBatch);
        $rolledBack = 0;
        
        foreach (array_reverse($migrations) as $migration) {
            try {
                $this->pdo->beginTransaction();
                
                // Rollback migration'ı çalıştır
                if (isset($migration['down'])) {
                    $this->pdo->exec($migration['down']);
                }
                
                // Migration kaydını sil
                $stmt = $this->pdo->prepare("DELETE FROM {$this->migrations_table} WHERE migration = ?");
                $stmt->execute([$migration['name']]);
                
                $this->pdo->commit();
                $rolledBack++;
                
                echo "🔄 Migration geri alındı: {$migration['name']}\n";
            } catch (Exception $e) {
                $this->pdo->rollBack();
                db_error("Rollback hatası ({$migration['name']}): " . $e->getMessage());
            }
        }
        
        return $rolledBack;
    }
    
    public function hasRun($migration) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM {$this->migrations_table} WHERE migration = ?");
        $stmt->execute([$migration]);
        return $stmt->fetchColumn() > 0;
    }
    
    public function getNextBatchNumber() {
        $stmt = $this->pdo->query("SELECT MAX(batch) FROM {$this->migrations_table}");
        $maxBatch = $stmt->fetchColumn();
        return ($maxBatch ?? 0) + 1;
    }
    
    public function getLastBatchNumber() {
        $stmt = $this->pdo->query("SELECT MAX(batch) FROM {$this->migrations_table}");
        return $stmt->fetchColumn() ?? 0;
    }
    
    public function getMigrationsByBatch($batch) {
        $stmt = $this->pdo->prepare("SELECT migration FROM {$this->migrations_table} WHERE batch = ? ORDER BY id");
        $stmt->execute([$batch]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    public function getMigrationStatus() {
        $stmt = $this->pdo->query("SELECT migration, batch, created_at FROM {$this->migrations_table} ORDER BY batch DESC, id DESC");
        return $stmt->fetchAll();
    }
    
    public function resetMigrations() {
        $migrations = $this->getMigrationStatus();
        $totalRollbacks = 0;
        
        foreach (array_reverse($migrations) as $migration) {
            $this->rollbackMigrations(1);
            $totalRollbacks++;
        }
        
        return $totalRollbacks;
    }
    
    public function refreshMigrations() {
        $this->resetMigrations();
        return $this->runMigrations();
    }
}

// Örnek Migration Tanımları
$migrations = [
    [
        'name' => 'create_users_table',
        'up' => "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        'down' => "DROP TABLE IF EXISTS users"
    ],
    [
        'name' => 'create_posts_table',
        'up' => "CREATE TABLE IF NOT EXISTS posts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            content TEXT,
            user_id INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )",
        'down' => "DROP TABLE IF EXISTS posts"
    ]
];

// Kullanım örneği:
// $migration = new DatabaseMigration($pdo);
// $migration->runMigrations($migrations);
?> 