// Merthtmlcss - API Entegrasyonu ve Dinamik İçerik Yöneticisi
class ContentManager {
    constructor() {
        this.apiBaseUrl = 'http://localhost:5000';
        this.cache = new Map();
        this.init();
    }

    init() {
        this.loadDynamicContent();
        this.setupSocialMediaIcons();
        this.createLoadingAnimations();
        console.log("🌐 Merthtmlcss İçerik Yöneticisi başlatıldı");
    }

    async loadDynamicContent() {
        try {
            // XML verilerini yükle
            await this.loadXMLData();
            
            // API'den dinamik içerik yükle
            await this.loadFromAPI();
            
            // Sosyal medya linklerini güncelle
            this.updateSocialLinks();
            
        } catch (error) {
            console.error('İçerik yükleme hatası:', error);
            this.showErrorMessage('İçerik yüklenirken hata oluştu');
        }
    }

    async loadXMLData() {
        const xmlFiles = ['xml1.xml', 'xml2.xml'];
        
        for (const xmlFile of xmlFiles) {
            try {
                const response = await fetch(xmlFile);
                if (response.ok) {
                    const xmlText = await response.text();
                    const parser = new DOMParser();
                    const xmlDoc = parser.parseFromString(xmlText, 'text/xml');
                    this.processXMLData(xmlDoc, xmlFile);
                }
            } catch (error) {
                console.warn(`${xmlFile} yüklenemedi:`, error);
            }
        }
    }

    processXMLData(xmlDoc, fileName) {
        // Başlık güncelleme
        const titles = xmlDoc.querySelectorAll('title');
        titles.forEach((title, index) => {
            const titleElements = document.querySelectorAll('h1, h2, h3');
            if (titleElements[index]) {
                titleElements[index].textContent = title.textContent;
                titleElements[index].style.animation = 'glow 2s ease-in-out infinite alternate';
            }
        });

        // Açıklama güncelleme
        const descriptions = xmlDoc.querySelectorAll('description');
        descriptions.forEach((desc, index) => {
            const descElements = document.querySelectorAll('p, .description');
            if (descElements[index]) {
                descElements[index].textContent = desc.textContent;
            }
        });

        // Geliştirici bilgileri
        const developer = xmlDoc.querySelector('developer');
        if (developer) {
            const name = developer.querySelector('name');
            const role = developer.querySelector('role');
            const experience = developer.querySelector('experience');
            
            if (name) this.updateElement('.developer-name', name.textContent);
            if (role) this.updateElement('.developer-role', role.textContent);
            if (experience) this.updateElement('.developer-exp', experience.textContent);
        }

        // Sosyal medya linkleri
        const socialLinks = xmlDoc.querySelectorAll('social');
        socialLinks.forEach(social => {
            const platform = social.getAttribute('platform');
            const url = social.textContent;
            this.updateSocialLink(platform, url);
        });
    }

    async loadFromAPI() {
        try {
            const response = await fetch(`${this.apiBaseUrl}/api/messages`);
            if (response.ok) {
                const data = await response.json();
                this.displayMessages(data);
            }
        } catch (error) {
            console.warn('API bağlantısı kurulamadı, yerel veriler kullanılıyor');
        }
    }

    displayMessages(messages) {
        const container = document.querySelector('.messages-container') || this.createMessagesContainer();
        
        container.innerHTML = '';
        messages.forEach(message => {
            const messageElement = this.createMessageElement(message);
            container.appendChild(messageElement);
        });
    }

    createMessagesContainer() {
        const container = document.createElement('div');
        container.className = 'messages-container';
        container.style.cssText = `
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            color: white;
        `;
        
        const title = document.createElement('h3');
        title.textContent = '📨 Gelen Mesajlar';
        title.style.textAlign = 'center';
        container.appendChild(title);
        
        document.body.appendChild(container);
        return container;
    }

    createMessageElement(message) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'message-item';
        messageDiv.style.cssText = `
            background: rgba(255,255,255,0.1);
            margin: 10px 0;
            padding: 15px;
            border-radius: 10px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        `;
        
        messageDiv.innerHTML = `
            <h4>${message.name || 'Anonim'}</h4>
            <p><strong>Email:</strong> ${message.email || 'Belirtilmemiş'}</p>
            <p><strong>Mesaj:</strong> ${message.message || 'Mesaj içeriği yok'}</p>
            <small>${message.timestamp || new Date().toLocaleString('tr-TR')}</small>
        `;
        
