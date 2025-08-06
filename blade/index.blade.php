@php
$xml = simplexml_load_file(resource_path('views/blade/hakkinda.xml'));
$baslik = (string)$xml->baslik;
$aciklama = (string)$xml->aciklama;
$gelistirici = (string)$xml->gelistirici;
$iletisim = (string)($xml->iletisim ?? 'info@merthtmlcss.com');
$iletisimLink = '<a
    href="mailto:' . $iletisim . '?subject=İletişim&body=Merhaba, Merthtmlcss ile ilgili bir sorum var.">Bize e-posta
    gönderin</a>';
@endphp
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Web Geliştirme Projesi | Merthtmlcss</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link rel="stylesheet" href="merthtmlcss-styles.css">
    <style>
    body {
        background: linear-gradient(120deg, #e0f7fa 0%, #f8fafc 100%);
        font-family: 'Segoe UI', Arial, sans-serif;
        margin: 0;
        padding: 0;
    }

    .bilgi-kutusu {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(14, 160, 160, 0.10);
        padding: 36px 28px;
        margin: 60px auto 0 auto;
        max-width: 520px;
        text-align: center;
        position: relative;
    }

    .bilgi-baslik {
        color: #0ea0a0;
        font-size: 2.2em;
        font-weight: bold;
        margin-bottom: 18px;
        letter-spacing: 1px;
        text-align: center;
    }

    .bilgi-aciklama {
        background: #e0f7fa;
        color: #222;
        border-radius: 10px;
        padding: 18px 20px;
        font-size: 1.18em;
        margin-bottom: 22px;
        box-shadow: 0 2px 8px rgba(14, 160, 160, 0.07);
        text-align: left;
        display: inline-block;
        max-width: 95%;
    }

    .bilgi-gelistirici {
        background: linear-gradient(90deg, #0ea0a0 60%, #0e7ca0 100%);
        color: #fff;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        font-weight: 600;
        font-size: 1.1em;
        padding: 10px 22px;
        margin-bottom: 10px;
        box-shadow: 0 2px 8px rgba(14, 160, 160, 0.10);
        gap: 10px;
    }

    .bilgi-gelistirici::before {
        content: '\1F464';
        /* kullanıcı ikonu */
        font-size: 1.2em;
    }

    .bilgi-btn {
        background: linear-gradient(90deg, #0ea0a0 60%, #ffb347 100%);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 12px 28px;
        font-size: 1em;
        cursor: pointer;
        margin-top: 20px;
        transition: background 0.2s, transform 0.2s, box-shadow 0.2s;
        box-shadow: 0 2px 8px rgba(14, 160, 160, 0.12);
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .bilgi-btn:hover {
        background: linear-gradient(90deg, #097c7c 60%, #ff9800 100%);
        transform: translateY(-2px) scale(1.04);
        box-shadow: 0 4px 16px rgba(14, 160, 160, 0.18);
    }

    .ek-bilgi {
        margin-top: 20px;
        color: #0ea0a0;
        font-weight: bold;
        font-size: 1.09em;
        background: linear-gradient(90deg, #e0f7fa 60%, #f8fafc 100%);
        border-left: 4px solid #0ea0a0;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(14, 160, 160, 0.07);
        padding: 18px 22px 18px 32px;
        position: relative;
        min-height: 32px;
        max-height: 0;
        opacity: 0;
        overflow: hidden;
        transition: max-height 0.5s cubic-bezier(.4, 2, .6, 1), opacity 0.4s;
    }

    .ek-bilgi.goster {
        max-height: 200px;
        opacity: 1;
        transition: max-height 0.7s cubic-bezier(.4, 2, .6, 1), opacity 0.4s;
    }

    .ek-bilgi::before {
        content: '\1F4A1';
        /* ampul ikonu */
        position: absolute;
        left: 8px;
        top: 18px;
        font-size: 1.3em;
    }

    #anasayfa {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(14, 160, 160, 0.10);
        padding: 40px 28px;
        margin: 0 auto 48px auto;
        max-width: 520px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    #anasayfa h1 {
        color: #0ea0a0;
        margin-bottom: 18px;
        font-size: 2.1em;
    }

    #anasayfa p {
        color: #333;
        margin-bottom: 12px;
        font-size: 1.13em;
    }

    #anasayfa button {
        background: linear-gradient(90deg, #0ea0a0 60%, #0e7ca0 100%);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 12px 32px;
        font-size: 1em;
        cursor: pointer;
        margin-top: 18px;
        transition: background 0.2s, transform 0.2s;
        box-shadow: 0 2px 8px rgba(14, 160, 160, 0.08);
    }

    #anasayfa button:hover {
        background: linear-gradient(90deg, #097c7c 60%, #0e5c7c 100%);
        transform: translateY(-2px) scale(1.03);
    }

    #anasayfa img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 50%;
        margin-bottom: 22px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.10);
    }

    #js-mesaj {
        margin-top: 18px;
        color: #0ea0a0;
        font-weight: bold;
        font-size: 1.05em;
    }

    @media (max-width: 600px) {

        .bilgi-kutusu,
        #anasayfa {
            padding: 18px 6vw;
            max-width: 98vw;
        }

        #anasayfa img {
            width: 80px;
            height: 80px;
        }
    }

    .teknoloji-listesi {
        margin-top: 32px;
        background: #f8f8fa;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
        padding: 20px;
        max-width: 480px;
        margin-left: auto;
        margin-right: auto;
    }

    .teknoloji-listesi h3 {
        color: #0ea0a0;
        margin-bottom: 10px;
    }

    .teknoloji-listesi ul {
        padding-left: 20px;
    }

    .teknoloji-listesi li {
        color: #333;
        font-size: 1.08em;
        margin-bottom: 4px;
    }

    .iletisim-kutusu {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(14, 160, 160, 0.10);
        padding: 36px 28px;
        margin: 60px auto 40px auto;
        max-width: 520px;
        text-align: center;
    }

    .iletisim-baslik {
        color: #0ea0a0;
        margin-bottom: 10px;
        font-size: 1.7em;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .iletisim-ikon {
        font-size: 1.2em;
    }

    .iletisim-aciklama {
        color: #333;
        margin-bottom: 18px;
        font-size: 1.13em;
    }

    .input-group {
        display: flex;
        align-items: center;
        margin-bottom: 12px;
        background: #f8f8fa;
        border-radius: 7px;
        box-shadow: 0 1px 4px rgba(14, 160, 160, 0.04);
        padding: 0 8px;
    }

    .input-ikon {
        color: #0ea0a0;
        font-size: 1.2em;
        margin-right: 8px;
    }

    .iletisim-input {
        width: 100%;
        padding: 10px 10px;
        border: none;
        background: transparent;
        font-size: 1em;
        border-radius: 7px;
        outline: none;
    }

    textarea.iletisim-input {
        resize: vertical;
        min-height: 60px;
        max-height: 180px;
    }

    .iletisim-btn {
        background: linear-gradient(90deg, #0ea0a0 60%, #ffb347 100%);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 12px 32px;
        font-size: 1em;
        cursor: pointer;
        margin-top: 8px;
        transition: background 0.2s, transform 0.2s, box-shadow 0.2s;
        box-shadow: 0 2px 8px rgba(14, 160, 160, 0.08);
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .iletisim-btn:hover {
        background: linear-gradient(90deg, #097c7c 60%, #ff9800 100%);
        transform: translateY(-2px) scale(1.03);
        box-shadow: 0 4px 16px rgba(14, 160, 160, 0.18);
    }

    .form-sonuc {
        margin-top: 16px;
        color: #0ea0a0;
        font-weight: bold;
        min-height: 24px;
        opacity: 0;
        transition: opacity 0.5s;
    }

    .form-sonuc.goster {
        opacity: 1;
        animation: fadeIn 0.7s;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .iletisim-ekstra {
        margin-top: 24px;
        color: #0ea0a0;
        font-size: 1.08em;
    }

    .iletisim-mail {
        margin-bottom: 8px;
        font-weight: 600;
    }

    .iletisim-sosyal a {
        color: #0ea0a0;
        text-decoration: none;
        margin: 0 4px;
        font-weight: 600;
        transition: color 0.2s;
    }

    .iletisim-sosyal a:hover {
        color: #ff9800;
        text-decoration: underline;
    }

    .eposta-btn {
        background: linear-gradient(90deg, #0ea0a0 60%, #0e7ca0 100%);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 12px 32px;
        font-size: 1em;
        cursor: pointer;
        margin-top: 16px;
        font-weight: 600;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 8px rgba(14, 160, 160, 0.08);
        transition: background 0.2s, transform 0.2s, box-shadow 0.2s;
    }

    .eposta-btn:hover {
        background: linear-gradient(90deg, #097c7c 60%, #ff9800 100%);
        transform: translateY(-2px) scale(1.04);
        box-shadow: 0 4px 16px rgba(14, 160, 160, 0.18);
    }

    .eposta-bilgi {
        margin-top: 10px;
        color: #0ea0a0;
        font-weight: bold;
        min-height: 24px;
        opacity: 0;
        transition: opacity 0.5s;
    }

    .eposta-bilgi.goster {
        opacity: 1;
        animation: fadeIn 0.7s;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }
    </style>
</head>

<body class="site-body">
    <div class="bilgi-kutusu">
        <div class="bilgi-baslik glow">Modern Web Geliştirme Projesi</div>
        <div class="bilgi-gelistirici">Mert</div>
        <div class="bilgi-aciklama">Bu proje, modern web geliştirme teknolojilerini bir araya getiren örnek bir
            projedir. HTML, CSS ve JavaScript teknolojileri kullanılarak oluşturulmuştur.</div>

        <!-- Modern Butonlar -->
        <div class="fade-in delay-1" style="margin: 20px 0; text-align: center;">
            <button class="button button-primary">Başla</button>
            <button class="button button-success">Başarılı</button>
        </div>

        <!-- Modern Kartlar -->
        <div class="card card-primary fade-in delay-2" style="margin: 20px 0;">
            <div class="card-header">
                <h3 class="card-title">Başlık</h3>
            </div>
            <div class="card-body">İçerik</div>
        </div>

        <!-- Animasyonlu İçerik -->
        <div class="fade-in delay-3" style="text-align: center; margin: 20px 0;">
            <h4 class="glow">Animasyonlu İçerik</h4>
            <p>Bu içerik modern CSS animasyonları ile görüntüleniyor.</p>
        </div>
    </div>
    <div class="teknoloji-listesi">
        <h3>Kullanılan Teknolojiler</h3>
        <ul id="teknolojiler"></ul>
        <button id="teknoloji-btn" class="bilgi-btn" style="margin-top:16px;">Teknolojileri Göster</button>
    </div>
    <section id="anasayfa">
        <img src="https://images.unsplash.com/photo-1461749280684-dccba630e2f6?auto=format&fit=cover&w=400&q=80"
            alt="Web Geliştirme" id="anasayfa-img">
        <h1>Merthtmlcss'e Hoş Geldiniz</h1>
        <p id="php-mesaj">
            Merthtmlcss, modern web geliştirme için HTML, CSS ve JavaScript teknolojilerini bir araya getiren örnek bir
            projedir.
        </p>
        <p>Bu projede, temel web teknolojilerini öğrenebilir ve kendi projelerinize ilham alabilirsiniz.</p>
        <button id="eposta-btn" class="eposta-btn">Bize E-posta Gönder</button>
        <div id="eposta-bilgi" class="eposta-bilgi"></div>
        <p id="js-mesaj"></p>
    </section>
    <section class="iletisim-kutusu">
        <h2 class="iletisim-baslik"><span class="iletisim-ikon">&#9993;</span> İletişim</h2>
        <p class="iletisim-aciklama">Her türlü soru, görüş ve iş birliği için bana ulaşabilirsiniz. Size en kısa sürede
            dönüş yapacağım.</p>
        <form id="iletisim-form">
            <div class="input-group">
                <span class="input-ikon">&#128100;</span>
                <input type="text" id="ad" placeholder="Adınız" required class="iletisim-input">
            </div>
            <div class="input-group">
                <span class="input-ikon">&#9993;</span>
                <input type="email" id="email" placeholder="E-posta" required class="iletisim-input">
            </div>
            <div class="input-group">
                <span class="input-ikon">&#9998;</span>
                <textarea id="mesaj" placeholder="Mesajınız" required class="iletisim-input"></textarea>
            </div>
            <button type="submit" class="iletisim-btn">Gönder</button>
        </form>
        <div id="form-sonuc" class="form-sonuc"></div>
        <div class="iletisim-ekstra">
            <div class="iletisim-mail"><span class="input-ikon">&#128231;</span> info@merthtmlcss.com</div>
            <div class="iletisim-sosyal">
                <a href="#" target="_blank">Twitter</a> |
                <a href="#" target="_blank">GitHub</a> |
                <a href="#" target="_blank">LinkedIn</a>
            </div>
        </div>
    </section>
    <script type="module" src="{{ asset('script.js') }}"></script>
    <script>
    // Hakkında bilgilerini XML'den dinamik olarak çek
    fetch('hakkinda.xml')
        .then(response => {
            if (!response.ok) throw new Error('XML dosyası bulunamadı veya erişilemiyor.');
            return response.text();
        })
        .then(str => (new window.DOMParser()).parseFromString(str, "text/xml"))
        .then(data => {
            document.querySelector('.bilgi-baslik').textContent = data.getElementsByTagName('baslik')[0]?.textContent || 'Modern Web Geliştirme Projesi';
            document.querySelector('.bilgi-gelistirici').textContent = data.getElementsByTagName('gelistirici')[0]?.textContent || 'Mert';
            document.querySelector('.bilgi-aciklama').textContent = data.getElementsByTagName('aciklama')[0]?.textContent || 'Bu proje, modern web geliştirme teknolojilerini bir araya getiren örnek bir projedir. HTML, CSS ve JavaScript teknolojileri kullanılarak oluşturulmuştur.';
        })
        .catch(e => {
            document.querySelector('.bilgi-baslik').textContent = 'Hakkında bilgisi yüklenemedi.';
            document.querySelector('.bilgi-gelistirici').textContent = '';
            document.querySelector('.bilgi-aciklama').textContent = 'XML dosyası bulunamadı veya erişilemiyor.';
        });
    document.addEventListener("DOMContentLoaded", function() {
        const btn = document.getElementById("bilgi-btn");
        const ekBilgi = document.getElementById("ek-bilgi");
        btn.addEventListener("click", function() {
            ekBilgi.classList.toggle("goster");
            btn.textContent = ekBilgi.classList.contains("goster") ? "Ek Bilgiyi Gizle" :
                "Ek Bilgi Göster";
        });
    });
    document.getElementById('teknoloji-btn').addEventListener('click', function() {
        fetch('/blade/xml1.xml')
            .then(response => response.text())
            .then(str => (new window.DOMParser()).parseFromString(str, "text/xml"))
            .then(data => {
                const techs = data.getElementsByTagName('teknoloji');
                const ul = document.getElementById('teknolojiler');
                ul.innerHTML = '';
                for (let i = 0; i < techs.length; i++) {
                    const li = document.createElement('li');
                    li.textContent = techs[i].textContent;
                    ul.appendChild(li);
                }
            });
    });
    document.getElementById('iletisim-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const sonuc = document.getElementById('form-sonuc');
        sonuc.textContent = 'Mesajınız başarıyla gönderildi! (Demo)';
        sonuc.classList.add('goster');
        this.reset();
        setTimeout(() => {
            sonuc.classList.remove('goster');
            sonuc.textContent = '';
        }, 3000);
    });
    document.getElementById('eposta-btn').addEventListener('click', function() {
        window.location.href =
            "mailto:{{ $iletisim }}?subject=İletişim&body=Merhaba, Merthtmlcss ile ilgili bir sorum var.";
        const bilgi = document.getElementById('eposta-bilgi');
        bilgi.textContent =
            "E-posta uygulamanız açılmadıysa, {{ $iletisim }} adresine manuel olarak e-posta gönderebilirsiniz.";
        bilgi.classList.add('goster');
        setTimeout(() => {
            bilgi.classList.remove('goster');
            bilgi.textContent = '';
        }, 4000);
    });
    </script>
</body>

</html>