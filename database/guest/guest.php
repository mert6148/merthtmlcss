<?php
/**
 * Guest Panel Controller & Classification System
 * Konuk yönetim operasyonları ve davranış sınıflandırması
 */

namespace App\Database;

use PDO;
use Exception;

// Constants
define('GUEST_STATUS_ACTIVE', 'active');
define('GUEST_STATUS_BANNED', 'banned');
define('GUEST_STATUS_INACTIVE', 'inactive');
define('GUEST_TYPE_ANONYMOUS', 'anonymous');
define('GUEST_TYPE_REGISTERED', 'registered');
define('GUEST_TYPE_TEMPORARY', 'temporary');

require_once '../../vendor/autoload.php';

/**
 * GuestBehavior: Konuk davranış sınıflandırması
 */
class GuestBehavior {
    private $guestId;
    private $conn;

    const BEHAVIOR_TYPES = [
        'normal' => 'Standart davranış',
        'suspicious' => 'Şüpheli aktivite',
        'malicious' => 'Kötü niyetli davranış',
        'inactive' => 'Hareketsiz konuk',
        'active' => 'Çok aktif konuk'
    ];

    public function __construct($guestId, PDO $conn) {
        $this->guestId = $guestId;
        $this->conn = $conn;
    }

    /**
     * Davranış tipini sınıflandır
     */
    public function classifyBehavior() {
        try {
            $stmt = $this->conn->prepare("SELECT visits, actions, last_action, created_at FROM guests WHERE id = :id");
            $stmt->bindParam(':id', $this->guestId);
            $stmt->execute();
            $guest = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$guest) {
                return null;
            }

            $behavior = $this->determineBehaviorType($guest);

            return [
                'guest_id' => $this->guestId,
                'behavior_type' => $behavior,
                'description' => self::BEHAVIOR_TYPES[$behavior],
                'risk_level' => $this->getRiskLevel($behavior),
                'classification_time' => date('Y-m-d H:i:s')
            ];
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Davranış tipini belirle
     */
    private function determineBehaviorType($guest) {
        $visits = (int) $guest['visits'];
        $actions = (int) $guest['actions'];
        $lastAction = $guest['last_action'];
        $createdAt = $guest['created_at'];

        $daysSinceCreated = (strtotime('now') - strtotime($createdAt)) / 86400;
        $daysSinceAction = $lastAction ? (strtotime('now') - strtotime($lastAction)) / 86400 : 999;

        // Hareketsiz konuk
        if ($daysSinceAction > 7 || $visits === 0) {
            return 'inactive';
        }

        // Çok aktif konuk
        if ($visits > 100 || $actions > 500) {
            return 'active';
        }

        // Standart davranış
        if ($visits > 5 && $actions > 20) {
            return 'normal';
        }

        // Şüpheli (tek seferde çok aksiyon)
        if ($visits === 1 && $actions > 100) {
            return 'suspicious';
        }

        // Kötü niyetli (çok sayıda hızlı istek)
        if ($actions > 1000) {
            return 'malicious';
        }

        return 'normal';
    }

    /**
     * Risk seviyesini belirle
     */
    private function getRiskLevel($behavior) {
        $riskLevels = [
            'normal' => 'low',
            'suspicious' => 'medium',
            'malicious' => 'high',
            'inactive' => 'low',
            'active' => 'medium'
        ];

        return $riskLevels[$behavior] ?? 'unknown';
    }

    /**
     * Tüm davranışları sınıflandır
     */
    public function classifyAllBehaviors() {
        try {
            $stmt = $this->conn->query("SELECT id, visits, actions, last_action, created_at FROM guests");
            $guests = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $classified = [
                'normal' => [],
                'suspicious' => [],
                'malicious' => [],
                'inactive' => [],
                'active' => []
            ];

            foreach ($guests as $guest) {
                $this->guestId = $guest['id'];
                $behavior = $this->determineBehaviorType($guest);
                $classified[$behavior][] = [
                    'id' => $guest['id'],
                    'visits' => $guest['visits'],
                    'actions' => $guest['actions'],
                    'last_action' => $guest['last_action']
                ];
            }

            return $classified;
        } catch (Exception $e) {
            return [];
        }
    }
}

/**
 * GuestClassifier: Konuk tipleri sınıflandırma
 */
class GuestClassifier {
    private $conn;

