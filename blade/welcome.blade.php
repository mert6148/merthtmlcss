<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $baslik ?? 'Hoş Geldiniz' }} | Merthtmlcss</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">k
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
        @if(!isset($girisMesaji) && !isset($kayitMesaji) && !isset($girisBasarili) && !isset($cikisMesaji) && !isset($basariliMesaj) && !isset($kullaniciBilgisi))
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
    
    // Loading efekti için form submit
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>İşleniyor...</span>';
                submitBtn.disabled = true;
            }
        });
    });

    // Buton tıklama efekti
    if(buton) {
        buton.addEventListener("click", function() {
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
            window.location.href = "mailto:info@merthtmlcss.com?subject=İletişim&body=Merhaba, Merthtmlcss ile ilgili bir sorum var.";
            mesaj.textContent = "E-posta uygulamanız açılmadıysa, lütfen info@merthtmlcss.com adresine manuel olarak e-posta gönderebilirsiniz.";
            mesaj.style.color = "#667eea";
            mesaj.style.fontWeight = "bold";
        });
    }

    // Input focus efektleri
    const inputs = document.querySelectorAll('input');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'scale(1.02)';
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.style.transform = 'scale(1)';
        });
    });

    // Sayfa yüklendiğinde animasyon
    setTimeout(() => {
        document.querySelector('.bilgi-kutusu').style.opacity = '1';
        document.querySelector('.bilgi-kutusu').style.transform = 'translateY(0)';
    }, 100);
});

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
