# Kapsamlı CSS ve JavaScript örneği çıktısı

def print_css_js():
    css = '''
/* Modern CSS Framework */
:root {
    --primary: #0ea0a0;
    --secondary: #ffb347;
    --success: #2ed573;
    --error: #ff4757;
    --dark: #222;
    --light: #fff;
}

* { margin: 0; padding: 0; box-sizing: border-box; }

body { 
    font-family: 'Segoe UI', sans-serif; 
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 20px;
}

.container {
    max-width: 800px;
    margin: 0 auto;
    background: var(--light);
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.button { 
    background: var(--primary); 
    color: var(--light); 
    border: none; 
    border-radius: 8px; 
    padding: 12px 24px; 
    cursor: pointer; 
    transition: all 0.3s ease;
    font-weight: 600;
    margin: 5px;
}

.button:hover { 
    background: var(--secondary); 
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.card { 
    background: var(--light); 
    border-radius: 12px; 
    box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
    padding: 20px; 
    margin: 16px 0;
    border-left: 4px solid var(--primary);
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

body.dark { 
    background: var(--dark); 
    color: var(--light); 
}

body.dark .container {
    background: #333;
    color: var(--light);
}

body.dark .card {
    background: #444;
    color: var(--light);
}

.alert {
    padding: 15px;
    border-radius: 8px;
    margin: 10px 0;
    font-weight: 600;
}

.alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.fade-in { animation: fadeIn 0.5s ease-out; }
'''
    
    js = '''
// Modern JavaScript Framework
class AppManager {
    constructor() {
        this.init();
    }
    
    init() {
        this.setupEventListeners();
        this.loadData();
        console.log("🚀 App başlatıldı");
    }
    
    setupEventListeners() {
        // Tema değiştirme
        document.querySelectorAll('.theme-toggle').forEach(btn => {
            btn.addEventListener('click', () => this.toggleTheme());
        });
        
        // Buton animasyonları
        document.querySelectorAll('.button').forEach(btn => {
            btn.addEventListener('click', (e) => this.buttonClick(e));
        });
        
        // Kart etkileşimleri
        document.querySelectorAll('.card').forEach(card => {
            card.addEventListener('mouseenter', () => this.cardHover(card));
        });
    }
    
    toggleTheme() {
        document.body.classList.toggle('dark');
        this.showAlert('Tema değiştirildi!', 'success');
    }
    
    buttonClick(e) {
        const btn = e.target;
        btn.style.transform = 'scale(0.95)';
        setTimeout(() => {
            btn.style.transform = '';
        }, 150);
        
        this.showAlert('Butona tıklandı!', 'success');
    }
    
    cardHover(card) {
        card.classList.add('fade-in');
    }
    
    showAlert(message, type = 'success') {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        alert.textContent = message;
        
        document.querySelector('.container').appendChild(alert);
        
        setTimeout(() => {
            alert.remove();
        }, 3000);
    }
    
    loadData() {
        // Simüle edilmiş veri yükleme
        setTimeout(() => {
            this.showAlert('Veriler yüklendi!', 'success');
        }, 1000);
    }
}

// Tema değiştirme fonksiyonu
function temaDegistir() {
    document.body.classList.toggle('dark');
}

// App'i başlat
document.addEventListener('DOMContentLoaded', () => {
    new AppManager();
});
'''
    
    html = f'''
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern CSS & JS Framework</title>
    <style>{css}</style>
</head>
<body>
    <div class="container">
        <h1 style="text-align: center; margin-bottom: 30px; color: var(--primary);">
            🎨 Modern CSS & JS Framework
        </h1>
        
        <div class="card fade-in">
            <h3>Hoş Geldiniz!</h3>
            <p>Bu modern CSS ve JavaScript framework'ü ile güzel arayüzler oluşturabilirsiniz.</p>
        </div>
        
        <div style="text-align: center; margin: 20px 0;">
            <button class="button theme-toggle">🌙 Tema Değiştir</button>
            <button class="button" onclick="temaDegistir()">🎨 Alternatif Tema</button>
        </div>
        
        <div class="grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 30px;">
            <div class="card">
                <h4>✨ Özellik 1</h4>
                <p>Modern CSS değişkenleri ve animasyonlar</p>
            </div>
            <div class="card">
                <h4>🚀 Özellik 2</h4>
                <p>Responsive tasarım ve hover efektleri</p>
            </div>
            <div class="card">
                <h4>⚡ Özellik 3</h4>
                <p>JavaScript ile dinamik etkileşimler</p>
            </div>
        </div>
    </div>
    
    <script>{js}</script>
</body>
</html>
'''
    
    # HTML dosyasını kaydet
    with open('modern-app.html', 'w', encoding='utf-8') as f:
        f.write(html)
    
    print("✅ Modern CSS & JS Framework oluşturuldu: modern-app.html")
    print(html)

if __name__ == "__main__":
    print_css_js()