    const DEVICE_TYPES = ['mobile', 'tablet', 'desktop', 'bot', 'unknown'];
    const LOCATION_CATEGORIES = ['domestic', 'international', 'vpn', 'proxy'];

    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    /**
     * Konuk tipini sınıflandır
     */
    public function classifyGuestType($guestId) {
        try {
            $stmt = $this->conn->prepare("SELECT type, status, created_at FROM guests WHERE id = :id");
            $stmt->bindParam(':id', $guestId);
            $stmt->execute();
            $guest = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$guest) {
                return null;
            }

            return [
                'id' => $guestId,
                'type' => $guest['type'],
                'status' => $guest['status'],
                'age_days' => $this->getGuestAge($guest['created_at']),
                'classification' => $this->getTypeClassification($guest['type']),
                'is_active' => $guest['status'] === GUEST_STATUS_ACTIVE
            ];
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Konuk yaşını hesapla
     */
    private function getGuestAge($createdAt) {
        $createdTime = strtotime($createdAt);
        $now = strtotime('now');
        return floor(($now - $createdTime) / 86400);
    }

    /**
     * Tip sınıflandırması
     */
    private function getTypeClassification($type) {
        $classifications = [
            GUEST_TYPE_ANONYMOUS => 'Tanımsız - Oturum açmamış konuk',
            GUEST_TYPE_REGISTERED => 'Kayıtlı - Üyelik olan konuk',
            GUEST_TYPE_TEMPORARY => 'Geçici - Zaman sınırlı erişim'
        ];

        return $classifications[$type] ?? 'Bilinmiyor';
    }

    /**
     * Cihaz tipini sınıflandır
     */
    public function classifyDeviceType($userAgent) {
        $deviceType = 'unknown';

        if (preg_match('/(mobile|android|iphone|ipod)/i', $userAgent)) {
            $deviceType = 'mobile';
        } elseif (preg_match('/(tablet|ipad)/i', $userAgent)) {
            $deviceType = 'tablet';
        } elseif (preg_match('/(windows|mac|linux)/i', $userAgent)) {
            $deviceType = 'desktop';
        } elseif (preg_match('/(bot|crawler|spider)/i', $userAgent)) {
            $deviceType = 'bot';
        }

        return $deviceType;
    }

    /**
     * Tüm konukları sınıflandır
     */
    public function classifyAllGuests() {
        try {
            $stmt = $this->conn->query("SELECT id, type, status, created_at FROM guests");
            $guests = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $classified = [
                GUEST_TYPE_ANONYMOUS => [],
                GUEST_TYPE_REGISTERED => [],
                GUEST_TYPE_TEMPORARY => []
            ];

            foreach ($guests as $guest) {
                $classified[$guest['type']][] = [
                    'id' => $guest['id'],
                    'status' => $guest['status'],
                    'age_days' => $this->getGuestAge($guest['created_at'])
                ];
            }

            return $classified;
        } catch (Exception $e) {
            return [];
        }
    }
}

/**
 * GuestController: Geliştirilmiş konuk yönetim operasyonları
 */
class GuestController {
    private $dbConnection;
    private $conn;
    private $classifier;

    public function __construct() {
        try {
            $this->dbConnection = new DatabaseConnection();
            $this->conn = $this->dbConnection->getConnection();
            $this->classifier = new GuestClassifier($this->conn);
        } catch (Exception $e) {
            http_response_code(500);
            die(json_encode(['error' => 'Veritabanı bağlantı hatası: ' . $e->getMessage()]));
        }
    }

    /**
     * Tüm aktif konukları al
     */
    public function getAllGuests() {
        try {
            $stmt = $this->conn->prepare("SELECT id, username, email, type, status, visits, actions, created_at, last_action FROM guests WHERE status = :status ORDER BY created_at DESC");
            $stmt->bindParam(':status', GUEST_STATUS_ACTIVE, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return ['error' => 'Konuklar alınamadı'];
        }
    }

    /**
     * Konuk detaylarını al
     */
    public function getGuestDetails($guestId) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM guests WHERE id = :id");
            $stmt->bindParam(':id', $guestId);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return ['error' => 'Konuk bulunamadı'];
        }
    }

