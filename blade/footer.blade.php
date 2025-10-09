<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bilgilendirme | Merthtmlcss</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
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

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background: var(--gradient-primary);
        font-family: var(--font-family);
        margin: 0;
        padding: 0;
        min-height: 100vh;
        transition: var(--transition);
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

    body.dark {
        background: var(--gradient-dark);
        color: var(--light);
    }

    body.dark::before {
        background:
            radial-gradient(circle at 20% 80%, rgba(14, 160, 160, 0.05) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(255, 179, 71, 0.05) 0%, transparent 50%);
    }

    .bilgi-kutusu {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-xl);
        padding: 48px 40px;
        margin: 80px auto 0 auto;
        max-width: 700px;
        text-align: left;
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

    body.dark .bilgi-kutusu {
        background: rgba(45, 45, 45, 0.95);
        color: var(--light);
        border-color: rgba(14, 160, 160, 0.3);
        backdrop-filter: blur(20px);
    }

    .bilgi-kutusu h2 {
        color: var(--primary);
        margin-bottom: 20px;
        font-size: 2em;
        display: flex;
        align-items: center;
        font-weight: 700;
    }

    .bilgi-kutusu h2::before {
        content: '\f05a';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        margin-right: 12px;
        font-size: 1.2em;
        color: var(--primary);
    }

    .bilgi-kutusu p {
        color: #333;
        font-size: 1.1em;
        margin-bottom: 0;
        line-height: 1.6;
    }

    body.dark .bilgi-kutusu p {
        color: #e0e0e0;
    }

    .bilgi-btn {
        background: linear-gradient(90deg, var(--primary) 60%, var(--secondary) 100%);
        color: var(--light);
        border: none;
        border-radius: 12px;
        padding: 14px 32px;
        font-size: 1em;
        cursor: pointer;
        margin: 10px 10px 10px 0;
        transition: all 0.3s ease;
        box-shadow: 0 4px 16px rgba(14, 160, 160, 0.2);
        font-weight: 600;
        letter-spacing: 0.5px;
        position: relative;
        overflow: hidden;
    }

    .bilgi-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }

    .bilgi-btn:hover::before {
        left: 100%;
    }

    .bilgi-btn:hover {
        background: linear-gradient(90deg, #097c7c 60%, #ff9800 100%);
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 8px 25px rgba(14, 160, 160, 0.3);
    }

    .bilgi-btn:active {
        transform: translateY(-1px) scale(1.02);
    }

    .ek-bilgi {
        margin-top: 20px;
        color: var(--primary);
        font-weight: 600;
        font-size: 1.1em;
        background: linear-gradient(90deg, #e0f7fa 60%, #f8fafc 100%);
        border-left: 4px solid var(--primary);
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(14, 160, 160, 0.1);
        padding: 20px 24px 20px 40px;
        position: relative;
        min-height: 40px;
        max-height: 0;
        opacity: 0;
        overflow: hidden;
        transition: all 0.5s cubic-bezier(.4, 2, .6, 1);
    }

    body.dark .ek-bilgi {
        background: linear-gradient(90deg, rgba(14, 160, 160, 0.1) 60%, rgba(45, 45, 45, 0.8) 100%);
        color: var(--light);
    }

    .ek-bilgi.goster {
        max-height: 300px;
        opacity: 1;
        margin-top: 20px;
    }

    .ek-bilgi::before {
        content: '\f0eb';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        position: absolute;
        left: 12px;
        top: 20px;
        font-size: 1.2em;
        color: var(--primary);
    }

    .theme-toggle {
        position: fixed;
        top: 20px;
        right: 20px;
        background: var(--light);
        color: var(--primary);
        border: 2px solid var(--primary);
        border-radius: 50%;
        width: 50px;
        height: 50px;
        font-size: 1.3em;
        cursor: pointer;
        transition: all 0.3s ease;
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    }

    .theme-toggle:hover {
        background: var(--primary);
        color: var(--light);
        transform: scale(1.1) rotate(180deg);
    }

    body.dark .theme-toggle {
        background: #2d2d2d;
        color: var(--accent);
        border-color: var(--accent);
    }

    /* Gelişmiş CSS sınıfları */
    .glow {
        text-shadow: 0 0 20px rgba(14, 160, 160, 0.6);
        animation: glow 2s ease-in-out infinite alternate;
    }

    @keyframes glow {
        from {
            text-shadow: 0 0 20px rgba(14, 160, 160, 0.6);
        }

        to {
            text-shadow: 0 0 30px rgba(14, 160, 160, 0.8), 0 0 40px rgba(14, 160, 160, 0.4);
        }
    }

    .fade-in {
        opacity: 0;
        animation: fadeIn 0.8s ease-out forwards;
    }

    .delay-1 {
        animation-delay: 0.1s;
    }

    .delay-2 {
        animation-delay: 0.2s;
    }

    .delay-3 {
        animation-delay: 0.3s;
    }

    .delay-4 {
        animation-delay: 0.4s;
    }

    .card {
        background: var(--light);
        border-radius: 16px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        padding: 24px;
        margin: 20px 0;
        border-left: 4px solid var(--primary);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    body.dark .card {
        background: #333;
        color: var(--light);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
    }

    .card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(45deg, transparent, rgba(14, 160, 160, 0.05), transparent);
        transform: translateX(-100%);
        transition: transform 0.6s ease;
    }

    .card:hover::before {
        transform: translateX(100%);
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    }

    body.dark .card:hover {
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4);
    }

    .card-primary {
        border-left-color: var(--primary);
    }

    .card-header {
        margin-bottom: 16px;
    }

    .card-title {
        color: var(--primary);
        margin: 0;
        font-size: 1.3em;
        font-weight: 600;
    }

    .card-body {
        color: #333;
        line-height: 1.7;
    }

    body.dark .card-body {
        color: #e0e0e0;
    }

    /* Yeni özellikler */
    .feature-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin: 20px 0;
    }

    .feature-item {
        background: linear-gradient(135deg, rgba(14, 160, 160, 0.1), rgba(255, 179, 71, 0.1));
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        transition: all 0.3s ease;
        border: 1px solid rgba(14, 160, 160, 0.2);
    }

    body.dark .feature-item {
        background: linear-gradient(135deg, rgba(14, 160, 160, 0.2), rgba(255, 179, 71, 0.2));
        border-color: rgba(14, 160, 160, 0.3);
    }

    .feature-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(14, 160, 160, 0.2);
    }

    .feature-icon {
        font-size: 2em;
        color: var(--primary);
        margin-bottom: 10px;
    }

    .feature-title {
        font-weight: 600;
        color: var(--primary);
        margin-bottom: 8px;
    }

    .feature-desc {
        font-size: 0.9em;
        color: var(--gray);
        line-height: 1.5;
    }

    body.dark .feature-desc {
        color: #ccc;
    }

    .progress-bar {
        width: 100%;
        height: 8px;
        background: rgba(14, 160, 160, 0.2);
        border-radius: 4px;
        overflow: hidden;
        margin: 10px 0;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--primary), var(--secondary));
        border-radius: 4px;
        transition: width 2s ease;
        width: 0%;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideIn {
        from {
            transform: translateX(-100%);
        }

        to {
            transform: translateX(0);
        }
    }

    @keyframes bounce {

        0%,
        20%,
        50%,
        80%,
        100% {
            transform: translateY(0);
        }

        40% {
            transform: translateY(-10px);
        }

        60% {
            transform: translateY(-5px);
        }
    }

    .bounce {
        animation: bounce 2s infinite;
    }

    /* Responsive tasarım */
    @media (max-width: 768px) {
        .bilgi-kutusu {
            margin: 20px;
            padding: 24px 20px;
        }

        .bilgi-kutusu h2 {
            font-size: 1.6em;
        }

        .feature-grid {
            grid-template-columns: 1fr;
        }

        .theme-toggle {
            top: 15px;
            right: 15px;
            width: 45px;
            height: 45px;
            font-size: 1.1em;
        }
    }

    /* Loading animasyonu */
    .loading {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: white;
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
    </style>
</head>

<body>
    <button class="theme-toggle" onclick="toggleTheme()" title="Tema Değiştir">
        <span class="icon-moon">🌙</span>
        <span class="icon-sun" style="display:none;">☀️</span>
    </button>

    <div class="bilgi-kutusu">
        <h2 class="glow">Bilgilendirme</h2>
        <p>Bu sayfa, Merthtmlcss projesinin modern web teknolojileriyle hazırlanmış örnek bir bilgilendirme bölümüdür.
            Kodlar ve içerik açık kaynak olarak sunulmaktadır. Geliştirici: Mert</p>

        <div class="fade-in delay-1" style="margin: 20px 0;">
            <button class="bilgi-btn" onclick="startProject()">
                <i class="fas fa-rocket"></i> Başla
            </button>
            <button class="bilgi-btn" onclick="toggleTheme()">
                <i class="fas fa-moon"></i> Tema
            </button>
            <button class="bilgi-btn" onclick="showFeatures()">
                <i class="fas fa-star"></i> Özellikler
            </button>
        </div>

        <div class="card card-primary fade-in delay-2" style="margin: 20px 0;">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-code"></i> Teknolojiler
                </h3>
            </div>
            <div class="card-body">
                <div class="feature-grid" id="featureGrid" style="display: none;">
                    <div class="feature-item">
                        <div class="feature-icon">🌐</div>
                        <div class="feature-title">HTML5</div>
                        <div class="feature-desc">Modern web standartları</div>
                        <div class="progress-bar">
                            <div class="progress-fill" data-width="95"></div>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">🎨</div>
                        <div class="feature-title">CSS3</div>
                        <div class="feature-desc">Gelişmiş stillendirme</div>
                        <div class="progress-bar">
                            <div class="progress-fill" data-width="90"></div>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">⚡</div>
                        <div class="feature-title">JavaScript</div>
                        <div class="feature-desc">Dinamik etkileşim</div>
                        <div class="progress-bar">
                            <div class="progress-fill" data-width="85"></div>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">🐘</div>
                        <div class="feature-title">PHP</div>
                        <div class="feature-desc">Sunucu tarafı işlem</div>
                        <div class="progress-bar">
                            <div class="progress-fill" data-width="80"></div>
                        </div>
                    </div>
                </div>
                <div id="defaultContent">
                    Bu proje Blade, PHP, HTML, CSS, JavaScript ve Python teknolojileriyle entegre edilmiştir.
                </div>
            </div>
        </div>

        <div class="fade-in delay-3">
            <div class="feature-item bounce">
                <div class="feature-icon">🚀</div>
                <div class="feature-title">Hızlı Geliştirme</div>
                <div class="feature-desc">Modern araçlarla hızlı proje geliştirme</div>
            </div>
        </div>

        <button class="bilgi-btn" id="bilgi-btn">
            <i class="fas fa-info-circle"></i> Ek Bilgi Göster
        </button>

        <div class="ek-bilgi" id="ek-bilgi">
            <strong>Proje Özellikleri:</strong><br><br>
            ✅ Responsive tasarım<br>
            ✅ Modern CSS animasyonları<br>
            ✅ Tema değiştirme özelliği<br>
            ✅ JavaScript etkileşimleri<br>
            ✅ Font Awesome ikonları<br>
            ✅ Google Fonts entegrasyonu<br>
            ✅ Gradient arka planlar<br>
            ✅ Hover efektleri<br>
            ✅ Loading animasyonları<br>
            ✅ Progress bar'lar
        </div>
    </div>

    <script>
    // Tema değiştirme fonksiyonu
    function toggleTheme() {
        const body = document.body;
        const iconMoon = document.querySelector('.icon-moon');
        const iconSun = document.querySelector('.icon-sun');

        body.classList.toggle('dark');

        if (body.classList.contains('dark')) {
            iconMoon.style.display = 'none';
            iconSun.style.display = 'block';
            localStorage.setItem('theme', 'dark');
        } else {
            iconMoon.style.display = 'block';
            iconSun.style.display = 'none';
            localStorage.setItem('theme', 'light');
        }
    }

    // Proje başlatma fonksiyonu
    function startProject() {
        const button = event.target.closest('.bilgi-btn');
        const originalText = button.innerHTML;

        button.innerHTML = '<span class="loading"></span> Başlatılıyor...';
        button.disabled = true;

        setTimeout(() => {
            button.innerHTML = originalText;
            button.disabled = false;
            showMessage('Proje başarıyla başlatıldı! 🎉', 'success');
        }, 2000);
    }

    // Özellikler gösterme fonksiyonu
    function showFeatures() {
        const featureGrid = document.getElementById('featureGrid');
        const defaultContent = document.getElementById('defaultContent');

        if (featureGrid.style.display === 'none') {
            featureGrid.style.display = 'grid';
            defaultContent.style.display = 'none';

            // Progress bar animasyonları
            setTimeout(() => {
                const progressBars = document.querySelectorAll('.progress-fill');
                progressBars.forEach(bar => {
                    const width = bar.getAttribute('data-width');
                    bar.style.width = width + '%';
                });
            }, 300);
        } else {
            featureGrid.style.display = 'none';
            defaultContent.style.display = 'block';
        }
    }

    // Mesaj gösterme fonksiyonu
    function showMessage(text, type) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message-${type}`;
        messageDiv.style.cssText = `
                    position: fixed;
                    top: 20px;
                    left: 50%;
                    transform: translateX(-50%);
                    padding: 12px 24px;
                    border-radius: 8px;
                    color: white;
                    font-weight: 600;
                    z-index: 1001;
                    animation: slideIn 0.3s ease;
                `;

        if (type === 'success') {
            messageDiv.style.background = 'linear-gradient(45deg, #51cf66, #40c057)';
        } else if (type === 'error') {
            messageDiv.style.background = 'linear-gradient(45deg, #ff4757, #ff3742)';
        }

        messageDiv.textContent = text;
        document.body.appendChild(messageDiv);

        setTimeout(() => {
            messageDiv.remove();
        }, 3000);
    }

    // Sayfa yüklendiğinde çalışacak kodlar
    document.addEventListener("DOMContentLoaded", function() {
        // Tema kontrolü
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            document.body.classList.add('dark');
            document.querySelector('.icon-moon').style.display = 'none';
            document.querySelector('.icon-sun').style.display = 'block';
        }

        // Ek bilgi toggle
        const btn = document.getElementById("bilgi-btn");
        const ekBilgi = document.getElementById("ek-bilgi");

        btn.addEventListener("click", function() {
            ekBilgi.classList.toggle("goster");
            if (ekBilgi.classList.contains("goster")) {
                btn.innerHTML = '<i class="fas fa-eye-slash"></i> Ek Bilgiyi Gizle';
            } else {
                btn.innerHTML = '<i class="fas fa-info-circle"></i> Ek Bilgi Göster';
            }
        });

        // Sayfa yüklendiğinde animasyon
        setTimeout(() => {
            document.body.style.opacity = '1';
        }, 100);
    });

    // Sayfa yüklendiğinde opacity animasyonu
    document.body.style.opacity = '0';
    document.body.style.transition = 'opacity 0.5s ease';
    </script>
</body>

</html>