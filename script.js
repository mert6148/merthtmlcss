/**
 * Merthtmlcss Ana JavaScript Dosyası
 * Modern ES6+ özellikleri ile güçlendirilmiş interaktif web uygulaması
 * 
 * @author Mert Doğanay
 * @version 2.1.0
 * @since 2024
 */

// Strict mode kullanımı
'use strict';

// Modern modül sistemi
const Merthtmlcss = (() => {

    // Konfigürasyon
    const CONFIG = {
        API_BASE_URL: 'https://api.merthtmlcss.com',
        THEME_KEY: 'merthtmlcss_theme',
        ANIMATION_DURATION: 300,
        DEBOUNCE_DELAY: 250,
        MAX_RETRIES: 3,
        CACHE_DURATION: 3600000 // 1 saat
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
            return function () {
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

    // Tema yöneticisi
    const themeManager = {
        init() {
            this.loadTheme();
            this.bindEvents();
        },

        loadTheme() {
            const savedTheme = utils.storage.get(CONFIG.THEME_KEY) || 'light';
            this.setTheme(savedTheme);
        },

        setTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            document.body.classList.toggle('dark', theme === 'dark');

            const iconMoon = utils.$('.icon-moon');
            const iconSun = utils.$('.icon-sun');

            if (iconMoon && iconSun) {
                iconMoon.style.display = theme === 'dark' ? 'none' : 'block';
                iconSun.style.display = theme === 'dark' ? 'block' : 'none';
            }

            utils.storage.set(CONFIG.THEME_KEY, theme);

            // Tema değişikliği eventi
            window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme } }));
        },

        toggleTheme() {
            const currentTheme = utils.storage.get(CONFIG.THEME_KEY) || 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            this.setTheme(newTheme);
        },

        bindEvents() {
            const themeToggle = utils.$('.theme-toggle');
            if (themeToggle) {
                themeToggle.addEventListener('click', () => this.toggleTheme());
            }

            // Klavye kısayolu (Ctrl+T)
            document.addEventListener('keydown', (e) => {
                if (e.ctrlKey && e.key === 't') {
                    e.preventDefault();
                    this.toggleTheme();
                }
            });
        }
    };

    // API yöneticisi
    const apiManager = {
        async request(endpoint, options = {}) {
            const url = `${CONFIG.API_BASE_URL}${endpoint}`;

            return utils.retry(async () => {
                return await utils.fetch(url, options);
            });
        },

        async get(endpoint) {
            return this.request(endpoint, { method: 'GET' });
        },

        async post(endpoint, data) {
            return this.request(endpoint, {
                method: 'POST',
                body: JSON.stringify(data)
            });
        },

        async put(endpoint, data) {
            return this.request(endpoint, {
                method: 'PUT',
                body: JSON.stringify(data)
            });
        },

        async delete(endpoint) {
            return this.request(endpoint, { method: 'DELETE' });
        }
    };

    // Form yöneticisi
    const formManager = {
        init() {
            this.bindFormEvents();
        },

        bindFormEvents() {
            const forms = utils.$$('form');
            forms.forEach(form => {
                form.addEventListener('submit', (e) => this.handleSubmit(e));
                this.addFormValidation(form);
            });
        },

        addFormValidation(form) {
            const inputs = form.querySelectorAll('input[required], textarea[required]');
            inputs.forEach(input => {
                input.addEventListener('blur', () => this.validateField(input));
                input.addEventListener('input', utils.debounce(() => this.validateField(input), CONFIG.DEBOUNCE_DELAY));
            });
        },

        validateField(field) {
            const value = field.value.trim();
            const isValid = this.isFieldValid(field, value);

            field.classList.toggle('input-error', !isValid);
            field.classList.toggle('input-success', isValid && value.length > 0);

            return isValid;
        },

        isFieldValid(field, value) {
            const type = field.type;
            const required = field.hasAttribute('required');

            if (required && !value) return false;

            switch (type) {
                case 'email':
                    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
                case 'tel':
                    return /^[\+]?[0-9\s\-\(\)]{10,}$/.test(value);
                case 'url':
                    return /^https?:\/\/.+/.test(value);
                default:
                    return true;
            }
        },

        async handleSubmit(e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);
            const submitButton = form.querySelector('button[type="submit"]');
            const originalText = submitButton?.textContent;

            // Form validasyonu
            const inputs = form.querySelectorAll('input[required], textarea[required]');
            let isValid = true;

            inputs.forEach(input => {
                if (!this.validateField(input)) {
                    isValid = false;
                }
            });

            if (!isValid) {
                this.showMessage('Lütfen tüm gerekli alanları doldurun.', 'error');
                return;
            }

            // Submit butonunu devre dışı bırak
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="loading"></span> Gönderiliyor...';
            }

            try {
                // Form verilerini gönder
                const response = await apiManager.post('/api/contact', Object.fromEntries(formData));

                if (response.success) {
                    this.showMessage('Form başarıyla gönderildi!', 'success');
                    form.reset();
                    inputs.forEach(input => input.classList.remove('input-success'));
                } else {
                    this.showMessage(response.message || 'Bir hata oluştu.', 'error');
                }
            } catch (error) {
                console.error('Form submit error:', error);
                this.showMessage('Bağlantı hatası. Lütfen tekrar deneyin.', 'error');
            } finally {
                // Submit butonunu geri etkinleştir
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.textContent = originalText;
                }
            }
        },

        showMessage(text, type) {
            const messageDiv = utils.$('#js-mesaj');
            if (!messageDiv) return;

            messageDiv.textContent = text;
            messageDiv.className = `message-${type}`;
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

    // Sosyal medya yöneticisi
    const socialManager = {
        platforms: {
            github: { url: 'https://github.com/mert6148', message: 'GitHub profilinizi görüntülemek için tıklayın' },
            twitter: { url: 'https://x.com/MertDoganay61', message: 'Twitter hesabınızı görüntülemek için tıklayın' },
            linkedin: { url: '#', message: 'LinkedIn profilinizi görüntülemek için tıklayın' },
            instagram: { url: '#', message: 'Instagram hesabınızı görüntülemek için tıklayın' },
            youtube: { url: 'https://www.youtube.com/@mert_doganay', message: 'YouTube kanalınızı görüntülemek için tıklayın' }
        },

        init() {
            this.bindEvents();
        },

        bindEvents() {
            const socialLinks = utils.$$('.social-links a');
            socialLinks.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const platform = this.getPlatformFromElement(link);
                    this.handleSocialClick(platform);
                });
            });
        },

        getPlatformFromElement(element) {
            const icon = element.querySelector('i');
            if (!icon) return null;

            const classList = icon.className;
            if (classList.includes('fa-github')) return 'github';
            if (classList.includes('fa-twitter')) return 'twitter';
            if (classList.includes('fa-linkedin')) return 'linkedin';
            if (classList.includes('fa-instagram')) return 'instagram';
            if (classList.includes('fa-youtube')) return 'youtube';

            return null;
        },

        handleSocialClick(platform) {
            if (!platform || !this.platforms[platform]) return;

            const platformInfo = this.platforms[platform];

            // Mesaj göster
            formManager.showMessage(platformInfo.message, 'success');

            // URL varsa yeni sekmede aç
            if (platformInfo.url && platformInfo.url !== '#') {
                setTimeout(() => {
                    window.open(platformInfo.url, '_blank', 'noopener,noreferrer');
                }, 1000);
            }
        }
    };

    // İletişim yöneticisi
    const contactManager = {
        init() {
            this.bindEvents();
        },

        bindEvents() {
            const contactButton = utils.$('#js-buton');
            if (contactButton) {
                contactButton.addEventListener('click', (e) => this.handleContact(e));
            }
        },

        async handleContact(e) {
            e.preventDefault();

            const button = e.target;
            const originalText = button.innerHTML;

            button.innerHTML = '<span class="loading"></span> Gönderiliyor...';
            button.disabled = true;

            try {
                // E-posta uygulamasını aç
                const email = 'info@merthtmlcss.com';
                const subject = 'İletişim';
                const body = 'Merhaba, Merthtmlcss ile ilgili bir sorum var.';

                const mailtoUrl = `mailto:${email}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;

                // E-posta uygulamasını açmaya çalış
                window.location.href = mailtoUrl;

                // Başarı mesajı göster
                setTimeout(() => {
                    formManager.showMessage('E-posta uygulamanız açılmadıysa, lütfen info@merthtmlcss.com adresine manuel olarak e-posta gönderebilirsiniz.', 'info');
                }, 1000);

            } catch (error) {
                console.error('Contact error:', error);
                formManager.showMessage('Bir hata oluştu. Lütfen tekrar deneyin.', 'error');
            } finally {
                button.innerHTML = originalText;
                button.disabled = false;
            }
        }
    };

    // XML veri yöneticisi
    const xmlManager = {
        async loadXMLData() {
            try {
                const response = await utils.fetch('blade/hakkinda.xml');
                const xmlText = await response.text();
                const xmlDoc = new DOMParser().parseFromString(xmlText, 'text/xml');

                this.updatePageContent(xmlDoc);
            } catch (error) {
                console.warn('XML yüklenemedi:', error);
                this.showXMLError();
            }
        },

        updatePageContent(xmlDoc) {
            const mappings = {
                'xml-baslik': 'baslik',
                'xml-aciklama': 'aciklama',
                'xml-gelistirici': 'gelistirici',
                'xml-iletisim': 'iletisim'
            };

            Object.entries(mappings).forEach(([elementId, xmlTag]) => {
                const element = utils.$(`#${elementId}`);
                const xmlElement = xmlDoc.querySelector(xmlTag);

                if (element && xmlElement) {
                    if (elementId === 'xml-iletisim') {
                        element.innerHTML = `<a href="mailto:${xmlElement.textContent}">${xmlElement.textContent}</a>`;
                    } else {
                        element.textContent = xmlElement.textContent;
                    }
                }
            });

            // Sosyal medya linklerini güncelle
            this.updateSocialLinks(xmlDoc);
        },

        updateSocialLinks(xmlDoc) {
            const sosyal = xmlDoc.querySelector('sosyal');
            if (!sosyal) return;

            const socialContainer = utils.$('#xml-sosyal');
            if (!socialContainer) return;

            let socialHTML = '';
            const socialPlatforms = ['twitter', 'github', 'youtube', 'linkedin', 'instagram'];

            socialPlatforms.forEach(platform => {
                const platformElement = sosyal.querySelector(platform);
                if (platformElement) {
                    const iconClass = this.getSocialIconClass(platform);
                    socialHTML += `
                        <li>
                            <a href="${platformElement.textContent}" target="_blank" rel="noopener noreferrer">
                                <i class="${iconClass}"></i> ${platform.charAt(0).toUpperCase() + platform.slice(1)}
                            </a>
                        </li>
                    `;
                }
            });

            socialContainer.innerHTML = socialHTML;
        },

        getSocialIconClass(platform) {
            const iconMap = {
                twitter: 'fab fa-twitter',
                github: 'fab fa-github',
                youtube: 'fab fa-youtube',
                linkedin: 'fab fa-linkedin',
                instagram: 'fab fa-instagram'
            };
            return iconMap[platform] || 'fas fa-link';
        },

        showXMLError() {
            const xmlBilgi = utils.$('#xml-bilgi');
            if (xmlBilgi) {
                xmlBilgi.innerHTML = `
                    <div class="message-error" style="text-align:center;">
                        <strong>Hakkında bilgisi yüklenemedi.</strong><br>
                        <span style="font-size:0.98em;">Lütfen bağlantınızı ve <code>blade/hakkinda.xml</code> dosyasının varlığını kontrol edin.</span>
                    </div>
                `;
            }
        }
    };

    // Performans izleyici
    const performanceMonitor = {
        init() {
            this.measurePageLoad();
            this.observeIntersections();
        },

        measurePageLoad() {
            window.addEventListener('load', () => {
                const loadTime = performance.now();
                console.log(`Sayfa yükleme süresi: ${loadTime.toFixed(2)}ms`);

                // Analytics'e gönder
                this.sendAnalytics('page_load', { loadTime });
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

                const style = field.style || field.id || 'field';
                const elements = utils.$$('.bilgi-kutusu, .btn, .social-links');
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
            console.log('Analytics:', event, data);
        }
    };

    // Ana uygulama sınıfı
    class App {
        constructor() {
            this.modules = [
                themeManager,
                formManager,
                socialManager,
                contactManager,
                xmlManager,
                performanceMonitor
            ];
        }

        init() {
            console.log('🚀 Merthtmlcss uygulaması başlatılıyor...');

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

                // XML verilerini yükle
                xmlManager.loadXMLData();

                console.log('✅ Merthtmlcss uygulaması başarıyla başlatıldı!');

            } catch (error) {
                console.error('❌ Uygulama başlatma hatası:', error);
            }
        }

        footerManager() {
            const footer = utils.$('#footer');
            if (footer) {
                footer.addEventListener('click', (e) => this.handleFooterClick(e));
                document.addEvenListener('click', (e) => this.handleFooterLinkClick(e));
            }
        }

        handleFooterClick(e) {
            e.preventDefault();
            const footer = e.target;
            const footerLinks = footer.querySelectorAll('.footer-links a');
            const footerLinkHref = footerLinks.href;
            this.handleFooterLinkHref(footerLinkHref);
        }

        handleFooterLinkClick(e) {
            e.preventDefault();
            const footerLink = e.target;
            const footerLinkHref = footerLink.href;
        }

        handleFooterLinkHref(href) {
            window.location.href = href;
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
    window.toggleTheme = () => themeManager.toggleTheme();
    window.contactUs = () => contactManager.handleContact({ preventDefault: () => { } });
    window.showSocial = (platform) => socialManager.handleSocialClick(platform);
    window.showMessage = (text, type) => formManager.showMessage(text, type);

    // Uygulamayı başlat
    const app = new App();
    app.init();

    // Public API
    return {
        app,
        utils,
        themeManager,
        formManager,
        socialManager,
        contactManager,
        xmlManager,
        apiManager,
        CONFIG
    };

})();

// Service Worker kaydı (PWA desteği için)
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(registration => {
                console.log('SW registered: ', registration);
            })
            .catch(registrationError => {
                console.log('SW registration failed: ', registrationError);
            });
    });
}

