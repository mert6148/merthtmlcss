# Merthtmlcss - Modern Web Geliştirme Platformu

[![Version](https://img.shields.io/badge/version-2.1.0-blue.svg)](https://github.com/mert6148/merthtmlcss)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![PHP](https://img.shields.io/badge/PHP-8.0+-purple.svg)](https://php.net)
[![Python](https://img.shields.io/badge/Python-3.8+-yellow.svg)](https://python.org)
[![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-orange.svg)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)

Modern web teknolojileri ile geliştirilmiş kapsamlı bir web uygulaması. HTML, CSS, JavaScript, PHP, Python ve API entegrasyonu ile güçlendirilmiş tam özellikli platform.

## 🚀 Özellikler

### ✨ Modern Tasarım
- **Responsive Tasarım**: Tüm cihazlarda mükemmel görünüm
- **Dark/Light Tema**: Kullanıcı dostu tema değiştirme sistemi
- **Modern Animasyonlar**: CSS3 ve JavaScript ile gelişmiş animasyonlar
- **Gradient Efektleri**: Modern gradient tasarımları

### 🔧 Teknoloji Stack
- **Frontend**: HTML5, CSS3, JavaScript ES6+, React, Vue.js
- **Backend**: PHP 8.0+, Python 3.8+, Node.js
- **Database**: MySQL, PostgreSQL, SQLite
- **Tools**: Git, Docker, VS Code, Postman

### 🛡️ Güvenlik
- **CSRF Koruması**: Güvenli form işlemleri
- **Input Sanitization**: Giriş verilerinin temizlenmesi
- **Session Güvenliği**: Güvenli oturum yönetimi
- **HTTPS Desteği**: Güvenli bağlantı

### 📱 Performans
- **Lazy Loading**: Görsel yükleme optimizasyonu
- **Minification**: CSS/JS dosyalarının sıkıştırılması
- **Caching**: Akıllı önbellek sistemi
- **CDN Desteği**: Hızlı içerik dağıtımı

## 📋 Gereksinimler

### Sistem Gereksinimleri
- **PHP**: 8.0 veya üzeri
- **Python**: 3.8 veya üzeri
- **Node.js**: 16.0 veya üzeri
- **MySQL**: 5.7 veya üzeri
- **Web Server**: Apache/Nginx

### Tarayıcı Desteği
- **Chrome**: 90+
- **Firefox**: 88+
- **Safari**: 14+
- **Edge**: 90+

## 🛠️ Kurulum

### 1. Projeyi İndirin
```bash
git clone https://github.com/mert6148/merthtmlcss.git
cd merthtmlcss
```

### 2. Bağımlılıkları Yükleyin
```bash
# Node.js bağımlılıkları
npm install

# Python bağımlılıkları
pip install -r requirements.txt

# PHP bağımlılıkları (Composer ile)
composer install
```

### 3. Veritabanını Kurun
```bash
# Veritabanı kurulum scriptini çalıştırın
php database/install.php
```

### 4. Konfigürasyon
```bash
# Konfigürasyon dosyasını düzenleyin
cp config/config.example.php config/config.php
# config.php dosyasını düzenleyin
```

### 5. Web Sunucusunu Başlatın
```bash
# PHP built-in server
php -S localhost:8000

# Python Flask server
python auth/api_server.py

# Node.js server
npm start
```

## 📁 Proje Yapısı

```
merthtmlcss/
├── 📁 auth/                    # Kimlik doğrulama sistemi
│   ├── api_server.py          # Flask API sunucusu
│   ├── auth_manager.py        # Kimlik doğrulama yöneticisi
│   ├── css/                   # Stil dosyaları
│   └── js/                    # JavaScript dosyaları
├── 📁 blade/                   # Blade template dosyaları
│   ├── html/                  # HTML şablonları
│   └── animasyonlu-app.html   # Animasyonlu uygulama
├── 📁 database/                # Veritabanı yönetimi
│   ├── config/                # Veritabanı konfigürasyonu
│   ├── includes/              # Veritabanı sınıfları
│   ├── merthtmlcss.sql        # Veritabanı şeması
│   └── install.php            # Kurulum scripti
├── 📁 merthtmlcss-python/      # Python uygulamaları
│   ├── app.py                 # Ana Python uygulaması
│   └── venv/                  # Python sanal ortamı
├── 📄 index.html              # Ana sayfa
├── 📄 main.php                # Ana PHP dosyası
├── 📄 script.js               # Ana JavaScript dosyası
├── 📄 style.css               # Ana CSS dosyası
├── 📄 package.json            # Node.js konfigürasyonu
└── 📄 README.md               # Bu dosya
```

## 🔧 Konfigürasyon

### Veritabanı Ayarları
```php
// config/database.php
private $host = 'localhost';
private $db_name = 'merthtmlcss';
private $username = 'root';
private $password = '';
```

### API Ayarları
```python
# auth/api_server.py
app.config['SECRET_KEY'] = 'your-secret-key'
app.config['DATABASE_URL'] = 'mysql://user:pass@localhost/db'
```

### Tema Ayarları
```css
/* style.css */
:root {
    --primary: #667eea;
    --secondary: #764ba2;
    --accent: #ff6b6b;
}
```

## 📊 API Dokümantasyonu

### Kimlik Doğrulama Endpoints

#### POST /api/register
Kullanıcı kaydı
```json
{
    "username": "kullanici",
    "email": "kullanici@example.com",
    "password": "sifre123"
}
```

#### POST /api/login
Kullanıcı girişi
```json
{
    "email": "kullanici@example.com",
    "password": "sifre123"
}
```

#### GET /api/profile
Kullanıcı profili
```json
{
    "id": 1,
    "username": "kullanici",
    "email": "kullanici@example.com",
    "created_at": "2024-12-19T10:00:00Z"
}
```

### Veritabanı Endpoints

#### GET /api/posts
Blog yazılarını listele
```json
{
    "posts": [
        {
            "id": 1,
            "title": "Başlık",
            "content": "İçerik",
            "author": "Yazar",
            "created_at": "2024-12-19T10:00:00Z"
        }
    ]
}
```

#### POST /api/posts
Yeni yazı oluştur
```json
{
    "title": "Yeni Başlık",
    "content": "Yeni İçerik",
    "author_id": 1
}
```

## 🎨 Tema Sistemi

### Tema Değiştirme
```javascript
// Tema değiştirme
function toggleTheme() {
    document.body.classList.toggle('dark');
    localStorage.setItem('theme', 
        document.body.classList.contains('dark') ? 'dark' : 'light'
    );
}
```

### Özel Tema Oluşturma
```css
/* Özel tema */
:root[data-theme="custom"] {
    --primary: #your-color;
    --secondary: #your-color;
    --accent: #your-color;
}
```

## 🔒 Güvenlik

### CSRF Koruması
```php
// CSRF token oluşturma
$csrfToken = generateCSRFToken();

// Token doğrulama
if (!verifyCSRFToken($_POST['csrf_token'])) {
    die('CSRF token geçersiz');
}
```

### Input Sanitization
```php
// Giriş temizleme
$cleanInput = sanitizeInput($_POST['user_input']);

// Email doğrulama
if (!validateEmail($_POST['email'])) {
    die('Geçersiz email adresi');
}
```

## 📈 Performans Optimizasyonu

### Lazy Loading
```html
<img src="image.jpg" loading="lazy" alt="Açıklama">
```

### CSS/JS Minification
```bash
# CSS minification
npm run build:css

# JavaScript minification
npm run build:js
```

### Caching
```php
// Cache kullanımı
$data = getCache('key');
if (!$data) {
    $data = expensiveOperation();
    setCache('key', $data, 3600);
}
```

## 🧪 Test

### PHP Testleri
```bash
# PHPUnit testleri
./vendor/bin/phpunit tests/
```

### JavaScript Testleri
```bash
# Jest testleri
npm test
```

### Python Testleri
```bash
# Pytest testleri
pytest tests/
```

## 📝 Geliştirme

### Kod Standartları
- **PHP**: PSR-12
- **JavaScript**: ESLint + Prettier
- **Python**: PEP 8
- **CSS**: Stylelint

### Git Workflow
```bash
# Feature branch oluştur
git checkout -b feature/yeni-ozellik

# Değişiklikleri commit et
git add .
git commit -m "feat: yeni özellik eklendi"

# Pull request oluştur
git push origin feature/yeni-ozellik
```

## 🚀 Deployment

### Production Kurulumu
```bash
# Production build
npm run build

# Veritabanı migration
php database/migrate.php

# Cache temizleme
php cache/clear.php
```

### Docker Deployment
```bash
# Docker image oluştur
docker build -t merthtmlcss .

# Container çalıştır
docker run -p 80:80 merthtmlcss
```

## 📊 İstatistikler

- **Toplam Proje**: 15
- **Toplam Kullanıcı**: 1,250
- **Toplam İndirme**: 8,900
- **GitHub Stars**: 45
- **GitHub Forks**: 12

## 🤝 Katkıda Bulunma

1. **Fork** yapın
2. **Feature branch** oluşturun (`git checkout -b feature/amazing-feature`)
3. **Commit** edin (`git commit -m 'feat: amazing feature'`)
4. **Push** edin (`git push origin feature/amazing-feature`)
5. **Pull Request** oluşturun

### Katkıda Bulunanlar
- [Mert Doğanay](https://github.com/mert6148) - Ana Geliştirici

## 📄 Lisans

Bu proje MIT lisansı altında lisanslanmıştır. Detaylar için [LICENSE](LICENSE) dosyasına bakın.

## 📞 İletişim

- **Email**: mertdoganay437@gmail.com
- **Website**: https://merthtmlcss.com
- **Twitter**: [@MertDoganay61](https://x.com/MertDoganay61)
- **GitHub**: [@mert6148](https://github.com/mert6148)
- **YouTube**: [@mert_doganay](https://www.youtube.com/@mert_doganay)

## 🙏 Teşekkürler

Bu projeyi mümkün kılan tüm açık kaynak topluluğuna teşekkürler:

- [Font Awesome](https://fontawesome.com/) - İkonlar
- [Google Fonts](https://fonts.google.com/) - Tipografi
- [Unsplash](https://unsplash.com/) - Görseller
- [Flask](https://flask.palletsprojects.com/) - Python Framework
- [Laravel](https://laravel.com/) - PHP Framework

## 📈 Gelecek Planları

- [ ] PWA Desteği
- [ ] Mikroservis Mimarisi
- [ ] AI Entegrasyonu
- [ ] Mobil Uygulama
- [ ] Cloud Deployment
- [ ] Real-time Özellikler

---

**⭐ Bu projeyi beğendiyseniz yıldız vermeyi unutmayın!** 