        return messageDiv;
    }

    setupSocialMediaIcons() {
        const socialIcons = {
            'facebook': '📘',
            'twitter': '🐦',
            'instagram': '📷',
            'linkedin': '💼',
            'github': '🐙',
            'youtube': '📺',
            'discord': '🎮',
            'telegram': '📱'
        };

        // Sosyal medya ikonları için container oluştur
        const socialContainer = document.createElement('div');
        socialContainer.className = 'social-icons';
        socialContainer.style.cssText = `
            position: fixed;
            bottom: 20px;
            left: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            z-index: 1000;
        `;

        Object.entries(socialIcons).forEach(([platform, icon]) => {
            const link = document.createElement('a');
            link.href = '#';
            link.innerHTML = icon;
            link.title = platform.charAt(0).toUpperCase() + platform.slice(1);
            link.style.cssText = `
                display: block;
                width: 50px;
                height: 50px;
                background: linear-gradient(45deg, #667eea, #764ba2);
                color: white;
                text-decoration: none;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 20px;
                transition: all 0.3s ease;
                box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            `;
            
            link.addEventListener('mouseenter', () => {
                link.style.transform = 'scale(1.2) rotate(10deg)';
                link.style.boxShadow = '0 8px 25px rgba(0,0,0,0.4)';
            });
            
            link.addEventListener('mouseleave', () => {
                link.style.transform = 'scale(1) rotate(0deg)';
                link.style.boxShadow = '0 4px 15px rgba(0,0,0,0.2)';
            });
            
            socialContainer.appendChild(link);
        });

        document.body.appendChild(socialContainer);
    }

    updateSocialLink(platform, url) {
        const link = document.querySelector(`.social-icons a[title="${platform}"]`);
        if (link) {
            link.href = url;
            link.addEventListener('click', (e) => {
                e.preventDefault();
                window.open(url, '_blank');
            });
        }
    }

    updateElement(selector, content) {
        const element = document.querySelector(selector);
        if (element) {
            element.textContent = content;
            element.style.animation = 'highlight 1s ease-out';
        }
    }

    createLoadingAnimations() {
        const loadingStyle = document.createElement('style');
        loadingStyle.textContent = `
            @keyframes glow {
                from { text-shadow: 0 0 5px #667eea, 0 0 10px #667eea, 0 0 15px #667eea; }
                to { text-shadow: 0 0 10px #764ba2, 0 0 20px #764ba2, 0 0 30px #764ba2; }
            }
            
            @keyframes highlight {
                0% { background-color: transparent; }
                50% { background-color: rgba(102, 126, 234, 0.3); }
                100% { background-color: transparent; }
            }
            
            @keyframes pulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.05); }
                100% { transform: scale(1); }
            }
            
            .loading {
                animation: pulse 2s infinite;
            }
        `;
        document.head.appendChild(loadingStyle);
    }

    showErrorMessage(message) {
        const errorDiv = document.createElement('div');
        errorDiv.textContent = message;
        errorDiv.style.cssText = `
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #ff4757;
            color: white;
            padding: 20px;
            border-radius: 10px;
            z-index: 1003;
            animation: slideIn 0.5s ease-out;
        `;
        
        document.body.appendChild(errorDiv);
        
        setTimeout(() => {
            errorDiv.remove();
        }, 3000);
    }

    // API'ye mesaj gönderme fonksiyonu
    async sendMessage(messageData) {
        try {
            const response = await fetch(`${this.apiBaseUrl}/api/contact`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(messageData)
            });
            
            if (response.ok) {
                return await response.json();
            } else {
                throw new Error('Mesaj gönderilemedi');
            }
        } catch (error) {
            console.error('Mesaj gönderme hatası:', error);
            throw error;
        }
    }
}

// İçerik yöneticisini başlat
const contentManager = new ContentManager();

// Global fonksiyon olarak dışa aktar
window.bilgiGoster = function() {
    const info = {
        title: 'Merthtmlcss Projesi',
        version: '3.0',
        features: ['Tema Değiştirme', 'Form Validasyonu', 'API Entegrasyonu', 'Dinamik İçerik'],
        developer: 'Mert'
    };
    
    const infoDiv = document.createElement('div');
    infoDiv.innerHTML = `
        <h3>${info.title} v${info.version}</h3>
        <p><strong>Geliştirici:</strong> ${info.developer}</p>
        <p><strong>Özellikler:</strong></p>
        <ul>
            ${info.features.map(feature => `<li>${feature}</li>`).join('')}
        </ul>
    `;
    
    infoDiv.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        padding: 30px;
        border-radius: 15px;
        z-index: 1004;
        max-width: 400px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    `;
    
    document.body.appendChild(infoDiv);
    
    setTimeout(() => {
        infoDiv.remove();
    }, 5000);
}; 