    /**
     * Yeni konuk kayıt
     */
    public function registerGuest($username, $email, $type = GUEST_TYPE_ANONYMOUS) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO guests (username, email, type, status, visits, actions) VALUES (:username, :email, :type, :status, :visits, :actions)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':status', GUEST_STATUS_ACTIVE);
            $stmt->bindParam(':visits', $visits = 1, PDO::PARAM_INT);
            $stmt->bindParam(':actions', $actions = 0, PDO::PARAM_INT);
            $stmt->execute();
            return ['success' => true, 'id' => $this->conn->lastInsertId()];
        } catch (Exception $e) {
            return ['error' => 'Konuk kaydı başarısız'];
        }
    }

    /**
     * Konuk ziyaretini güncelle
     */
    public function updateVisit($guestId) {
        try {
            $stmt = $this->conn->prepare("UPDATE guests SET visits = visits + 1, last_action = NOW() WHERE id = :id");
            $stmt->bindParam(':id', $guestId);
            $stmt->execute();
            return ['success' => true];
        } catch (Exception $e) {
            return ['error' => 'Ziyaret güncellenemedi'];
        }
    }

    /**
     * Konuk aksiyonunu güncelle
     */
    public function updateAction($guestId, $actionType) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO guest_actions (guest_id, action_type, created_at) VALUES (:guest_id, :action_type, NOW())");
            $stmt->bindParam(':guest_id', $guestId);
            $stmt->bindParam(':action_type', $actionType);
            $stmt->execute();

            // Aksiyon sayısını güncelle
            $this->conn->prepare("UPDATE guests SET actions = actions + 1, last_action = NOW() WHERE id = :id")->execute([':id' => $guestId]);

            return ['success' => true];
        } catch (Exception $e) {
            return ['error' => 'Aksiyon kaydı başarısız'];
        }
    }

    /**
     * Konuyu banlama/engelleme
     */
    public function banGuest($guestId, $reason = null) {
        try {
            $stmt = $this->conn->prepare("UPDATE guests SET status = :status WHERE id = :id");
            $stmt->bindParam(':id', $guestId);
            $stmt->bindParam(':status', GUEST_STATUS_BANNED);
            $stmt->execute();

            if ($reason) {
                $this->addLog('ban_guest', "Konuk $guestId banlandı: $reason", null);
            }

            return ['success' => true];
        } catch (Exception $e) {
            return ['error' => 'Konuk banlama başarısız'];
        }
    }

    /**
     * Konuyu aktifleştirme
     */
    public function activateGuest($guestId) {
        try {
            $stmt = $this->conn->prepare("UPDATE guests SET status = :status WHERE id = :id");
            $stmt->bindParam(':id', $guestId);
            $stmt->bindParam(':status', GUEST_STATUS_ACTIVE);
            $stmt->execute();
            return ['success' => true];
        } catch (Exception $e) {
            return ['error' => 'Konuk aktivasyonu başarısız'];
        }
    }

    /**
     * Konuk istatistiklerini al
     */
    public function getGuestStatistics() {
        try {
            return [
                'total_guests' => $this->getTotalGuests(),
                'active_guests' => $this->getActiveGuestsCount(),
                'banned_guests' => $this->getBannedGuestsCount(),
                'classified_guests' => $this->classifier->classifyAllGuests(),
                'behavior_classification' => $this->classifyAllBehaviors()
            ];
        } catch (Exception $e) {
            return ['error' => 'İstatistikler alınamadı'];
        }
    }

    /**
     * Toplam konuk sayısı
     */
    public function getTotalGuests() {
        try {
            $stmt = $this->conn->query("SELECT COUNT(*) as count FROM guests");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Aktif konuk sayısı
     */
    public function getActiveGuestsCount() {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM guests WHERE status = :status");
            $stmt->bindParam(':status', GUEST_STATUS_ACTIVE);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Banlı konuk sayısı
     */
    public function getBannedGuestsCount() {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM guests WHERE status = :status");
            $stmt->bindParam(':status', GUEST_STATUS_BANNED);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Konuk tipini sınıflandır
     */
    public function classifyGuest($guestId) {
        return $this->classifier->classifyGuestType($guestId);
    }

    /**
     * Davranış sınıflandırması
     */
    public function classifyBehavior($guestId) {
        $behavior = new GuestBehavior($guestId, $this->conn);
        return $behavior->classifyBehavior();
    }

    /**
     * Tüm davranışları sınıflandır
     */
    public function classifyAllBehaviors() {
        $behavior = new GuestBehavior(0, $this->conn);
        return $behavior->classifyAllBehaviors();
    }

    /**
     * Cihaz tipini sınıflandır
     */
    public function classifyDevice($userAgent) {
        return $this->classifier->classifyDeviceType($userAgent);
    }

    /**
     * Log kaydı ekle
     */
    public function addLog($action, $details, $guest_id = null) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO guest_logs (action, details, guest_id, created_at) VALUES (:action, :details, :guest_id, NOW())");
            $stmt->bindParam(':action', $action);
            $stmt->bindParam(':details', $details);
            $stmt->bindParam(':guest_id', $guest_id);
            $stmt->execute();
            return ['success' => true, 'log_id' => $this->conn->lastInsertId()];
        } catch (Exception $e) {
            return ['error' => 'Log kaydı eklenemedi'];
        }
    }

    /**
     * Log kayıtlarını al
     */
    public function getLogs($limit = 50) {
        try {
            $stmt = $this->conn->prepare("SELECT id, action, details, guest_id, created_at FROM guest_logs ORDER BY created_at DESC LIMIT :limit");
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
}

// API endpoint'leri
header('Content-Type: application/json');

$action = $_GET['action'] ?? null;
$controller = new GuestController();

switch ($action) {
    case 'all':
        echo json_encode($controller->getAllGuests());
        break;

    case 'details':
        if (isset($_GET['id'])) {
            echo json_encode($controller->getGuestDetails($_GET['id']));
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Guest ID required']);
        }
        break;

    case 'statistics':
        echo json_encode($controller->getGuestStatistics());
        break;

    case 'classify-guest':
        if (isset($_GET['id'])) {
            echo json_encode($controller->classifyGuest($_GET['id']));
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Guest ID required']);
        }
        break;

    case 'classify-behavior':
        if (isset($_GET['id'])) {
            echo json_encode($controller->classifyBehavior($_GET['id']));
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Guest ID required']);
        }
        break;

    case 'classify-device':
        if (isset($_GET['user_agent'])) {
            echo json_encode(['device_type' => $controller->classifyDevice($_GET['user_agent'])]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'User agent required']);
        }
        break;

    case 'register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            echo json_encode($controller->registerGuest(
                $data['username'] ?? '',
                $data['email'] ?? '',
                $data['type'] ?? GUEST_TYPE_ANONYMOUS
            ));
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    case 'update-visit':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            echo json_encode($controller->updateVisit($data['id'] ?? null));
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    case 'update-action':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            echo json_encode($controller->updateAction($data['id'] ?? null, $data['action_type'] ?? ''));
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    case 'ban':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            echo json_encode($controller->banGuest($data['id'] ?? null, $data['reason'] ?? null));
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    case 'activate':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            echo json_encode($controller->activateGuest($data['id'] ?? null));
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    case 'logs':
        $limit = $_GET['limit'] ?? 50;
        echo json_encode($controller->getLogs((int) $limit));
        break;

    case 'add-log':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            echo json_encode($controller->addLog(
                $data['action'] ?? '',
                $data['details'] ?? '',
                $data['guest_id'] ?? null
            ));
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    default:
        http_response_code(400);
        echo json_encode([
            'error' => 'Invalid action',
            'available_actions' => [
                'all', 'details', 'statistics',
                'classify-guest', 'classify-behavior', 'classify-device',
                'register', 'update-visit', 'update-action',
                'ban', 'activate', 'logs', 'add-log'
            ]
        ]);
}
