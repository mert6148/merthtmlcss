-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1:3306
-- Üretim Zamanı: 28 Haz 2025, 09:27:37
-- Sunucu sürümü: 9.1.0
-- PHP Sürümü: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `merthtmlcss`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `api_logs`
--

DROP TABLE IF EXISTS `api_logs`;
CREATE TABLE IF NOT EXISTS `api_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `endpoint` varchar(100) COLLATE utf16_turkish_ci DEFAULT NULL,
  `method` varchar(10) COLLATE utf16_turkish_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf16_turkish_ci DEFAULT NULL,
  `user_agent` text COLLATE utf16_turkish_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf16 COLLATE=utf16_turkish_ci;

--
-- Tablo için döküm verisi `api_logs`
--

INSERT INTO `api_logs` (`id`, `endpoint`, `method`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, '/api/login', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2025-01-28 09:30:00'),
(2, '/api/register', 'POST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2025-01-28 09:35:00'),
(3, '/api/posts', 'GET', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2025-01-28 09:40:00');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf16_turkish_ci NOT NULL,
  `description` text COLLATE utf16_turkish_ci,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf16 COLLATE=utf16_turkish_ci;

--
-- Tablo için döküm verisi `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`) VALUES
(1, 'Web Geliştirme', 'HTML, CSS, JavaScript ve modern web teknolojileri'),
(2, 'PHP', 'PHP programlama dili ve framework\'leri'),
(3, 'Veritabanı', 'MySQL, PostgreSQL ve diğer veritabanı sistemleri'),
(4, 'Tasarım', 'UI/UX tasarım ve kullanıcı deneyimi'),
(5, 'Mobil', 'Mobil uygulama geliştirme');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `comments`
--

DROP TABLE IF EXISTS `comments`;
CREATE TABLE IF NOT EXISTS `comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `post_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `comment` text COLLATE utf16_turkish_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf16 COLLATE=utf16_turkish_ci;

--
-- Tablo için döküm verisi `comments`
--

INSERT INTO `comments` (`id`, `post_id`, `user_id`, `comment`, `created_at`) VALUES
(1, 1, 1, 'Harika bir yazı! Çok faydalı bilgiler var.', '2025-01-28 10:00:00'),
(2, 1, 2, 'Bu konuda daha fazla örnek görebilir miyiz?', '2025-01-28 10:15:00'),
(3, 2, 1, 'Modern CSS özellikleri gerçekten çok etkileyici.', '2025-01-28 10:30:00');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `contact_messages`
--

DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf16_turkish_ci NOT NULL,
  `email` varchar(100) COLLATE utf16_turkish_ci NOT NULL,
  `message` text COLLATE utf16_turkish_ci NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf16 COLLATE=utf16_turkish_ci;

--
-- Tablo için döküm verisi `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `message`, `is_read`, `created_at`) VALUES
(1, 'Ahmet Yılmaz', 'ahmet@example.com', 'Siteniz çok güzel görünüyor. Daha fazla içerik bekliyoruz.', 0, '2025-01-28 11:00:00'),
(2, 'Ayşe Demir', 'ayse@example.com', 'Web geliştirme konusunda yardım alabilir miyim?', 0, '2025-01-28 11:30:00'),
(3, 'Mehmet Kaya', 'mehmet@example.com', 'Modern CSS teknikleri hakkında yazı yazabilir misiniz?', 1, '2025-01-28 12:00:00');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `media`
--

DROP TABLE IF EXISTS `media`;
CREATE TABLE IF NOT EXISTS `media` (
  `id` int NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) COLLATE utf16_turkish_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf16_turkish_ci NOT NULL,
  `uploaded_by` int DEFAULT NULL,
  `uploaded_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `uploaded_by` (`uploaded_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf16 COLLATE=utf16_turkish_ci;

--
-- Tablo için döküm verisi `media`
--

INSERT INTO `media` (`id`, `file_name`, `file_path`, `uploaded_by`, `uploaded_at`) VALUES
(1, 'logo.png', '/uploads/images/logo.png', 1, '2025-01-28 09:00:00'),
(2, 'banner.jpg', '/uploads/images/banner.jpg', 1, '2025-01-28 09:15:00'),
(3, 'document.pdf', '/uploads/documents/document.pdf', 2, '2025-01-28 09:30:00');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `token` varchar(255) COLLATE utf16_turkish_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf16 COLLATE=utf16_turkish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `posts`
--

DROP TABLE IF EXISTS `posts`;
CREATE TABLE IF NOT EXISTS `posts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(200) COLLATE utf16_turkish_ci NOT NULL,
  `content` text COLLATE utf16_turkish_ci,
  `author_id` int DEFAULT NULL,
  `status` enum('published','draft') COLLATE utf16_turkish_ci DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `author_id` (`author_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf16 COLLATE=utf16_turkish_ci;

--
-- Tablo için döküm verisi `posts`
--

