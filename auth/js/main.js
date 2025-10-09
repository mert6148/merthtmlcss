/**
 * AI Destekli Modern Auth JavaScript v2.1.0
 * Gelişmiş tema sistemi ve AI entegrasyonu
 * 
 * @author Mert Doğanay
 * @version 2.1.0
 * @since 2024
 */

// Strict mode kullanımı
'use strict';

// Modern modül sistemi
const AuthAI = (() => {
    
    // Konfigürasyon
    const CONFIG = {
        THEME_KEY: 'auth_ai_theme',
        AI_ENDPOINT: 'https://api.openai.com/v1/chat/completions',
        AI_MODEL: 'gpt-4-turbo',
        AI_MAX_TOKENS: 2048,
        AI_TEMPERATURE: 0.7,
        ANIMATION_DURATION: 300,
        DEBOUNCE_DELAY: 250,
        MAX_RETRIES: 3
    };

    // Utility fonksiyonları
    const utils = {
        // Debounce fonksiyonu
        debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        // Throttle fonksiyonu
        throttle(func, limit) {
            let inThrottle;
            return function() {
                const args = arguments;
                const context = this;
                if (!inThrottle) {
                    func.apply(context, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        },

        // Local storage wrapper
        storage: {
            get(key) {
                try {
                    return JSON.parse(localStorage.getItem(key));
                } catch (e) {
                    console.warn('Storage get error:', e);
                    return null;
                }
            },
            set(key, value) {
                try {
                    localStorage.setItem(key, JSON.stringify(value));
                    return true;
                } catch (e) {
                    console.warn('Storage set error:', e);
                    return false;
                }
            },
            remove(key) {
                try {
                    localStorage.removeItem(key);
                    return true;
                } catch (e) {
                    console.warn('Storage remove error:', e);
                    return false;
                }
            }
        },

        // DOM element seçici
        $(selector) {
            return document.querySelector(selector);
        },

        $$(selector) {
            return document.querySelectorAll(selector);
        },

        // Element oluşturucu
        createElement(tag, attributes = {}, children = []) {
            const element = document.createElement(tag);
            Object.entries(attributes).forEach(([key, value]) => {
                if (key === 'className') {
                    element.className = value;
                } else if (key === 'textContent') {
                    element.textContent = value;
                } else {
                    element.setAttribute(key, value);
                }
            });
            children.forEach(child => element.appendChild(child));
            return element;
        },

        // Animasyon yardımcısı
        animate(element, keyframes, options = {}) {
            return element.animate(keyframes, {
                duration: CONFIG.ANIMATION_DURATION,
                easing: 'cubic-bezier(0.4, 0, 0.2, 1)',
                fill: 'forwards',
                ...options
            });
        },

        // Fetch wrapper
        async fetch(url, options = {}) {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 10000);

            try {
                const response = await fetch(url, {
                    ...options,
                    signal: controller.signal,
                    headers: {
                        'Content-Type': 'application/json',
                        ...options.headers
                    }
                });

                clearTimeout(timeoutId);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                return await response.json();
            } catch (error) {
                clearTimeout(timeoutId);
                throw error;
            }
        },

        // Retry mekanizması
        async retry(fn, retries = CONFIG.MAX_RETRIES) {
            try {
                return await fn();
            } catch (error) {
                if (retries > 0) {
                    await new Promise(resolve => setTimeout(resolve, 1000));
                    return utils.retry(fn, retries - 1);
                }
                throw error;
            }
        }
    };

    // AI Tema Yöneticisi
    const aiThemeManager = {
        themes: {
            light: {
                '--primary': '#667eea',
                '--secondary': '#764ba2',
                '--accent': '#ff6b6b',
                '--success': '#51cf66',
                '--error': '#ff4757',
                '--warning': '#ffd43b',
                '--info': '#17a2b8',
                '--white': '#ffffff',
                '--black': '#000000',
                '--gray-50': '#f8f9fa',
                '--gray-100': '#e9ecef',
                '--gray-200': '#dee2e6',
                '--gray-300': '#ced4da',
                '--gray-400': '#adb5bd',
                '--gray-500': '#6c757d',
                '--gray-600': '#495057',
                '--gray-700': '#343a40',
                '--gray-800': '#212529',
                '--gray-900': '#0d1117'
            },
            dark: {
                '--primary': '#8b9eff',
                '--secondary': '#9b6bc7',
                '--accent': '#ff8e8e',
                '--success': '#69db7c',
                '--error': '#ff6b7a',
                '--warning': '#ffe066',
                '--info': '#39c0ba',
                '--white': '#1a1a1a',
                '--black': '#ffffff',
                '--gray-50': '#0d1117',
                '--gray-100': '#161b22',
                '--gray-200': '#21262d',
                '--gray-300': '#30363d',
                '--gray-400': '#484f58',
                '--gray-500': '#6e7681',
                '--gray-600': '#8b949e',
                '--gray-700': '#a1a1aa',
                '--gray-800': '#c7c7cc',
                '--gray-900': '#f1f1f4'
            },
            auto: {}
        },

        init() {
            this.loadTheme();
            this.bindEvents();
            this.createThemeToggle();
        },

        loadTheme() {
            const savedTheme = utils.storage.get(CONFIG.THEME_KEY) || 'light';
            this.setTheme(savedTheme);
        },

        setTheme(theme) {
    const root = document.documentElement;
            
            // Tema değişkenlerini uygula
            if (this.themes[theme]) {
                Object.entries(this.themes[theme]).forEach(([property, value]) => {
                    root.style.setProperty(property, value);
                });
            }

            // Data attribute'u güncelle
            root.setAttribute('data-theme', theme);
            
            // Tema toggle butonunu güncelle
            this.updateThemeToggle(theme);

            // Local storage'a kaydet
            utils.storage.set(CONFIG.THEME_KEY, theme);
            
            // Tema değişikliği eventi
            window.dispatchEvent(new CustomEvent('themeChanged', { 
                detail: { theme, timestamp: Date.now() } 
            }));

            // AI destekli tema önerisi
            this.suggestTheme();
        },

        updateThemeToggle(theme) {
            const toggle = utils.$('.theme-toggle');
            if (toggle) {
                const icons = {
                    light: '🌙',
                    dark: '☀️',
                    auto: '🤖'
                };
                toggle.textContent = icons[theme] || '🌙';
                toggle.setAttribute('data-theme', theme);
            }
        },

        toggleTheme() {
            const currentTheme = utils.storage.get(CONFIG.THEME_KEY) || 'light';
            const themes = Object.keys(this.themes);
            const currentIndex = themes.indexOf(currentTheme);
            const nextIndex = (currentIndex + 1) % themes.length;
            const newTheme = themes[nextIndex];
            
            this.setTheme(newTheme);
        },

        async suggestTheme() {
            try {
                const hour = new Date().getHours();
                const isNight = hour < 6 || hour > 18;
                const currentTheme = utils.storage.get(CONFIG.THEME_KEY);
                
                if (currentTheme === 'auto') {
                    const suggestedTheme = isNight ? 'dark' : 'light';
                    if (suggestedTheme !== this.getCurrentTheme()) {
                        this.setTheme(suggestedTheme);
                        this.showAIRecommendation(suggestedTheme);
                    }
                }
            } catch (error) {
                console.warn('AI tema önerisi hatası:', error);
            }
        },

        getCurrentTheme() {
            return document.documentElement.getAttribute('data-theme') || 'light';
        },

        showAIRecommendation(theme) {
            const messages = {
                light: '🤖 AI: Gün ışığı için açık tema öneriyorum!',
                dark: '🤖 AI: Gece modu için koyu tema öneriyorum!'
            };
            
            this.showMessage(messages[theme], 'info');
        },

        createThemeToggle() {
            const existingToggle = utils.$('.theme-toggle');
            if (existingToggle) return;

            const toggle = utils.createElement('button', {
                className: 'theme-toggle',
                textContent: '🌙',
                title: 'Tema değiştir (Ctrl+T)'
            });

            document.body.appendChild(toggle);
        },

        bindEvents() {
            // Tema toggle butonu
            document.addEventListener('click', (e) => {
                if (e.target.classList.contains('theme-toggle')) {
                    this.toggleTheme();
                }
            });

            // Klavye kısayolu (Ctrl+T)
            document.addEventListener('keydown', (e) => {
                if (e.ctrlKey && e.key === 't') {
                    e.preventDefault();
                    this.toggleTheme();
                }
            });

            // Sistem tema değişikliği
            if (window.matchMedia) {
                const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
                mediaQuery.addEventListener('change', (e) => {
                    const currentTheme = utils.storage.get(CONFIG.THEME_KEY);
                    if (currentTheme === 'auto') {
                        this.setTheme('auto');
                    }
                });
            }
        },

        showMessage(text, type = 'info') {
            const messageDiv = utils.$('.auth-message');
            if (!messageDiv) return;
            
            messageDiv.textContent = text;
            messageDiv.className = `auth-message ${type}`;
            messageDiv.style.display = 'block';
            
            // Animasyon
            utils.animate(messageDiv, [
                { opacity: 0, transform: 'translateY(20px)' },
                { opacity: 1, transform: 'translateY(0)' }
            ]);
            
            // Otomatik kapatma
            setTimeout(() => {
                utils.animate(messageDiv, [
                    { opacity: 1, transform: 'translateY(0)' },
                    { opacity: 0, transform: 'translateY(-20px)' }
                ]).onfinish = () => {
                    messageDiv.style.display = 'none';
                };
            }, 4000);
        }
    };

    // AI Asistan Yöneticisi
    const aiAssistant = {
        isActive: false,
        isThinking: false,

        init() {
            this.createAssistant();
            this.bindEvents();
        },

        createAssistant() {
            const existingAssistant = utils.$('.ai-assistant');
            if (existingAssistant) return;

            const assistant = utils.createElement('div', {
                className: 'ai-assistant',
                title: 'AI Asistan (Ctrl+A)'
            });

            document.body.appendChild(assistant);
        },

        bindEvents() {
            // AI Asistan tıklama
            document.addEventListener('click', (e) => {
                if (e.target.classList.contains('ai-assistant')) {
                    this.toggleAssistant();
                }
            });

            // Klavye kısayolu (Ctrl+A)
            document.addEventListener('keydown', (e) => {
                if (e.ctrlKey && e.key === 'a') {
                    e.preventDefault();
                    this.toggleAssistant();
                }
            });
        },

        async toggleAssistant() {
            if (this.isThinking) return;

            if (!this.isActive) {
                this.activateAssistant();
            } else {
                this.deactivateAssistant();
            }
        },

        async activateAssistant() {
            this.isActive = true;
            this.setThinking(true);
            
            const assistant = utils.$('.ai-assistant');
            if (assistant) {
                assistant.classList.add('thinking');
            }

            try {
                const response = await this.getAIResponse('Merhaba! Ben AI asistanınız. Size nasıl yardımcı olabilirim?');
                this.showAIResponse(response);
            } catch (error) {
                console.error('AI asistan hatası:', error);
                this.showMessage('AI asistan şu anda kullanılamıyor.', 'error');
            } finally {
                this.setThinking(false);
            }
        },

        deactivateAssistant() {
            this.isActive = false;
            const assistant = utils.$('.ai-assistant');
            if (assistant) {
                assistant.classList.remove('thinking');
            }
        },

        setThinking(thinking) {
            this.isThinking = thinking;
            const assistant = utils.$('.ai-assistant');
            if (assistant) {
                assistant.classList.toggle('thinking', thinking);
            }
        },

        async getAIResponse(prompt) {
            // Bu fonksiyon gerçek AI API'si ile entegre edilebilir
            // Şimdilik simüle edilmiş yanıtlar döndürüyor
            const responses = [
                'Merhaba! Size nasıl yardımcı olabilirim?',
                'Güvenlik ayarlarınızı kontrol etmenizi öneririm.',
                'Tema değiştirmek için Ctrl+T tuşlarını kullanabilirsiniz.',
                'Form validasyonu otomatik olarak çalışmaktadır.',
                'Herhangi bir sorunuz varsa yardımcı olmaktan mutluluk duyarım.'
            ];
            
            await new Promise(resolve => setTimeout(resolve, 1000 + Math.random() * 2000));
            return responses[Math.floor(Math.random() * responses.length)];
        },

        showAIResponse(response) {
            this.showMessage(`🤖 AI: ${response}`, 'info');
        },

        showMessage(text, type = 'info') {
            const messageDiv = utils.$('.auth-message');
            if (!messageDiv) return;
            
            messageDiv.textContent = text;
            messageDiv.className = `auth-message ${type}`;
            messageDiv.style.display = 'block';
            
            // Animasyon
            utils.animate(messageDiv, [
                { opacity: 0, transform: 'translateY(20px)' },
                { opacity: 1, transform: 'translateY(0)' }
            ]);
            
            // Otomatik kapatma
            setTimeout(() => {
                utils.animate(messageDiv, [
                    { opacity: 1, transform: 'translateY(0)' },
                    { opacity: 0, transform: 'translateY(-20px)' }
                ]).onfinish = () => {
                    messageDiv.style.display = 'none';
                };
            }, 5000);
        }
    };

    // Performans İzleyici
    const performanceMonitor = {
        init() {
            this.measurePageLoad();
            this.observeIntersections();
        },

        measurePageLoad() {
            window.addEventListener('load', () => {
                const loadTime = performance.now();
                console.log(`Auth sayfası yükleme süresi: ${loadTime.toFixed(2)}ms`);
                
                // Analytics'e gönder
                this.sendAnalytics('auth_page_load', { loadTime });
            });
        },

        observeIntersections() {
            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.style.opacity = '1';
                            entry.target.style.transform = 'translateY(0)';
                        }
                    });
                }, {
                    threshold: 0.1,
                    rootMargin: '0px 0px -50px 0px'
                });

                const elements = utils.$$('.auth-container, .theme-toggle, .ai-assistant');
                elements.forEach(el => {
                    el.style.opacity = '0';
                    el.style.transform = 'translateY(20px)';
                    el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                    observer.observe(el);
                });
            }
        },

        sendAnalytics(event, data) {
            // Analytics verilerini gönder
            console.log('Auth Analytics:', event, data);
        }
    };

    // Ana uygulama sınıfı
    class AuthApp {
        constructor() {
            this.modules = [
                aiThemeManager,
                aiAssistant,
                performanceMonitor
            ];
        }

        init() {
            console.log('🤖 AI Auth uygulaması başlatılıyor...');
            
            // DOM hazır olduğunda başlat
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => this.start());
            } else {
                this.start();
            }
        }

        start() {
            try {
                // Modülleri başlat
                this.modules.forEach(module => {
                    if (module.init && typeof module.init === 'function') {
                        module.init();
                    }
                });

                // Sayfa animasyonu
                this.animatePageLoad();
                
                console.log('✅ AI Auth uygulaması başarıyla başlatıldı!');
                
            } catch (error) {
                console.error('❌ AI Auth başlatma hatası:', error);
            }
        }

        animatePageLoad() {
            document.body.style.opacity = '0';
            document.body.style.transition = 'opacity 0.5s ease';
            
            setTimeout(() => {
                document.body.style.opacity = '1';
            }, 100);
        }
    }

    // Global fonksiyonlar (geriye uyumluluk için)
    window.setTheme = (dark) => aiThemeManager.setTheme(dark ? 'dark' : 'light');
    window.toggleTheme = () => aiThemeManager.toggleTheme();
    window.initTheme = () => aiThemeManager.init();

    // Uygulamayı başlat
    const app = new AuthApp();
    app.init();

    // Public API
    return {
        app,
        utils,
        aiThemeManager,
        aiAssistant,
        performanceMonitor,
        CONFIG
    };

})();

// Service Worker kaydı (PWA desteği için)
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/auth-sw.js')
            .then(registration => {
                console.log('Auth SW registered: ', registration);
            })
            .catch(registrationError => {
                console.log('Auth SW registration failed: ', registrationError);
            });
    });
}

// Global hata yakalayıcı
window.addEventListener('error', (event) => {
    console.error('Auth Global error:', event.error);
});

// Unhandled promise rejection yakalayıcı
window.addEventListener('unhandledrejection', (event) => {
    console.error('Auth Unhandled promise rejection:', event.reason);
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AuthAI;
}