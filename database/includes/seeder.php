<?php
echo '<link rel="stylesheet" href="../style-db.css">';
echo '<script src="../script-db.js"></script>';
/**
 * Veritabanı Seeder Sistemi
 * Merthtmlcss Projesi - Database Includes
 */

// Modern Seeder Sistemi - Hata yönetimi ve loglama
// Merthtmlcss Projesi
require_once __DIR__ . '/includes.php';
class DatabaseSeeder {
    private $pdo;
    private $seeded_table = 'seeders';
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->createSeedersTable();
    }
    
    private function createSeedersTable() {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->seeded_table} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            seeder VARCHAR(255) NOT NULL,
            batch INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        try {
            $this->pdo->exec($sql);
        } catch (Exception $e) {
            db_error("Seeder tablosu oluşturulamadı: " . $e->getMessage());
        }
    }
    
    public function runSeeders($seeders = []) {
        $batch = $this->getNextBatchNumber();
        $seeded = 0;
        
        foreach ($seeders as $seeder) {
            if (!$this->hasSeeded($seeder['name'])) {
                try {
                    $this->pdo->beginTransaction();
                    
                    // Seeder'ı çalıştır
                    $seeder['run']($this->pdo);
                    
                    // Seeder kaydını ekle
                    $stmt = $this->pdo->prepare("INSERT INTO {$this->seeded_table} (seeder, batch) VALUES (?, ?)");
                    $stmt->execute([$seeder['name'], $batch]);
                    
                    $this->pdo->commit();
                    $seeded++;
                    
                    echo "🌱 Seeder çalıştırıldı: {$seeder['name']}\n";
                } catch (Exception $e) {
                    $this->pdo->rollBack();
                    db_error("Seeder hatası ({$seeder['name']}): " . $e->getMessage());
                }
            }
        }
        
        return $seeded;
    }
    
    public function hasSeeded($seeder) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM {$this->seeded_table} WHERE seeder = ?");
        $stmt->execute([$seeder]);
        return $stmt->fetchColumn() > 0;
    }
    
    public function getNextBatchNumber() {
        $stmt = $this->pdo->query("SELECT MAX(batch) FROM {$this->seeded_table}");
        $maxBatch = $stmt->fetchColumn();
        return ($maxBatch ?? 0) + 1;
    }
    
    public function getSeederStatus() {
        $stmt = $this->pdo->query("SELECT seeder, batch, created_at FROM {$this->seeded_table} ORDER BY batch DESC, id DESC");
        return $stmt->fetchAll();
    }
    
    public function clearSeeders() {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->seeded_table}");
        return $stmt->execute();
    }
    
    public function refreshSeeders($seeders = []) {
        $this->clearSeeders();
        return $this->runSeeders($seeders);
    }
}

// Örnek Seeder Tanımları
$seeders = [
    [
        'name' => 'users_seeder',
        'run' => function($pdo) {
            $users = [
                ['name' => 'Admin User', 'email' => 'admin@example.com', 'password' => password_hash('password', PASSWORD_DEFAULT)],
                ['name' => 'Editor User', 'email' => 'editor@example.com', 'password' => password_hash('password', PASSWORD_DEFAULT)],
                ['name' => 'Normal User', 'email' => 'user@example.com', 'password' => password_hash('password', PASSWORD_DEFAULT)]
            ];
            
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            foreach ($users as $user) {
                $stmt->execute([$user['name'], $user['email'], $user['password']]);
            }
        }
    ],
    [
        'name' => 'categories_seeder',
        'run' => function($pdo) {
            $categories = [
                ['name' => 'Web Geliştirme', 'description' => 'HTML, CSS, JavaScript ve modern web teknolojileri'],
                ['name' => 'PHP', 'description' => 'PHP programlama dili ve framework\'leri'],
                ['name' => 'Veritabanı', 'description' => 'MySQL, PostgreSQL ve diğer veritabanı sistemleri'],
                ['name' => 'Tasarım', 'description' => 'UI/UX tasarım ve kullanıcı deneyimi'],
                ['name' => 'Mobil', 'description' => 'Mobil uygulama geliştirme']
            ];
            
            $stmt = $pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
            foreach ($categories as $category) {
                $stmt->execute([$category['name'], $category['description']]);
            }
        }
    ],
    [
        'name' => 'posts_seeder',
        'run' => function($pdo) {
            $posts = [
                [
                    'title' => 'Modern CSS Teknikleri',
                    'content' => 'Modern CSS ile web sitelerinizi nasıl daha etkileyici hale getirebileceğinizi öğrenin.',
                    'author_id' => 1,
                    'status' => 'published'
                ],
                [
                    'title' => 'JavaScript ES6+ Özellikleri',
                    'content' => 'JavaScript\'in yeni özelliklerini keşfedin. Arrow functions, destructuring, modules ve daha fazlası.',
                    'author_id' => 2,
                    'status' => 'published'
                ],
                [
                    'title' => 'PHP ile API Geliştirme',
                    'content' => 'PHP kullanarak RESTful API nasıl geliştirilir? Adım adım rehber ve örnekler.',
                    'author_id' => 1,
                    'status' => 'published'
                ]
            ];
            
            $stmt = $pdo->prepare("INSERT INTO posts (title, content, author_id, status) VALUES (?, ?, ?, ?)");
            foreach ($posts as $post) {
                $stmt->execute([$post['title'], $post['content'], $post['author_id'], $post['status']]);
            }
        }
    ]
];

// Kullanım örneği:
// $seeder = new DatabaseSeeder($pdo);
// $seeder->runSeeders($seeders);
?> 