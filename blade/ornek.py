#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Merthtmlcss Python CSS Yöneticisi
CSS kodları oluşturan ve yöneten Python uygulaması
"""

import os
import json
import datetime
from typing import Dict, List, Optional

class CSSManager:
    """CSS kodları oluşturan ve yöneten sınıf"""
    
    def __init__(self):
        self.css_variables = {
            'primary-color': '#667eea',
            'secondary-color': '#764ba2',
            'success-color': '#2ed573',
            'error-color': '#ff4757',
            'warning-color': '#ffa502',
            'info-color': '#3742fa',
            'light-color': '#ffffff',
            'dark-color': '#1a1a1a',
            'gray-color': '#747d8c',
            'border-radius': '10px',
            'box-shadow': '0 4px 15px rgba(0,0,0,0.1)',
            'transition': 'all 0.3s ease'
        }
        
        self.css_styles = {}
        self.output_file = 'generated_styles.css'
        
    def add_css_variable(self, name: str, value: str) -> None:
        """CSS değişkeni ekle"""
        self.css_variables[name] = value
        print(f"✅ CSS değişkeni eklendi: {name} = {value}")
    
    def generate_css_variables(self) -> str:
        """CSS değişkenlerini oluştur"""
        css = ":root {\n"
        for name, value in self.css_variables.items():
            css += f"    --{name}: {value};\n"
        css += "}\n\n"
        return css
    
    def create_button_styles(self) -> str:
        """Modern buton stilleri oluştur"""
        css = """
/* Modern Buton Stilleri */
.button {
    display: inline-block;
    padding: 12px 24px;
    background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
    color: var(--light-color);
    text-decoration: none;
    border: none;
    border-radius: var(--border-radius);
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    box-shadow: var(--box-shadow);
    position: relative;
    overflow: hidden;
}

.button:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
}

.button:active {
    transform: translateY(0);
}

.button::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.button:hover::before {
    left: 100%;
}

/* Buton varyasyonları */
.button-primary {
    background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
}

