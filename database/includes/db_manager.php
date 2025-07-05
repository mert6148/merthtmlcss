<?php
/**
 * Veritabanı Yönetim Sınıfı
 * Merthtmlcss Projesi
 */

require_once 'config/database.php';

class DatabaseManager {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // Kullanıcı işlemleri
    public function createUser($name, $email, $password, $number = null) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $query = "INSERT INTO users (name, email, password, number) VALUES (:name, :email, :password, :number)";
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':number', $number);
        
        return $stmt->execute();
    }
    
    public function getUserByEmail($email) {
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    public function getUserById($id) {
        $query = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    // Blog yazıları işlemleri
    public function createPost($title, $content, $author_id, $status = 'draft') {
        $query = "INSERT INTO posts (title, content, author_id, status) VALUES (:title, :content, :author_id, :status)";
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':author_id', $author_id);
        $stmt->bindParam(':status', $status);
        
        return $stmt->execute();
    }
    
    public function getPosts($limit = 10, $offset = 0, $status = 'published') {
        $query = "SELECT p.*, u.name as author_name 
                  FROM posts p 
                  LEFT JOIN users u ON p.author_id = u.id 
                  WHERE p.status = :status 
                  ORDER BY p.created_at DESC 
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    public function getPostById($id) {
        $query = "SELECT p.*, u.name as author_name 
                  FROM posts p 
                  LEFT JOIN users u ON p.author_id = u.id 
                  WHERE p.id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    // Kategori işlemleri
    public function getCategories() {
        $query = "SELECT * FROM categories ORDER BY name";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    public function getPostCategories($post_id) {
        $query = "SELECT c.* FROM categories c 
                  INNER JOIN post_categories pc ON c.id = pc.category_id 
                  WHERE pc.post_id = :post_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // Yorum işlemleri
    public function createComment($post_id, $user_id, $comment) {
        $query = "INSERT INTO comments (post_id, user_id, comment) VALUES (:post_id, :user_id, :comment)";
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':post_id', $post_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':comment', $comment);
        
        return $stmt->execute();
    }
    
    public function getComments($post_id) {
        $query = "SELECT c.*, u.name as user_name 
                  FROM comments c 
                  LEFT JOIN users u ON c.user_id = u.id 
                  WHERE c.post_id = :post_id 
                  ORDER BY c.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // İletişim mesajları
    public function createContactMessage($name, $email, $message) {
        $query = "INSERT INTO contact_messages (name, email, message) VALUES (:name, :email, :message)";
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':message', $message);
        
        return $stmt->execute();
    }
    
    public function getContactMessages($limit = 50) {
        $query = "SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // Site ayarları
    public function getSetting($key) {
        $query = "SELECT setting_value FROM site_settings WHERE setting_key = :key";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':key', $key);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result ? $result['setting_value'] : null;
    }
    
    public function updateSetting($key, $value) {
        $query = "INSERT INTO site_settings (setting_key, setting_value) 
                  VALUES (:key, :value) 
                  ON DUPLICATE KEY UPDATE setting_value = :value";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':key', $key);
        $stmt->bindParam(':value', $value);
        
        return $stmt->execute();
    }
    
    // İstatistikler
    public function getStats() {
        $stats = [];
        
        // Toplam kullanıcı sayısı
        $query = "SELECT COUNT(*) as count FROM users";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $stats['total_users'] = $stmt->fetch()['count'];
        
        // Toplam yazı sayısı
        $query = "SELECT COUNT(*) as count FROM posts WHERE status = 'published'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $stats['total_posts'] = $stmt->fetch()['count'];
        
        // Toplam yorum sayısı
        $query = "SELECT COUNT(*) as count FROM comments";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $stats['total_comments'] = $stmt->fetch()['count'];
        
        // Okunmamış mesaj sayısı
        $query = "SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 0";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $stats['unread_messages'] = $stmt->fetch()['count'];
        
        return $stats;
    }
    
    // API log kayıtları
    public function logApiRequest($endpoint, $method, $ip_address, $user_agent) {
        $query = "INSERT INTO api_logs (endpoint, method, ip_address, user_agent) 
                  VALUES (:endpoint, :method, :ip_address, :user_agent)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':endpoint', $endpoint);
        $stmt->bindParam(':method', $method);
        $stmt->bindParam(':ip_address', $ip_address);
        $stmt->bindParam(':user_agent', $user_agent);
        
        return $stmt->execute();
    }
    
    // Bildirimler
    public function createNotification($user_id, $title, $message, $type = 'info') {
        $query = "INSERT INTO notifications (user_id, title, message, type) 
                  VALUES (:user_id, :title, :message, :type)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':type', $type);
        
        return $stmt->execute();
    }
    
    public function getUserNotifications($user_id, $limit = 10) {
        $query = "SELECT * FROM notifications 
                  WHERE user_id = :user_id 
                  ORDER BY created_at DESC 
                  LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    public function markNotificationAsRead($notification_id) {
        $query = "UPDATE notifications SET is_read = 1 WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $notification_id);
        
        return $stmt->execute();
    }
}
?> 