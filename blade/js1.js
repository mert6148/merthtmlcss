// Merthtmlcss - Tema Yöneticisi ve Animasyon Kontrolcüsü
class ThemeManager {
    constructor() {
        this.currentTheme = localStorage.getItem('theme') || 'light';
        this.init();
    }

    init() {
        this.applyTheme();
        this.createThemeToggle();
        console.log("🎨 Merthtmlcss Tema Yöneticisi başlatıldı");
    }

    applyTheme() {
        document.documentElement.setAttribute('data-theme', this.currentTheme);
        document.body.className = this.currentTheme;

        // Tema değişikliği animasyonu
        document.body.style.transition = 'all 0.3s ease-in-out';

        if (this.currentTheme === 'dark') {
            document.body.style.backgroundColor = '#1a1a1a';
            document.body.style.color = '#ffffff';
        } else {
            document.body.style.backgroundColor = '#ffffff';
            document.body.style.color = '#333333';
        }
    }

    toggleTheme() {
        this.currentTheme = this.currentTheme === 'light' ? 'dark' : 'light';
        localStorage.setItem('theme', this.currentTheme);
        this.applyTheme();

        // Tema değişikliği bildirimi
        this.showNotification(`Tema ${this.currentTheme === 'dark' ? 'karanlık' : 'aydınlık'} moda geçirildi`);
    }

    createThemeToggle() {
        const toggle = document.createElement('button');
        toggle.innerHTML = this.currentTheme === 'light' ? '🌙' : '☀️';
        toggle.className = 'theme-toggle';
        toggle.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: none;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            font-size: 20px;
            cursor: pointer;
            z-index: 1000;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        `;

        toggle.addEventListener('click', () => this.toggleTheme());
        toggle.addEventListener('mouseenter', () => {
            toggle.style.transform = 'scale(1.1) rotate(180deg)';
        });
        toggle.addEventListener('mouseleave', () => {
            toggle.style.transform = 'scale(1) rotate(0deg)';
        });

        document.body.appendChild(toggle);
    }

    showNotification(message) {
        const notification = document.createElement('div');
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 80px;
            right: 20px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            z-index: 1001;
            animation: slideIn 0.5s ease-out;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'slideOut 0.5s ease-in';
            setTimeout(() => notification.remove(), 500);
        }, 2000);
    }
}

// Animasyon CSS'ini ekle
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    
    .fade-in {
        animation: fadeIn 0.8s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
`;
document.head.appendChild(style);

// Tema yöneticisini başlat
const themeManager = new ThemeManager();

// Sayfa yüklendiğinde animasyonları uygula
document.addEventListener('DOMContentLoaded', () => {
    const elements = document.querySelectorAll('h1, h2, h3, p, .card, .button');
    elements.forEach((el, index) => {
        el.style.animationDelay = `${index * 0.1}s`;
        el.classList.add('fade-in');
    });
});