<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hakkında | Merthtmlcss</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <style>
        body {
            background: linear-gradient(120deg, #e0f7fa 0%, #f8fafc 100%);
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #222;
        }
        .container {
            max-width: 900px;
            margin: 40px auto 40px auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 32px rgba(14,160,160,0.10);
            padding: 48px 36px 48px 36px;
        }
        h1, h2, h3, h4 {
            color: #0ea0a0;
            margin-top: 32px;
        }
        h1 {
            font-size: 2.5em;
            margin-bottom: 18px;
        }
        h2 {
            font-size: 2em;
            margin-bottom: 12px;
        }
        h3 {
            font-size: 1.4em;
            margin-bottom: 8px;
        }
        p, li, td, th {
            font-size: 1.13em;
            line-height: 1.7;
        }
        .section {
            margin-bottom: 48px;
        }
        .profile-img {
            width: 160px;
            height: 160px;
            object-fit: cover;
            border-radius: 50%;
            box-shadow: 0 2px 12px rgba(14,160,160,0.13);
            margin-bottom: 18px;
        }
        .highlight {
            background: #e0f7fa;
            border-left: 5px solid #0ea0a0;
            padding: 12px 18px;
            border-radius: 8px;
            margin: 18px 0;
        }
        .code-block {
            background: #23272e;
            color: #e3e3e3;
            border-radius: 8px;
            padding: 18px 16px;
            margin: 18px 0 0 0;
            font-size: 1em;
            overflow-x: auto;
            box-shadow: 0 2px 8px rgba(0,0,0,0.10);
        }
        .code-block code {
            font-family: 'Fira Mono', 'Consolas', 'Menlo', monospace;
            color: #b5f4a5;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 18px 0;
        }
        th, td {
            border: 1px solid #b2dfdb;
            padding: 10px 8px;
            text-align: left;
        }
        th {
            background: #e0f7fa;
        }
        .faq {
            margin: 24px 0;
        }
        .faq-question {
            cursor: pointer;
            font-weight: bold;
            color: #0ea0a0;
            margin-bottom: 6px;
        }
        .faq-answer {
            display: none;
            margin-bottom: 12px;
            color: #333;
        }
        .faq-question.open + .faq-answer {
            display: block;
            animation: fadeIn 0.5s;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .social-links a {
            display: inline-block;
            margin-right: 16px;
            color: #0ea0a0;
            font-weight: bold;
            text-decoration: none;
            font-size: 1.2em;
        }
        .social-links a:hover {
            text-decoration: underline;
            color: #097c7c;
        }
        .hakkinda-btn {
            background: linear-gradient(90deg, #0ea0a0 60%, #0e7ca0 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 14px 36px;
            font-size: 1.1em;
            cursor: pointer;
            margin-top: 32px;
            transition: background 0.2s, transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 16px rgba(14,160,160,0.13);
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .hakkinda-btn:hover {
            background: linear-gradient(90deg, #097c7c 60%, #0e5c7c 100%);
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 8px 24px rgba(14,160,160,0.18);
        }
        .divider {
            border: none;
            border-top: 2px solid #e0f7fa;
            margin: 40px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Profil Fotoğrafı" class="profile-img">
        <h1 id="hakkinda-baslik"></h1>
        <p id="hakkinda-aciklama"></p>
        <div id="hakkinda-gelistirici"></div>
        <div id="hakkinda-iletisim"></div>
        <div id="hakkinda-sosyal"></div>
        <div class="section">
            <h2>Biyografi</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque euismod, nisi eu consectetur consectetur, nisl nisi consectetur nisi, euismod euismod nisi nisi euismod. (Buraya detaylı biyografi metni eklenebilir.)</p>
        </div>
        <div class="section">
            <h2>Vizyon & Misyon</h2>
            <div class="highlight">Vizyonum: Modern ve erişilebilir web uygulamaları geliştirmek.<br>Misyonum: Açık kaynak projelerle topluluğa katkı sağlamak.</div>
        </div>
        <div class="section">
            <h2>Projeler</h2>
            <ul>
                <li>Merthtmlcss - Modern web örnek projesi</li>
                <li>Blogify - Kişisel blog platformu</li>
                <li>APIConnect - RESTful API entegrasyon aracı</li>
                <li>EduLearn - Online eğitim platformu</li>
                <li>Ve daha fazlası...</li>
            </ul>
        </div>
        <div class="section">
            <h2>Kullanılan Teknolojiler</h2>
            <ul>
                <li>HTML, CSS, JavaScript, PHP, Python</li>
                <li>Laravel, Flask, Blade, Bootstrap</li>
                <li>MySQL, SQLite, MongoDB</li>
                <li>Git, Docker, Linux</li>
            </ul>
        </div>
        <div class="section">
            <h2>Yetkinlikler</h2>
            <table>
                <tr><th>Teknoloji</th><th>Düzey</th></tr>
                <tr><td>HTML/CSS</td><td>İleri</td></tr>
                <tr><td>JavaScript</td><td>İleri</td></tr>
                <tr><td>PHP</td><td>Orta</td></tr>
                <tr><td>Python</td><td>Orta</td></tr>
                <tr><td>Laravel</td><td>Orta</td></tr>
                <tr><td>Flask</td><td>Orta</td></tr>
                <tr><td>MySQL</td><td>İleri</td></tr>
            </table>
        </div>
        <div class="section">
            <h2>Eğitimler & Sertifikalar</h2>
            <ul>
                <li>Bilgisayar Mühendisliği Lisans - Örnek Üniversite</li>
                <li>Full Stack Web Development Sertifikası - Online Platform</li>
                <li>Python for Everybody - Coursera</li>
                <li>Responsive Web Tasarımı - FreeCodeCamp</li>
            </ul>
        </div>
        <div class="section">
            <h2>Kod Örneği</h2>
            <div class="code-block"><code>function selamla(isim) {
    return `Merhaba, ${isim}!`;
}
console.log(selamla('Dünya'));
</code></div>
        </div>
        <div class="section">
            <h2>Referanslar</h2>
            <ul>
                <li>Ahmet Yılmaz - Yazılım Müdürü, Örnek Şirket</li>
                <li>Ayşe Demir - Proje Yöneticisi, Başka Şirket</li>
            </ul>
        </div>
        <div class="section">
            <h2>Hobiler</h2>
            <ul>
                <li>Yüzme, bisiklet, satranç</li>
                <li>Açık kaynak projelere katkı</li>
                <li>Teknoloji blogları yazmak</li>
            </ul>
        </div>
        <div class="section">
            <h2>Sıkça Sorulan Sorular (SSS)</h2>
            <div class="faq">
                <div class="faq-question">Merthtmlcss projesi nedir?</div>
                <div class="faq-answer">Modern web teknolojileriyle hazırlanmış, açık kaynak kodlu bir örnek projedir.</div>
                <div class="faq-question">Projeye nasıl katkı sağlayabilirim?</div>
                <div class="faq-answer">GitHub üzerinden pull request gönderebilir veya iletişim formunu kullanabilirsiniz.</div>
                <div class="faq-question">Hangi teknolojiler kullanıldı?</div>
                <div class="faq-answer">HTML, CSS, JS, PHP, Python, Laravel, Flask, Blade, Bootstrap ve daha fazlası.</div>
            </div>
        </div>
        <div class="section">
            <h2>İletişim</h2>
            <form id="iletisim-form">
                <input type="text" id="ad" placeholder="Adınız" required style="padding:8px 12px;margin-bottom:10px;width:100%;border-radius:6px;border:1px solid #b2dfdb;">
                <input type="email" id="email" placeholder="E-posta" required style="padding:8px 12px;margin-bottom:10px;width:100%;border-radius:6px;border:1px solid #b2dfdb;">
                <textarea id="mesaj" placeholder="Mesajınız" required style="padding:8px 12px;margin-bottom:10px;width:100%;border-radius:6px;border:1px solid #b2dfdb;"></textarea>
                <button type="submit" class="hakkinda-btn" style="width:100%;">Gönder</button>
            </form>
            <div id="form-sonuc" style="margin-top:16px;color:#0ea0a0;font-weight:bold;"></div>
        </div>
        <div class="section">
            <h2>Ekstra Açıklamalar</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque euismod, nisi eu consectetur consectetur, nisl nisi consectetur nisi, euismod euismod nisi nisi euismod. (Buraya uzun açıklamalar, vizyon, misyon, hedefler, topluluk katkıları, vs. eklenebilir.)</p>
        </div>
        <hr class="divider">
        <a href="#" id="anasayfa-btn" class="hakkinda-btn">Anasayfaya Dön</a>
    </div>
    <script>
    // SSS aç/kapa
    document.querySelectorAll('.faq-question').forEach(function(q) {
        q.addEventListener('click', function() {
            this.classList.toggle('open');
        });
    });
    // Anasayfaya yönlendirme
    document.getElementById('anasayfa-btn').addEventListener('click', function(e) {
        e.preventDefault();
        window.location.href = '/';
    });
    // İletişim formu örnek JS
    document.getElementById('iletisim-form').addEventListener('submit', function(e) {
        e.preventDefault();
        document.getElementById('form-sonuc').textContent = 'Mesajınız başarıyla gönderildi! (Demo)';
        this.reset();
    });
    // XML'den veri çekme
    fetch('hakkinda.xml')
        .then(response => response.text())
        .then(str => (new window.DOMParser()).parseFromString(str, "text/xml"))
        .then(data => {
            document.getElementById('hakkinda-baslik').textContent = data.getElementsByTagName('baslik')[0]?.textContent || '';
            document.getElementById('hakkinda-aciklama').textContent = data.getElementsByTagName('aciklama')[0]?.textContent || '';
            document.getElementById('hakkinda-gelistirici').textContent = 'Geliştirici: ' + (data.getElementsByTagName('gelistirici')[0]?.textContent || '');
            const iletisim = data.getElementsByTagName('iletisim')[0]?.textContent;
            if(iletisim) {
                document.getElementById('hakkinda-iletisim').innerHTML = '<b>İletişim:</b> <a href="mailto:' + iletisim + '">' + iletisim + '</a>';
            }
            const twitter = data.getElementsByTagName('twitter')[0]?.textContent;
            const github = data.getElementsByTagName('github')[0]?.textContent;
            let sosyal = '';
            if(twitter) sosyal += '<a href="' + twitter + '" target="_blank">Twitter</a> ';
            if(github) sosyal += '<a href="' + github + '" target="_blank">GitHub</a>';
            if(sosyal) document.getElementById('hakkinda-sosyal').innerHTML = '<b>Sosyal:</b> ' + sosyal;
        });
    </script>
</body>
</html> 