.button-success {
    background: linear-gradient(45deg, var(--success-color), #26de81);
}

.button-error {
    background: linear-gradient(45deg, var(--error-color), #ff3838);
}

.button-warning {
    background: linear-gradient(45deg, var(--warning-color), #ff9ff3);
}

.button-info {
    background: linear-gradient(45deg, var(--info-color), #54a0ff);
}

/* Buton boyutları */
.button-small {
    padding: 8px 16px;
    font-size: 14px;
}

.button-large {
    padding: 16px 32px;
    font-size: 18px;
}

"""
        return css
    
    def create_card_styles(self) -> str:
        """Modern kart stilleri oluştur"""
        css = """
/* Modern Kart Stilleri */
.card {
    background: var(--light-color);
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    padding: 20px;
    margin: 15px 0;
    transition: var(--transition);
    border: 1px solid rgba(0,0,0,0.1);
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.card-header {
    border-bottom: 2px solid var(--primary-color);
    padding-bottom: 10px;
    margin-bottom: 15px;
}

.card-title {
    color: var(--primary-color);
    font-size: 1.5em;
    font-weight: 700;
    margin: 0;
}

.card-subtitle {
    color: var(--gray-color);
    font-size: 1em;
    margin: 5px 0 0 0;
}

.card-body {
    line-height: 1.6;
}

.card-footer {
    border-top: 1px solid #eee;
    padding-top: 15px;
    margin-top: 15px;
    text-align: right;
}

/* Kart varyasyonları */
.card-primary {
    border-left: 4px solid var(--primary-color);
}

.card-success {
    border-left: 4px solid var(--success-color);
}

.card-error {
    border-left: 4px solid var(--error-color);
}

.card-warning {
    border-left: 4px solid var(--warning-color);
}

"""
        return css
    
    def create_form_styles(self) -> str:
        """Modern form stilleri oluştur"""
        css = """
/* Modern Form Stilleri */
.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--dark-color);
}

.form-input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e1e8ed;
    border-radius: var(--border-radius);
    font-size: 16px;
    transition: var(--transition);
    background: var(--light-color);
}

.form-input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-input.error {
    border-color: var(--error-color);
    box-shadow: 0 0 0 3px rgba(255, 71, 87, 0.1);
}

.form-input.success {
    border-color: var(--success-color);
    box-shadow: 0 0 0 3px rgba(46, 213, 115, 0.1);
}

.form-textarea {
    min-height: 120px;
    resize: vertical;
}

.form-select {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 12px center;
    background-repeat: no-repeat;
    background-size: 16px;
    padding-right: 40px;
}

.form-error {
    color: var(--error-color);
    font-size: 14px;
    margin-top: 5px;
    display: block;
}

.form-success {
    color: var(--success-color);
    font-size: 14px;
    margin-top: 5px;
    display: block;
}

"""
        return css
    
    def create_animation_styles(self) -> str:
        """Animasyon stilleri oluştur"""
        css = """
/* Animasyon Stilleri */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideIn {
    from {
        transform: translateX(-100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOut {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
    100% {
        transform: scale(1);
    }
}

@keyframes glow {
    from {
        text-shadow: 0 0 5px var(--primary-color), 0 0 10px var(--primary-color), 0 0 15px var(--primary-color);
    }
    to {
        text-shadow: 0 0 10px var(--secondary-color), 0 0 20px var(--secondary-color), 0 0 30px var(--secondary-color);
    }
}

@keyframes rotate {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

/* Animasyon sınıfları */
.fade-in {
    animation: fadeIn 0.8s ease-out;
}

.slide-in {
    animation: slideIn 0.5s ease-out;
}

.slide-out {
    animation: slideOut 0.5s ease-in;
}

.pulse {
    animation: pulse 2s infinite;
}

.glow {
    animation: glow 2s ease-in-out infinite alternate;
}

.rotate {
    animation: rotate 2s linear infinite;
}

/* Animasyon gecikmeleri */
.delay-1 { animation-delay: 0.1s; }
.delay-2 { animation-delay: 0.2s; }
.delay-3 { animation-delay: 0.3s; }
.delay-4 { animation-delay: 0.4s; }
.delay-5 { animation-delay: 0.5s; }

"""
        return css
    
    def create_responsive_styles(self) -> str:
        """Responsive tasarım stilleri oluştur"""
        css = """
/* Responsive Tasarım Stilleri */
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.row {
    display: flex;
    flex-wrap: wrap;
    margin: 0 -10px;
}

.col {
    flex: 1;
    padding: 0 10px;
}

/* Grid sistemi */
.grid {
    display: grid;
    gap: 20px;
}

.grid-2 {
    grid-template-columns: repeat(2, 1fr);
}

.grid-3 {
    grid-template-columns: repeat(3, 1fr);
}

.grid-4 {
    grid-template-columns: repeat(4, 1fr);
}

/* Responsive breakpoint'ler */
@media (max-width: 768px) {
    .container {
        padding: 0 15px;
    }
    
    .grid-2,
    .grid-3,
    .grid-4 {
        grid-template-columns: 1fr;
    }
    
    .row {
        flex-direction: column;
    }
    
    .col {
        margin-bottom: 20px;
    }
    
    .button {
        width: 100%;
        text-align: center;
    }
}

@media (max-width: 480px) {
    .container {
        padding: 0 10px;
    }
    
    .card {
        padding: 15px;
    }
    
    .form-input {
        padding: 10px 12px;
    }
}

/* Utility sınıfları */
.text-center { text-align: center; }
.text-left { text-align: left; }
.text-right { text-align: right; }

.mt-1 { margin-top: 10px; }
.mt-2 { margin-top: 20px; }
.mt-3 { margin-top: 30px; }

.mb-1 { margin-bottom: 10px; }
.mb-2 { margin-bottom: 20px; }
.mb-3 { margin-bottom: 30px; }

.p-1 { padding: 10px; }
.p-2 { padding: 20px; }
.p-3 { padding: 30px; }

.d-none { display: none; }
.d-block { display: block; }
.d-flex { display: flex; }
.d-grid { display: grid; }

"""
        return css
    
    def generate_complete_css(self) -> str:
        """Tüm CSS stillerini oluştur"""
        css = f"""/*
Merthtmlcss CSS Framework
Oluşturulma Tarihi: {datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S')}
Geliştirici: Mert Doğanay
Versiyon: 3.0
*/

{self.generate_css_variables()}
{self.create_button_styles()}
{self.create_card_styles()}
{self.create_form_styles()}
{self.create_animation_styles()}
{self.create_responsive_styles()}
"""
        return css
    
    def save_css_file(self, filename: Optional[str] = None) -> str:
        """CSS dosyasını kaydet"""
        if filename:
            self.output_file = filename
        
        css_content = self.generate_complete_css()
        
        with open(self.output_file, 'w', encoding='utf-8') as f:
            f.write(css_content)
        
        print(f"✅ CSS dosyası başarıyla oluşturuldu: {self.output_file}")
        return self.output_file
    
    def create_custom_style(self, selector: str, properties: Dict[str, str]) -> str:
        """Özel CSS stili oluştur"""
        css = f"{selector} {{\n"
        for property_name, value in properties.items():
            css += f"    {property_name}: {value};\n"
        css += "}\n\n"
        return css
    
    def add_custom_style(self, selector: str, properties: Dict[str, str]) -> None:
        """Özel stil ekle"""
        self.css_styles[selector] = properties
        print(f"✅ Özel stil eklendi: {selector}")

def main():
    """Ana fonksiyon"""
    print("🎨 Merthtmlcss CSS Yöneticisi Başlatılıyor...")
    
    # CSS yöneticisini oluştur
    css_manager = CSSManager()
    
    # Özel CSS değişkenleri ekle
    css_manager.add_css_variable('gradient-primary', 'linear-gradient(45deg, #667eea, #764ba2)')
    css_manager.add_css_variable('gradient-success', 'linear-gradient(45deg, #2ed573, #26de81)')
    css_manager.add_css_variable('gradient-error', 'linear-gradient(45deg, #ff4757, #ff3838)')
    
    # Özel stiller ekle
    css_manager.add_custom_style('.hero-section', {
        'background': 'var(--gradient-primary)',
        'color': 'var(--light-color)',
        'padding': '80px 0',
        'text-align': 'center'
    })
    
    css_manager.add_custom_style('.feature-card', {
        'background': 'var(--light-color)',
        'border-radius': 'var(--border-radius)',
        'padding': '30px',
        'text-align': 'center',
        'box-shadow': 'var(--box-shadow)',
        'transition': 'var(--transition)'
    })
    
    # CSS dosyasını oluştur
    output_file = css_manager.save_css_file('merthtmlcss-styles.css')
    
    print(f"\n🎉 CSS dosyası başarıyla oluşturuldu!")
    print(f"📁 Dosya konumu: {os.path.abspath(output_file)}")
    print(f"📊 Toplam CSS değişkeni: {len(css_manager.css_variables)}")
    print(f"🎨 Özel stil sayısı: {len(css_manager.css_styles)}")
    
    # HTML örneği oluştur
    html_example = """
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merthtmlcss CSS Örneği</title>
    <link rel="stylesheet" href="merthtmlcss-styles.css">
</head>
<body>
    <div class="container">
        <div class="hero-section">
            <h1 class="glow">Merthtmlcss CSS Framework</h1>
            <p>Modern ve responsive CSS framework'ü</p>
            <button class="button button-primary" id="startBtn">Başla</button>
        </div>
        
        <div class="grid grid-3">
            <div class="feature-card fade-in delay-1">
                <h3>Modern Tasarım</h3>
                <p>Güncel tasarım trendlerine uygun stiller</p>
                <button class="button button-small" onclick="showFeature('Modern Tasarım')">Detay</button>
            </div>
            <div class="feature-card fade-in delay-2">
                <h3>Responsive</h3>
                <p>Tüm cihazlarda mükemmel görünüm</p>
                <button class="button button-small" onclick="showFeature('Responsive')">Detay</button>
            </div>
            <div class="feature-card fade-in delay-3">
                <h3>Animasyonlar</h3>
                <p>Etkileyici CSS animasyonları</p>
                <button class="button button-small" onclick="showFeature('Animasyonlar')">Detay</button>
            </div>
        </div>
        
        <div id="notification" class="notification" style="display: none;"></div>
    </div>
    
    <script>
    // Merthtmlcss JavaScript Framework
    class MerthtmlcssApp {
        constructor() {
            this.notification = document.getElementById('notification');
            this.startBtn = document.getElementById('startBtn');
            this.init();
        }
        
        init() {
            this.setupEventListeners();
            this.loadFeatures();
            console.log("🎨 Merthtmlcss App başlatıldı");
        }
        
        setupEventListeners() {
            this.startBtn.addEventListener('click', () => this.handleStart());
            
            // Kart hover efektleri
            document.querySelectorAll('.feature-card').forEach(card => {
                card.addEventListener('mouseenter', () => this.cardHover(card));
                card.addEventListener('mouseleave', () => this.cardLeave(card));
            });
        }
        
        handleStart() {
            this.startBtn.textContent = 'Başlatıldı! 🚀';
            this.startBtn.classList.add('button-success');
            this.showNotification('Framework başarıyla başlatıldı!', 'success');
            
            setTimeout(() => {
                this.startBtn.textContent = 'Başla';
                this.startBtn.classList.remove('button-success');
            }, 2000);
        }
        
        cardHover(card) {
            card.style.transform = 'scale(1.02)';
            card.style.boxShadow = '0 15px 35px rgba(0,0,0,0.2)';
        }
        
        cardLeave(card) {
            card.style.transform = 'scale(1)';
            card.style.boxShadow = '';
        }
        
        showNotification(message, type = 'info') {
            this.notification.textContent = message;
            this.notification.className = `notification ${type}`;
            this.notification.style.display = 'block';
            
            setTimeout(() => {
                this.notification.style.display = 'none';
            }, 3000);
        }
        
        loadFeatures() {
            const features = [
                { name: 'Modern Tasarım', description: 'CSS Grid, Flexbox ve modern layout teknikleri' },
                { name: 'Responsive', description: 'Mobil-first yaklaşım ve breakpoint sistemi' },
                { name: 'Animasyonlar', description: 'CSS keyframes ve transition efektleri' }
            ];
            
            console.log('Özellikler yüklendi:', features);
        }
    }
    
    // Global fonksiyonlar
    function showFeature(featureName) {
        const messages = {
            'Modern Tasarım': 'CSS Grid ve Flexbox ile modern layout oluşturma',
            'Responsive': 'Tüm cihazlarda mükemmel görünüm sağlama',
            'Animasyonlar': 'CSS animasyonları ile etkileyici efektler'
        };
        
        const app = window.merthtmlcssApp;
        if (app) {
            app.showNotification(messages[featureName] || 'Özellik detayı', 'info');
        }
    }
    
    function toggleTheme() {
        document.body.classList.toggle('dark-theme');
        const isDark = document.body.classList.contains('dark-theme');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
        
        const app = window.merthtmlcssApp;
        if (app) {
            app.showNotification(`Tema ${isDark ? 'karanlık' : 'aydınlık'} moda geçirildi`, 'success');
        }
    }
    
    // Sayfa yüklendiğinde app'i başlat
    document.addEventListener('DOMContentLoaded', () => {
        window.merthtmlcssApp = new MerthtmlcssApp();
        
        // Kaydedilmiş temayı yükle
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            document.body.classList.add('dark-theme');
        }
    });
    </script>
</body>
</html>
"""
    
    # HTML örneğini kaydet
    with open('css-example.html', 'w', encoding='utf-8') as f:
        f.write(html_example)
    
    print(f"📄 HTML örneği oluşturuldu: css-example.html")
    print("\n🚀 Kullanım için:")
    print("1. merthtmlcss-styles.css dosyasını HTML'inize dahil edin")
    print("2. css-example.html dosyasını tarayıcıda açın")
    print("3. CSS sınıflarını kullanmaya başlayın!")

if __name__ == "__main__":
    main()
    