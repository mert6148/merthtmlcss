# Animasyonlu CSS ve Interaktif JavaScript örneği çıktısı

def print_css_js():
    css = '''
/* Animasyonlu CSS Framework */
:root {
    --primary: #667eea;
    --secondary: #764ba2;
    --accent: #ff6b6b;
    --success: #51cf66;
    --warning: #ffd43b;
    --error: #ff6b6b;
}

* { margin: 0; padding: 0; box-sizing: border-box; }

body { 
    font-family: 'Segoe UI', sans-serif; 
    background: linear-gradient(45deg, var(--primary), var(--secondary));
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.container {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    text-align: center;
    max-width: 500px;
    width: 100%;
}

.button { 
    background: linear-gradient(45deg, var(--primary), var(--secondary));
    color: white; 
    border: none; 
    border-radius: 25px; 
    padding: 15px 30px; 
    cursor: pointer; 
    transition: all 0.3s ease;
    font-weight: 600;
    font-size: 16px;
    margin: 10px;
    position: relative;
    overflow: hidden;
}

.button::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s;
}

.button:hover::before {
    left: 100%;
}

.button:hover { 
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}

.button:active {
    transform: translateY(-1px) scale(1.02);
}

.card { 
    background: white; 
    border-radius: 15px; 
    box-shadow: 0 8px 25px rgba(0,0,0,0.1); 
    padding: 25px; 
    margin: 20px 0;
    border: 2px solid transparent;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, var(--primary), var(--secondary), var(--accent));
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.card:hover::before {
    transform: scaleX(1);
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    border-color: var(--primary);
}

/* Animasyonlar */
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

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-10px); }
    60% { transform: translateY(-5px); }
}

.fade-in { animation: fadeInUp 0.6s ease-out; }
.pulse { animation: pulse 2s infinite; }
.rotate { animation: rotate 2s linear infinite; }
.bounce { animation: bounce 1s infinite; }

/* Responsive */
@media (max-width: 600px) {
    .container { padding: 20px; }
    .button { padding: 12px 24px; font-size: 14px; }
}
'''
    
    js = '''
// Interaktif JavaScript Framework
class InteractiveApp {
    constructor() {
        this.counter = 0;
        this.isAnimating = false;
        this.init();
    }
    
    init() {
        this.setupEventListeners();
        this.startAutoAnimation();
        console.log("🎭 Interaktif App başlatıldı");
    }
    
    setupEventListeners() {
        // Buton tıklama efektleri
        document.querySelectorAll('.button').forEach(btn => {
            btn.addEventListener('click', (e) => this.handleButtonClick(e));
        });
        
        // Kart hover efektleri
        document.querySelectorAll('.card').forEach(card => {
            card.addEventListener('mouseenter', () => this.handleCardHover(card));
            card.addEventListener('click', () => this.handleCardClick(card));
        });
        
        // Sayfa yükleme animasyonu
        window.addEventListener('load', () => this.pageLoadAnimation());
    }
    
    handleButtonClick(e) {
        const btn = e.target;
        
        // Tıklama animasyonu
        btn.style.transform = 'scale(0.95)';
        setTimeout(() => {
            btn.style.transform = '';
        }, 150);
        
        // Sayaç artırma
        this.counter++;
        this.updateCounter();
        
        // Rastgele renk değişimi
        this.changeRandomColor(btn);
        
        // Mesaj gösterimi
        this.showMessage(`Butona ${this.counter}. kez tıklandı! 🎉`);
    }
    
    handleCardHover(card) {
        if (!this.isAnimating) {
            card.classList.add('pulse');
            setTimeout(() => {
                card.classList.remove('pulse');
            }, 1000);
        }
    }
    
    handleCardClick(card) {
        this.isAnimating = true;
        
        // Kart animasyonu
        card.style.transform = 'rotate(5deg) scale(1.05)';
        card.style.transition = 'all 0.3s ease';
        
        setTimeout(() => {
            card.style.transform = 'rotate(0deg) scale(1)';
            this.isAnimating = false;
        }, 300);
        
        this.showMessage('Kart tıklandı! ✨');
    }
    
    changeRandomColor(element) {
        const colors = ['#667eea', '#764ba2', '#ff6b6b', '#51cf66', '#ffd43b'];
        const randomColor = colors[Math.floor(Math.random() * colors.length)];
        
        element.style.background = `linear-gradient(45deg, ${randomColor}, ${this.adjustBrightness(randomColor, -20)})`;
        
        setTimeout(() => {
            element.style.background = '';
        }, 1000);
    }
    
    adjustBrightness(color, percent) {
        const num = parseInt(color.replace("#",""), 16);
        const amt = Math.round(2.55 * percent);
        const R = (num >> 16) + amt;
        const G = (num >> 8 & 0x00FF) + amt;
        const B = (num & 0x0000FF) + amt;
        return "#" + (0x1000000 + (R<255?R<1?0:R:255)*0x10000 + (G<255?G<1?0:G:255)*0x100 + (B<255?B<1?0:B:255)).toString(16).slice(1);
    }
    
    updateCounter() {
        const counterElement = document.getElementById('counter');
        if (counterElement) {
            counterElement.textContent = this.counter;
            counterElement.classList.add('bounce');
            setTimeout(() => {
                counterElement.classList.remove('bounce');
            }, 1000);
        }
    }
    
    showMessage(message) {
        const messageDiv = document.createElement('div');
        messageDiv.textContent = message;
        messageDiv.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 15px 20px;
            border-radius: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            z-index: 1000;
            animation: fadeInUp 0.5s ease-out;
        `;
        
        document.body.appendChild(messageDiv);
        
        setTimeout(() => {
            messageDiv.style.animation = 'fadeInUp 0.5s ease-in reverse';
            setTimeout(() => {
                messageDiv.remove();
            }, 500);
        }, 2000);
    }
    
    startAutoAnimation() {
        setInterval(() => {
            const cards = document.querySelectorAll('.card');
            const randomCard = cards[Math.floor(Math.random() * cards.length)];
            if (randomCard && !this.isAnimating) {
                randomCard.classList.add('pulse');
                setTimeout(() => {
                    randomCard.classList.remove('pulse');
                }, 1000);
            }
        }, 5000);
    }
    
    pageLoadAnimation() {
        const elements = document.querySelectorAll('.container > *');
        elements.forEach((el, index) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                el.style.transition = 'all 0.6s ease-out';
                el.style.opacity = '1';
                el.style.transform = 'translateY(0)';
            }, index * 200);
        });
    }
}

// Tema değiştirme fonksiyonu
function temaDegistir() {
    document.body.style.background = document.body.style.background.includes('45deg') 
        ? 'linear-gradient(135deg, #ff6b6b, #feca57)' 
        : 'linear-gradient(45deg, #667eea, #764ba2)';
}

// App'i başlat
document.addEventListener('DOMContentLoaded', () => {
    new InteractiveApp();
});
'''
    
    html = f'''
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Animasyonlu CSS & JS Framework</title>
    <style>{css}</style>
</head>
<body>
    <div class="container fade-in">
        <h1 style="margin-bottom: 20px; color: var(--primary); font-size: 2em;">
            🎭 Animasyonlu Framework
        </h1>
        
        <div class="card">
            <h3>Hoş Geldiniz! 👋</h3>
            <p>Bu interaktif CSS ve JavaScript framework'ü ile harika animasyonlar oluşturabilirsiniz.</p>
            <p style="margin-top: 10px; font-weight: bold; color: var(--accent);">
                Tıklama Sayısı: <span id="counter">0</span>
            </p>
        </div>
        
        <div style="margin: 20px 0;">
            <button class="button">🎨 Animasyonlu Buton</button>
            <button class="button" onclick="temaDegistir()">🌅 Tema Değiştir</button>
        </div>
        
        <div class="card">
            <h4>✨ Özellikler</h4>
            <ul style="text-align: left; margin-top: 10px;">
                <li>Hover animasyonları</li>
                <li>Tıklama efektleri</li>
                <li>Otomatik animasyonlar</li>
                <li>Responsive tasarım</li>
            </ul>
        </div>
        
        <div class="card">
            <h4>🚀 Deneyin</h4>
            <p>Butonlara tıklayın, kartların üzerine gelin ve animasyonları keşfedin!</p>
        </div>
    </div>
    
    <script>{js}</script>
</body>
</html>
'''
    
    # HTML dosyasını kaydet
    with open('animasyonlu-app.html', 'w', encoding='utf-8') as f:
        f.write(html)
    
    print("✅ Animasyonlu CSS & JS Framework oluşturuldu: animasyonlu-app.html")
    print(html)

if __name__ == "__main__":
    print_css_js()