// Global hata yakalayıcı
window.addEventListener('error', (event) => {
    console.error('Global error:', event.error);
});

// Unhandled promise rejection yakalayıcı
window.addEventListener('unhandledrejection', (event) => {
    console.error('Unhandled promise rejection:', event.reason);
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = Merthtmlcss;
}

// Toast Bildirim Sistemi
function showToast(message, type = 'info', duration = 3500) {
    let toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.classList.add('show'), 10);
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, duration);
}

// Sistem Temasını Algıla ve Uygula
function autoDetectTheme() {
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const savedTheme = localStorage.getItem('merthtmlcss_theme');
    if (!savedTheme) {
        document.documentElement.setAttribute('data-theme', prefersDark ? 'dark' : 'light');
        document.body.classList.toggle('dark', prefersDark);
    }
}
window.addEventListener('DOMContentLoaded', autoDetectTheme);

// Erişilebilirlik: Tüm buton ve linklere klavye ile erişim için focus-visible
function enableFocusVisible() {
    document.body.addEventListener('keydown', e => {
        if (e.key === 'Tab') {
            document.body.classList.add('user-is-tabbing');
        }
    });
    document.body.addEventListener('mousedown', () => {
        document.body.classList.remove('user-is-tabbing');
    });
}
window.addEventListener('DOMContentLoaded', enableFocusVisible);

