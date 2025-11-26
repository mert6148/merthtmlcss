<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $baslik ?? 'Hoş Geldiniz' }} | Merthtmlcss</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    :root {
        --primary: #0ea0a0;
        --secondary: #ffb347;
        --accent: #ff6b6b;
        --success: #51cf66;
        --warning: #ffd43b;
        --error: #ff4757;
        --dark: #222;
        --light: #fff;
        --gray: #6c757d;
        --light-gray: #f8f9fa;
        --border: #e9ecef;

        /* Modern CSS Variables */
        --gradient-primary: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        --gradient-dark: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.1);
        --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.15);
        --shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.2);
        --shadow-xl: 0 16px 48px rgba(0, 0, 0, 0.25);
        --border-radius: 12px;
        --border-radius-lg: 20px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        --transition-fast: all 0.15s ease;
        --font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    body {
        background: var(--gradient-primary);
        font-family: var(--font-family);
        margin: 0;
        padding: 0;
        min-height: 100vh;
        position: relative;
        overflow-x: hidden;
    }

    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background:
            radial-gradient(circle at 20% 80%, rgba(14, 160, 160, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(255, 179, 71, 0.1) 0%, transparent 50%);
        pointer-events: none;
        z-index: -1;
    }

    .bilgi-kutusu {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-xl);
        padding: 60px 50px;
        margin: 80px auto;
        max-width: 800px;
        text-align: center;
        position: relative;
        animation: slideInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        overflow: hidden;
    }

    .bilgi-kutusu::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--gradient-primary);
    }

    .bilgi-kutusu h1 {
        font-size: 3.5em;
        margin-bottom: 30px;
        background: var(--gradient-primary);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 800;
        animation: fadeInUp 0.8s ease-out;
    }

    .bilgi-kutusu p {
        font-size: 1.2em;
        color: var(--gray);
        margin-bottom: 40px;
        line-height: 1.6;
    }

    .message-success,
    .message-error,
    .message-info {
        padding: 16px 24px;
        border-radius: var(--border-radius);
        margin: 20px 0;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: slideInDown 0.5s ease-out;
    }

    .message-success {
        background: linear-gradient(135deg, var(--success), #40c057);
        color: white;
    }

    .message-error {
        background: linear-gradient(135deg, var(--error), #ff3742);
        color: white;
    }

    .message-info {
        background: linear-gradient(135deg, var(--primary), #0e7ca0);
        color: white;
    }

    .info-box {
        background: rgba(14, 160, 160, 0.1);
        border-radius: var(--border-radius);
        padding: 24px;
        margin: 20px 0;
        border-left: 4px solid var(--primary);
        text-align: left;
    }

    .info-box h3 {
        color: var(--primary);
        margin-bottom: 16px;
        font-size: 1.3em;
    }

    .info-box p {
        margin: 8px 0;
        color: var(--dark);
    }

    .button-group,
    .footer-actions {
        display: flex;
        gap: 20px;
        justify-content: center;
        flex-wrap: wrap;
        margin: 30px 0;
    }

    .btn-primary,
    .btn-success,
    .btn-info,
    .btn-danger {
        padding: 16px 32px;
        border: none;
        border-radius: var(--border-radius);
        font-size: 1.1em;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        position: relative;
        overflow: hidden;
    }

    .btn-primary {
        background: var(--gradient-primary);
        color: white;
        box-shadow: var(--shadow-md);
    }

    .btn-success {
        background: linear-gradient(135deg, var(--success), #40c057);
        color: white;
        box-shadow: var(--shadow-md);
    }

    .btn-info {
        background: linear-gradient(135deg, var(--primary), #0e7ca0);
        color: white;
        box-shadow: var(--shadow-md);
    }

    .btn-danger {
        background: linear-gradient(135deg, var(--error), #ff3742);
        color: white;
        box-shadow: var(--shadow-md);
    }

    .btn-primary:hover,
    .btn-success:hover,
    .btn-info:hover,
    .btn-danger:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: var(--shadow-lg);
    }

    .btn-primary:active,
    .btn-success:active,
    .btn-info:active,
    .btn-danger:active {
        transform: translateY(-1px) scale(1.02);
    }

    .link-hover {
        display: inline-block;
        margin-top: 20px;
        padding: 12px 24px;
        background: rgba(14, 160, 160, 0.1);
        border-radius: 25px;
        color: var(--primary);
        text-decoration: none;
        font-weight: 600;
        transition: var(--transition);
    }

    .link-hover:hover {
        background: rgba(14, 160, 160, 0.2);
        transform: translateY(-2px);
    }

    form {
        max-width: 500px;
        margin: 0 auto;
        text-align: left;
    }

    form div {
        margin-bottom: 20px;
    }

    form input,
    form textarea {
        width: 100%;
        padding: 16px 20px;
        border: 2px solid var(--border);
        border-radius: var(--border-radius);
        font-size: 1em;
        transition: var(--transition);
        background: rgba(255, 255, 255, 0.9);
    }

    form input:focus,
    form textarea:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(14, 160, 160, 0.1);
        transform: scale(1.02);
    }

    .loading-state {
        opacity: 0.7;
        pointer-events: none;
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(50px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }

    @media (max-width: 768px) {
        .bilgi-kutusu {
            margin: 20px;
            padding: 40px 30px;
        }

        .bilgi-kutusu h1 {
            font-size: 2.5em;
        }

        .button-group,
        .footer-actions {
            flex-direction: column;
            align-items: center;
        }

        .btn-primary,
        .btn-success,
        .btn-info,
        .btn-danger {
            width: 100%;
            max-width: 300px;
        }
    }
    </style>
</head>

<body>
    <section class="bilgi-kutusu">
        <h1>{{ $baslik ?? 'Merthtmlcss\'e Hoş Geldiniz' }}</h1>

        {{-- Giriş Formu --}}
        @if(isset($girisMesaji))
        <p>{{ $girisMesaji }}</p>
        @if(isset($hataMesaji) && !empty($hataMesaji))
        <div class="message-error">
            <i class="fas fa-exclamation-triangle"></i>
            {{ $hataMesaji }}
        </div>
        @endif
        <form method="POST" action="/giris" class="loading">
            <div>
                <input type="text" name="kullanici_adi" placeholder="Kullanıcı Adı" required>
            </div>
            <div>
                <input type="email" name="email" placeholder="E-posta" required>
            </div>
            <button type="submit" class="btn-primary">
                <span>Giriş Yap</span>
            </button>
        </form>
        <a href="/kayit" class="link-hover">Hesabınız yok mu? Kayıt olun</a>
        @endif

        {{-- Kayıt Formu --}}
        @if(isset($kayitMesaji))
        <p>{{ $kayitMesaji }}</p>
        @if(isset($hataMesaji) && !empty($hataMesaji))
        <div class="message-error">
            <i class="fas fa-exclamation-triangle"></i>
            {{ $hataMesaji }}
        </div>
        @endif
        <form method="POST" action="/kayit" class="loading">
            <div>
                <input type="text" name="yeni_ad" placeholder="Adınız" required>
            </div>
            <div>
                <input type="text" name="yeni_soyad" placeholder="Soyadınız" required>
            </div>
            <div>
                <input type="email" name="yeni_email" placeholder="E-posta" required>
            </div>
            <div>
                <input type="text" name="yeni_kullanici_adi" placeholder="Kullanıcı Adı" required>
            </div>
            <button type="submit" class="btn-success">
                <span>Kayıt Ol</span>
            </button>
        </form>
        <a href="/giris" class="link-hover">Zaten hesabınız var mı? Giriş yapın</a>
        @endif

        {{-- Kullanıcı Paneli --}}
        @if(isset($girisBasarili) && $girisBasarili)
        <div class="message-success">
            <i class="fas fa-check-circle"></i>
            {{ $hosgeldinMesaj }}
        </div>
        <div class="info-box">
            <h3><i class="fas fa-user-circle"></i> Kullanıcı Bilgileri</h3>
            <p><strong>ID:</strong> {{ $kullaniciVerisi['id'] }}</p>
            <p><strong>Ad Soyad:</strong> {{ $kullaniciVerisi['ad'] }} {{ $kullaniciVerisi['soyad'] }}</p>
            <p><strong>E-posta:</strong> {{ $kullaniciVerisi['email'] }}</p>
            <p><strong>Rol:</strong> {{ $kullaniciVerisi['rol'] }}</p>
            <p><strong>Kullanıcı Adı:</strong> {{ $kullaniciVerisi['kullanici_adi'] }}</p>
            <p><strong>Giriş Tarihi:</strong> {{ $kullaniciVerisi['giris_tarihi'] }}</p>
            <p><strong>Oturum Süresi:</strong> {{ $kullaniciVerisi['oturum_suresi'] }}</p>
        </div>
        <div class="button-group">
            <a href="/kullanicilar" class="btn-info">
                <i class="fas fa-users"></i>
                <span>Kullanıcı Listesi</span>
            </a>
            <a href="/cikis" class="btn-danger">
                <i class="fas fa-sign-out-alt"></i>
                <span>Çıkış Yap</span>
            </a>
        </div>
        @endif

        {{-- Çıkış Mesajı --}}
        @if(isset($cikisMesaji))
        <div class="message-info">
            <i class="fas fa-info-circle"></i>
            {{ $cikisMesaji }}
        </div>
        <a href="/giris" class="btn-primary">
            <i class="fas fa-sign-in-alt"></i>
            <span>Tekrar Giriş Yap</span>
        </a>
        @endif

        {{-- Kayıt Başarılı --}}
        @if(isset($basariliMesaj))
        <div class="message-success">
            <i class="fas fa-check-circle"></i>
            {{ $basariliMesaj }}
        </div>
        <div class="info-box">
            <h3><i class="fas fa-user-plus"></i> Kayıt Bilgileri</h3>
            <p><strong>ID:</strong> {{ $yeniKullanici['id'] }}</p>
            <p><strong>Ad Soyad:</strong> {{ $yeniKullanici['ad'] }} {{ $yeniKullanici['soyad'] }}</p>
            <p><strong>E-posta:</strong> {{ $yeniKullanici['email'] }}</p>
            <p><strong>Kullanıcı Adı:</strong> {{ $yeniKullanici['kullanici_adi'] }}</p>
            <p><strong>Rol:</strong> {{ $yeniKullanici['rol'] }}</p>
            <p><strong>Kayıt Tarihi:</strong> {{ $yeniKullanici['kayit_tarihi'] }}</p>
            <p><strong>Durum:</strong> {{ $yeniKullanici['durum'] }}</p>
        </div>
        <a href="/giris" class="btn-primary">
            <i class="fas fa-sign-in-alt"></i>
            <span>Giriş Yap</span>
        </a>
        @endif

        {{-- Kullanıcı Bilgileri (Welcome sayfası için) --}}
        @if(isset($kullaniciBilgisi))
        <p>Bu sayfa, Laravel Blade ile oluşturulmuş modern bir hoş geldiniz ekranıdır.</p>
        <div class="info-box">
            <h3><i class="fas fa-info-circle"></i> Sayfa Bilgileri</h3>
            @foreach($kullaniciBilgisi as $anahtar => $deger)
            <p><strong>{{ ucfirst($anahtar) }}:</strong> {{ $deger }}</p>
            @endforeach
        </div>
        @endif

        {{-- Varsayılan İçerik --}}
        @if(!isset($girisMesaji) && !isset($kayitMesaji) && !isset($girisBasarili) && !isset($cikisMesaji) &&
        !isset($basariliMesaj) && !isset($kullaniciBilgisi))
        <p>Bu sayfa, Laravel Blade ile oluşturulmuş modern bir hoş geldiniz ekranıdır.</p>
        <div class="button-group">
            <a href="/giris" class="btn-primary">
                <i class="fas fa-sign-in-alt"></i>
                <span>Giriş Yap</span>
            </a>
            <a href="/kayit" class="btn-success">
                <i class="fas fa-user-plus"></i>
                <span>Kayıt Ol</span>
            </a>
            <a href="/kullanicilar" class="btn-info">
                <i class="fas fa-users"></i>
                <span>Kullanıcılar</span>
            </a>
        </div>
        @endif

        <div class="footer-actions">
            <a href="/" class="btn-primary">
                <i class="fas fa-home"></i>
                <span>Anasayfaya Dön</span>
            </a>
            <button id="js-buton" class="btn-info">
                <i class="fas fa-envelope"></i>
                <span>Bize Ulaşın</span>
            </button>
        </div>
        <p id="js-mesaj"></p>
    </section>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</body>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const buton = document.getElementById("js-buton");
    const mesaj = document.getElementById("js-mesaj");

    // Modern loading efekti için form submit
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                // Loading state
                submitBtn.innerHTML =
                    '<i class="fas fa-spinner fa-spin"></i> <span>İşleniyor...</span>';
                submitBtn.disabled = true;
                submitBtn.classList.add('loading-state');

                // Progress bar ekle
                const progressBar = document.createElement('div');
                progressBar.className = 'progress-bar';
                progressBar.innerHTML = '<div class="progress-fill"></div>';
                form.appendChild(progressBar);

                // Progress animasyonu
                const progressFill = progressBar.querySelector('.progress-fill');
                let progress = 0;
                const progressInterval = setInterval(() => {
                    progress += Math.random() * 15;
                    if (progress > 100) progress = 100;
                    progressFill.style.width = progress + '%';
                }, 200);

                // Simüle edilmiş işlem
                setTimeout(() => {
                    clearInterval(progressInterval);
                    progressFill.style.width = '100%';

                    // Başarı mesajı
                    showMessage('İşlem başarıyla tamamlandı!', 'success');

                    // Form'u eski haline getir
                    submitBtn.innerHTML = submitBtn.getAttribute(
                        'data-original-text') || 'Gönder';
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('loading-state');

                    // Progress bar'ı kaldır
                    setTimeout(() => {
                        progressBar.remove();
                    }, 1000);
                }, 2000);
            }
        });
    });

    // Gelişmiş buton tıklama efekti
    if (buton) {
        buton.addEventListener("click", function(e) {
            // Ripple efekti
            const ripple = document.createElement('span');
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.background = 'rgba(255, 255, 255, 0.3)';
            ripple.style.transform = 'scale(0)';
            ripple.style.animation = 'ripple 0.6s linear';
            ripple.style.left = '50%';
            ripple.style.top = '50%';
            ripple.style.width = '100px';
            ripple.style.height = '100px';
            ripple.style.marginLeft = '-50px';
            ripple.style.marginTop = '-50px';

            this.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);

            // E-posta gönderme
            const email = 'mertdoganay437@gmail.com';
            const subject = 'Merthtmlcss Projesi Hakkında';
            const body =
                'Merhaba,\n\nMerthtmlcss projeniz hakkında bilgi almak istiyorum.\n\nSaygılarımla';
            window.location.href =
                `mailto:${email}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;

            showMessage('E-posta uygulamanız açılıyor...', 'info');
        });
    }

    // Gelişmiş input focus efektleri
    const inputs = document.querySelectorAll('input, textarea');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'scale(1.02)';
            this.parentElement.style.boxShadow = '0 0 0 3px rgba(14, 160, 160, 0.1)';
        });

        input.addEventListener('blur', function() {
            this.parentElement.style.transform = 'scale(1)';
            this.parentElement.style.boxShadow = '';
        });

        // Real-time validation
        input.addEventListener('input', function() {
            if (this.type === 'email') {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (emailRegex.test(this.value)) {
                    this.style.borderColor = 'var(--success)';
                } else if (this.value.length > 0) {
                    this.style.borderColor = 'var(--error)';
                } else {
                    this.style.borderColor = 'var(--border)';
                }
            }
        });
    });

    // Sayfa yüklendiğinde animasyon
    setTimeout(() => {
        document.querySelector('.bilgi-kutusu').style.opacity = '1';
        document.querySelector('.bilgi-kutusu').style.transform = 'translateY(0)';
    }, 100);

    // Intersection Observer ile scroll animasyonları
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        document.querySelectorAll('.info-box, .message-success, .message-error, .message-info').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(el);
        });
    }
});

// Modern mesaj gösterme fonksiyonu
function showMessage(text, type = 'info', duration = 4000) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `message-${type}`;
    messageDiv.style.cssText = `
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            padding: 16px 24px;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            z-index: 1001;
            animation: slideInDown 0.3s ease;
            box-shadow: var(--shadow-lg);
            max-width: 400px;
            text-align: center;
        `;

    messageDiv.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'}"></i>
            <span style="margin-left: 8px;">${text}</span>
        `;

    document.body.appendChild(messageDiv);

    // Otomatik kapatma
    setTimeout(() => {
        messageDiv.style.animation = 'slideInDown 0.3s ease reverse';
        setTimeout(() => {
            messageDiv.remove();
        }, 300);
    }, duration);
}

// Ripple animasyonu için CSS
const style = document.createElement('style');
style.textContent = `
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
        
        .button-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin: 20px 0;
        }
        
        .footer-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin: 30px 0 20px 0;
        }
        
        .link-hover {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background: rgba(102, 126, 234, 0.1);
            border-radius: 25px;
            transition: all 0.3s ease;
        }
        
        .link-hover:hover {
            background: rgba(102, 126, 234, 0.2);
            transform: translateY(-2px);
        }
        
        .bilgi-kutusu {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease;
        }
        
        .btn-primary i,
        .btn-success i,
        .btn-info i,
        .btn-danger i {
            margin-right: 8px;
        }
        
        .message-success i,
        .message-error i,
        .message-info i {
            margin-right: 10px;
        }
        
        .info-box h3 i {
            margin-right: 10px;
        }
    `;
document.head.appendChild(style);
</script>

</html>