INSERT INTO `posts` (`id`, `title`, `content`, `author_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Modern CSS Teknikleri', 'Modern CSS ile web sitelerinizi nasıl daha etkileyici hale getirebileceğinizi öğrenin. CSS Grid, Flexbox ve animasyonlar hakkında detaylı bilgiler.', 1, 'published', '2025-01-28 09:00:00', '2025-01-28 09:00:00'),
(2, 'JavaScript ES6+ Özellikleri', 'JavaScript\'in yeni özelliklerini keşfedin. Arrow functions, destructuring, modules ve daha fazlası.', 2, 'published', '2025-01-28 09:30:00', '2025-01-28 09:30:00'),
(3, 'PHP ile API Geliştirme', 'PHP kullanarak RESTful API nasıl geliştirilir? Adım adım rehber ve örnekler.', 1, 'published', '2025-01-28 10:00:00', '2025-01-28 10:00:00'),
(4, 'Responsive Web Tasarımı', 'Mobil uyumlu web siteleri nasıl tasarlanır? Responsive tasarım prensipleri ve teknikleri.', 2, 'draft', '2025-01-28 10:30:00', '2025-01-28 10:30:00');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `post_categories`
--

DROP TABLE IF EXISTS `post_categories`;
CREATE TABLE IF NOT EXISTS `post_categories` (
  `post_id` int NOT NULL,
  `category_id` int NOT NULL,
  PRIMARY KEY (`post_id`,`category_id`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf16 COLLATE=utf16_turkish_ci;

--
-- Tablo için döküm verisi `post_categories`
--

INSERT INTO `post_categories` (`post_id`, `category_id`) VALUES
(1, 1),
(1, 4),
(2, 1),
(3, 2),
(4, 1),
(4, 4),
(4, 5);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf16_turkish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf16 COLLATE=utf16_turkish_ci;

--
-- Tablo için döküm verisi `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'admin'),
(2, 'editor'),
(3, 'user');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `site_settings`
--

DROP TABLE IF EXISTS `site_settings`;
CREATE TABLE IF NOT EXISTS `site_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) COLLATE utf16_turkish_ci NOT NULL,
  `setting_value` text COLLATE utf16_turkish_ci,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf16 COLLATE=utf16_turkish_ci;

--
-- Tablo için döküm verisi `site_settings`
--

INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'site_title', 'Merthtmlcss - Modern Web Geliştirme', '2025-01-28 09:00:00'),
(2, 'site_description', 'Modern web teknolojileri ile geliştirilmiş projeler ve eğitim içerikleri', '2025-01-28 09:00:00'),
(3, 'site_keywords', 'web geliştirme, css, javascript, php, html', '2025-01-28 09:00:00'),
(4, 'maintenance_mode', '0', '2025-01-28 09:00:00'),
(5, 'posts_per_page', '10', '2025-01-28 09:00:00'),
(6, 'allow_comments', '1', '2025-01-28 09:00:00'),
(7, 'theme', 'light', '2025-01-28 09:00:00');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(192) COLLATE utf16_turkish_ci NOT NULL,
  `password` varchar(192) COLLATE utf16_turkish_ci NOT NULL,
  `number` varchar(20) COLLATE utf16_turkish_ci DEFAULT NULL,
  `name` varchar(192) COLLATE utf16_turkish_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf16 COLLATE=utf16_turkish_ci;

--
-- Tablo için döküm verisi `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `number`, `name`, `created_at`, `updated_at`) VALUES
(1, 'admin@merthtmlcss.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '5551234567', 'Admin User', '2025-01-28 09:00:00', '2025-01-28 09:00:00'),
(2, 'editor@merthtmlcss.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '5559876543', 'Editor User', '2025-01-28 09:15:00', '2025-01-28 09:15:00'),
(3, 'user@merthtmlcss.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '5551112233', 'Normal User', '2025-01-28 09:30:00', '2025-01-28 09:30:00');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `user_roles`
--

DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE IF NOT EXISTS `user_roles` (
  `user_id` int NOT NULL,
  `role_id` int NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `role_id` (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf16 COLLATE=utf16_turkish_ci;

--
-- Tablo için döküm verisi `user_roles`
--

INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES
(1, 1),
(2, 2),
(3, 3);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(128) COLLATE utf16_turkish_ci NOT NULL,
  `user_id` int DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf16_turkish_ci DEFAULT NULL,
  `user_agent` text COLLATE utf16_turkish_ci,
  `payload` text COLLATE utf16_turkish_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `last_activity` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf16 COLLATE=utf16_turkish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `title` varchar(255) COLLATE utf16_turkish_ci NOT NULL,
  `message` text COLLATE utf16_turkish_ci NOT NULL,
  `type` enum('info','success','warning','error') COLLATE utf16_turkish_ci DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf16 COLLATE=utf16_turkish_ci;

--
-- Tablo için döküm verisi `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `type`, `is_read`, `created_at`) VALUES
(1, 1, 'Hoş Geldiniz!', 'Merthtmlcss sistemine başarıyla giriş yaptınız.', 'success', 0, '2025-01-28 09:00:00'),
(2, 2, 'Yeni Yazı', 'Yeni bir blog yazısı yayınlandı.', 'info', 0, '2025-01-28 09:30:00'),
(3, 3, 'Sistem Güncellemesi', 'Sistem güncellemesi tamamlandı.', 'info', 1, '2025-01-28 10:00:00');

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