// Micro-interaction: Butonlara tıklama animasyonu
function enableButtonBounce() {
    document.body.addEventListener('click', e => {
        if (e.target.classList.contains('btn')) {
            e.target.classList.remove('bounce');
            void e.target.offsetWidth; // reflow
            e.target.classList.add('bounce');
        }
    });
}
window.addEventListener('DOMContentLoaded', enableButtonBounce);

// Form Validasyonu: Gerçek zamanlı, erişilebilir, inline hata mesajı
function enableFormValidation() {
    document.querySelectorAll('form').forEach(form => {
        form.setAttribute('novalidate', 'true');
        form.addEventListener('submit', e => {
            let valid = true;
            form.querySelectorAll('input, textarea, select').forEach(field => {
                const errorId = field.name + '-error';
                let error = form.querySelector('#' + errorId);
                if (field.required && !field.value.trim()) {
                    valid = false;
                    if (!error) {
                        error = document.createElement('div');
                        error.id = errorId;
                        error.className = 'input-error';
                        error.setAttribute('role', 'alert');
                        error.setAttribute('aria-live', 'polite');
                        error.textContent = 'Bu alan zorunludur.';
                        field.setAttribute('aria-invalid', 'true');
                        field.setAttribute('aria-describedby', errorId);
                        field.parentNode.insertBefore(error, field.nextSibling);
                    }
                } else {
                    if (error) error.remove();
                    field.removeAttribute('aria-invalid');
                    field.removeAttribute('aria-describedby');
                }
            });
            if (!valid) {
                e.preventDefault();
                showToast('Lütfen gerekli alanları doldurun.', 'error');
            }
        });
    });
}
window.addEventListener('DOMContentLoaded', enableFormValidation);
window.addEventListener('load', enableFormValidation);
// Erişilebilirlik: Tüm formlara etiket ekle
function enableAccessibleLabels() {
    document.querySelectorAll('input, textarea, select').forEach(field => {
        if (!field.labels.length) {
            const label = document.createElement('label');
            label.textContent = field.placeholder || field.name || 'Bu alan';
            label.htmlFor = field.id || field.name;
            field.parentNode.insertBefore(label, field);
        }
    });
}