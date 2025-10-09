<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hakkında | Merthtmlcss</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
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
        background: linear-gradient(120deg, #e0f7fa 0%, #f8fafc 100%);
        font-family: var(--font-family);
        margin: 0;
        padding: 0;
        color: #222;
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

    .container {
        max-width: 1000px;
        margin: 60px auto 60px auto;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-xl);
        padding: 60px 50px;
        position: relative;
        overflow: hidden;
        animation: slideInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--gradient-primary);
    }

    h1,
    h2,
    h3,
    h4 {
        color: var(--primary);
        margin-top: 32px;
        font-weight: 700;
        line-height: 1.2;
    }

    h1 {
        font-size: 3em;
        margin-bottom: 24px;
        background: var(--gradient-primary);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: fadeInUp 0.8s ease-out;
    }

    h2 {
        font-size: 2.2em;
        margin-bottom: 16px;
        position: relative;
        padding-bottom: 10px;
    }

    h2::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 60px;
        height: 3px;
        background: var(--gradient-primary);
        border-radius: 2px;
    }

    h3 {
        font-size: 1.6em;
        margin-bottom: 12px;
        color: var(--secondary);
    }

    p,
    li,
    td,
    th {
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
        box-shadow: 0 2px 12px rgba(14, 160, 160, 0.13);
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
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.10);
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

    th,
    td {
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

    .faq-question.open+.faq-answer {
        display: block;
        animation: fadeIn 0.5s;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
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
        box-shadow: 0 4px 16px rgba(14, 160, 160, 0.13);
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .hakkinda-btn:hover {
        background: linear-gradient(90deg, #097c7c 60%, #0e5c7c 100%);
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 8px 24px rgba(14, 160, 160, 0.18);
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
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque euismod, nisi eu consectetur
                consectetur, nisl nisi consectetur nisi, euismod euismod nisi nisi euismod. (Buraya detaylı biyografi
                metni eklenebilir.)</p>
        </div>
        <div class="section">
            <h2>Vizyon & Misyon</h2>
            <div class="highlight">Vizyonum: Modern ve erişilebilir web uygulamaları geliştirmek.<br>Misyonum: Açık
                kaynak projelerle topluluğa katkı sağlamak.</div>
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
                <tr>
                    <th>Teknoloji</th>
                    <th>Düzey</th>
                </tr>
                <tr>
                    <td>HTML/CSS</td>
                    <td>İleri</td>
                </tr>
                <tr>
                    <td>JavaScript</td>
                    <td>İleri</td>
                </tr>
                <tr>
                    <td>PHP</td>
                    <td>Orta</td>
                </tr>
                <tr>
                    <td>Python</td>
                    <td>Orta</td>
                </tr>
                <tr>
                    <td>Laravel</td>
                    <td>Orta</td>
                </tr>
                <tr>
                    <td>Flask</td>
                    <td>Orta</td>
                </tr>
                <tr>
                    <td>MySQL</td>
                    <td>İleri</td>
                </tr>
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
                <div class="faq-answer">Modern web teknolojileriyle hazırlanmış, açık kaynak kodlu bir örnek projedir.
                </div>
                <div class="faq-question">Projeye nasıl katkı sağlayabilirim?</div>
                <div class="faq-answer">GitHub üzerinden pull request gönderebilir veya iletişim formunu
                    kullanabilirsiniz.</div>
                <div class="faq-question">Hangi teknolojiler kullanıldı?</div>
                <div class="faq-answer">HTML, CSS, JS, PHP, Python, Laravel, Flask, Blade, Bootstrap ve daha fazlası.
                </div>
            </div>
        </div>
        <div class="section">
            <h2>İletişim</h2>
            <form id="iletisim-form">
                <input type="text" id="ad" placeholder="Adınız" required
                    style="padding:8px 12px;margin-bottom:10px;width:100%;border-radius:6px;border:1px solid #b2dfdb;">
                <input type="email" id="email" placeholder="E-posta" required
                    style="padding:8px 12px;margin-bottom:10px;width:100%;border-radius:6px;border:1px solid #b2dfdb;">
                <textarea id="mesaj" placeholder="Mesajınız" required
                    style="padding:8px 12px;margin-bottom:10px;width:100%;border-radius:6px;border:1px solid #b2dfdb;"></textarea>
                <button type="submit" class="hakkinda-btn" style="width:100%;">Gönder</button>
            </form>
            <div id="form-sonuc" style="margin-top:16px;color:#0ea0a0;font-weight:bold;"></div>
        </div>
        <div class="section">
            <h2>Ekstra Açıklamalar</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque euismod, nisi eu consectetur
                consectetur, nisl nisi consectetur nisi, euismod euismod nisi nisi euismod. (Buraya uzun açıklamalar,
                vizyon, misyon, hedefler, topluluk katkıları, vs. eklenebilir.)</p>
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
    // Modern XML veri çekme fonksiyonu
    async function loadXMLData() {
        try {
            const response = await fetch('hakkinda.xml');
            if (!response.ok) throw new Error('XML dosyası bulunamadı veya erişilemiyor.');

            const xmlText = await response.text();
            const xml = new DOMParser().parseFromString(xmlText, 'text/xml');

            // XML parsing hatalarını kontrol et
            const parserError = xml.querySelector('parsererror');
            if (parserError) {
                throw new Error('XML parsing hatası: ' + parserError.textContent);
            }

            // Verileri güvenli şekilde çek
            const baslik = xml.querySelector('baslik')?.textContent?.trim() || 'Hakkında';
            const aciklama = xml.querySelector('aciklama')?.textContent?.trim() ||
                'Merthtmlcss projesi hakkında bilgiler.';
            const gelistirici = xml.querySelector('gelistirici')?.textContent?.trim() || 'Mert Doğanay';
            const iletisim = xml.querySelector('iletisim')?.textContent?.trim() || 'mertdoganay437@gmail.com';

            // DOM'a yerleştir
            document.getElementById('hakkinda-baslik').textContent = baslik;
            document.getElementById('hakkinda-aciklama').textContent = aciklama;
            document.getElementById('hakkinda-gelistirici').innerHTML = `
                <div class="developer-info">
                    <i class="fas fa-user-circle"></i>
                    <strong>Geliştirici:</strong> ${gelistirici}
                </div>
            `;

            document.getElementById('hakkinda-iletisim').innerHTML = `
                <div class="contact-info">
                    <i class="fas fa-envelope"></i>
                    <strong>İletişim:</strong> 
                    <a href="mailto:${iletisim}" class="contact-link">${iletisim}</a>
                </div>
            `;

            // Sosyal medya
            const sosyal = xml.querySelector('sosyal');
            let sosyalHTML = '';
            if (sosyal) {
                const socialPlatforms = [{
                        tag: 'twitter',
                        icon: 'fab fa-twitter',
                        name: 'Twitter',
                        color: '#1DA1F2'
                    },
                    {
                        tag: 'github',
                        icon: 'fab fa-github',
                        name: 'GitHub',
                        color: '#333'
                    },
                    {
                        tag: 'youtube',
                        icon: 'fab fa-youtube',
                        name: 'YouTube',
                        color: '#FF0000'
                    },
                    {
                        tag: 'linkedin',
                        icon: 'fab fa-linkedin',
                        name: 'LinkedIn',
                        color: '#0077B5'
                    }
                ];

                socialPlatforms.forEach(platform => {
                    const url = sosyal.querySelector(platform.tag)?.textContent?.trim();
                    if (url && url !== '#') {
                        sosyalHTML += `
                            <div class="social-card" style="--social-color: ${platform.color}">
                                <div class="social-icon">
                                    <i class="${platform.icon}"></i>
                                </div>
                                <div class="social-info">
                                    <h5>${platform.name}</h5>
                                    <a href="${url}" target="_blank" rel="noopener" class="social-link">
                                        Profili Görüntüle
                                    </a>
                                </div>
                            </div>
                        `;
                    }
                });
            }

            if (sosyalHTML) {
                document.getElementById('hakkinda-sosyal').innerHTML = `
                    <div class="social-section">
                        <h4><i class="fas fa-share-alt"></i> Sosyal Medya Hesapları</h4>
                        <div class="social-grid">${sosyalHTML}</div>
                    </div>
                `;
            } else {
                document.getElementById('hakkinda-sosyal').innerHTML = `
                    <div class="no-social">
                        <i class="fas fa-info-circle"></i>
                        <p>Henüz sosyal medya linki eklenmemiş</p>
                    </div>
                `;
            }

            // Başarı mesajı
            console.log('XML verisi başarıyla yüklendi');

        } catch (error) {
            console.warn('XML yüklenemedi:', error);
            document.getElementById('hakkinda-baslik').textContent = 'Hakkında bilgisi yüklenemedi.';
            document.getElementById('hakkinda-aciklama').textContent = 'XML dosyası bulunamadı veya erişilemiyor.';
            document.getElementById('hakkinda-gelistirici').innerHTML = '';
            document.getElementById('hakkinda-iletisim').innerHTML = '';
            document.getElementById('hakkinda-sosyal').innerHTML = `
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Hata: ${error.message}</span>
                </div>
            `;
        }
    }

    // Sayfa yüklendiğinde XML verisini yükle
    document.addEventListener('DOMContentLoaded', loadXMLData);
    </script>
</body>

</html>