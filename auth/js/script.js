/**
 * AI Destekli Modern Auth Script v2.1.0
 * Gelişmiş form validasyonu ve AI entegrasyonu
 * 
 * @author Mert Doğanay
 * @version 2.1.0
 * @since 2024
 */

// Strict mode kullanımı
'use strict';

// Modern modül sistemi
const AuthScript = (() => {
    
    // Konfigürasyon
    const CONFIG = {
        API_ENDPOINT: '/api/auth',
        VALIDATION_DELAY: 300,
        ANIMATION_DURATION: 300,
        MAX_RETRIES: 3,
        PASSWORD_MIN_LENGTH: 8,
        EMAIL_REGEX: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
        USERNAME_REGEX: /^[a-zA-Z0-9_]{3,20}$/
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

    // AI Destekli Form Validasyonu
    const aiFormValidator = {
        rules: {
            username: {
                required: true,
                minLength: 3,
                maxLength: 20,
                pattern: CONFIG.USERNAME_REGEX,
                messages: {
                    required: 'Kullanıcı adı gereklidir.',
                    minLength: 'Kullanıcı adı en az 3 karakter olmalıdır.',
                    maxLength: 'Kullanıcı adı en fazla 20 karakter olabilir.',
                    pattern: 'Kullanıcı adı sadece harf, rakam ve alt çizgi içerebilir.'
                }
            },
            email: {
                required: true,
                pattern: CONFIG.EMAIL_REGEX,
                messages: {
                    required: 'E-posta adresi gereklidir.',
                    pattern: 'Geçerli bir e-posta adresi giriniz.'
                }
            },
            password: {
                required: true,
                minLength: CONFIG.PASSWORD_MIN_LENGTH,
                messages: {
                    required: 'Şifre gereklidir.',
                    minLength: `Şifre en az ${CONFIG.PASSWORD_MIN_LENGTH} karakter olmalıdır.`
                }
            },
            confirmPassword: {
                required: true,
                match: 'password',
                messages: {
                    required: 'Şifre tekrarı gereklidir.',
                    match: 'Şifreler eşleşmiyor.'
                }
            }
        },

        init() {
            this.bindFormEvents();
            this.createPasswordStrengthMeter();
        },

        bindFormEvents() {
            const form = utils.$('.auth-container form');
            if (!form) return;

            // Form submit eventi
            form.addEventListener('submit', (e) => this.handleSubmit(e));

            // Input validation events
            const inputs = form.querySelectorAll('input[required]');
            inputs.forEach(input => {
                input.addEventListener('blur', () => this.validateField(input));
                input.addEventListener('input', utils.debounce(() => this.validateField(input), CONFIG.VALIDATION_DELAY));
                input.addEventListener('keyup', () => this.validateField(input));
            });

            // Password strength check
            const passwordInput = form.querySelector('input[type="password"]');
            if (passwordInput) {
                passwordInput.addEventListener('input', () => this.checkPasswordStrength(passwordInput));
            }
        },

        validateField(field) {
            const fieldName = field.name || field.type;
            const value = field.value.trim();
            const rule = this.rules[fieldName];

            if (!rule) return true;

            let isValid = true;
            let errorMessage = '';

            // Required check
            if (rule.required && !value) {
                isValid = false;
                errorMessage = rule.messages.required;
            }

            // Min length check
            if (isValid && rule.minLength && value.length < rule.minLength) {
                isValid = false;
                errorMessage = rule.messages.minLength;
            }

            // Max length check
            if (isValid && rule.maxLength && value.length > rule.maxLength) {
                isValid = false;
                errorMessage = rule.messages.maxLength;
            }

            // Pattern check
            if (isValid && rule.pattern && !rule.pattern.test(value)) {
                isValid = false;
                errorMessage = rule.messages.pattern;
            }

            // Match check (for confirm password)
            if (isValid && rule.match) {
                const matchField = utils.$(`input[name="${rule.match}"]`);
                if (matchField && value !== matchField.value) {
                    isValid = false;
                    errorMessage = rule.messages.match;
                }
            }

            // Apply visual feedback
            this.applyFieldValidation(field, isValid, errorMessage);

            return isValid;
        },

        applyFieldValidation(field, isValid, errorMessage) {
            // Remove existing classes
            field.classList.remove('input-error', 'input-success', 'input-warning');

            if (isValid) {
                field.classList.add('input-success');
                this.removeFieldError(field);
            } else {
                field.classList.add('input-error');
                this.showFieldError(field, errorMessage);
                
                // Shake animation
                utils.animate(field, [
                    { transform: 'translateX(0)' },
                    { transform: 'translateX(-5px)' },
                    { transform: 'translateX(5px)' },
                    { transform: 'translateX(-5px)' },
                    { transform: 'translateX(5px)' },
                    { transform: 'translateX(0)' }
                ], { duration: 300 });
            }
        },

        showFieldError(field, message) {
            // Remove existing error
            this.removeFieldError(field);

            // Create error element
            const errorDiv = utils.createElement('div', {
                className: 'field-error',
                textContent: message
            });

            // Style error element
            Object.assign(errorDiv.style, {
                color: 'var(--error)',
                fontSize: '0.875rem',
                marginTop: '0.25rem',
                fontWeight: '500',
                animation: 'fadeIn 0.3s ease-out'
            });

            // Insert after field
            field.parentNode.insertBefore(errorDiv, field.nextSibling);
        },

        removeFieldError(field) {
            const existingError = field.parentNode.querySelector('.field-error');
            if (existingError) {
                existingError.remove();
            }
        },

        checkPasswordStrength(passwordInput) {
            const password = passwordInput.value;
            const strengthMeter = utils.$('.password-strength');
            
            if (!strengthMeter) return;

            const strength = this.calculatePasswordStrength(password);
            const strengthText = this.getStrengthText(strength);
            const strengthColor = this.getStrengthColor(strength);

            strengthMeter.textContent = strengthText;
            strengthMeter.style.color = strengthColor;
            strengthMeter.style.fontWeight = '600';
        },

        calculatePasswordStrength(password) {
            let score = 0;
            
            if (password.length >= CONFIG.PASSWORD_MIN_LENGTH) score += 1;
            if (/[a-z]/.test(password)) score += 1;
            if (/[A-Z]/.test(password)) score += 1;
            if (/[0-9]/.test(password)) score += 1;
            if (/[^A-Za-z0-9]/.test(password)) score += 1;
            
            return Math.min(score, 5);
        },

        getStrengthText(strength) {
            const texts = ['Çok Zayıf', 'Zayıf', 'Orta', 'Güçlü', 'Çok Güçlü'];
            return texts[strength - 1] || 'Çok Zayıf';
        },

        getStrengthColor(strength) {
            const colors = [
                'var(--error)',
                'var(--warning)',
                'var(--warning)',
                'var(--success)',
                'var(--success)'
            ];
            return colors[strength - 1] || 'var(--error)';
        },

        createPasswordStrengthMeter() {
            const passwordInput = utils.$('input[type="password"]');
            if (!passwordInput) return;

            const strengthMeter = utils.createElement('div', {
                className: 'password-strength',
                textContent: 'Şifre gücü'
            });

            Object.assign(strengthMeter.style, {
                fontSize: '0.875rem',
                marginTop: '0.25rem',
                fontWeight: '500',
                color: 'var(--gray-500)'
            });

            passwordInput.parentNode.insertBefore(strengthMeter, passwordInput.nextSibling);
        },

        async handleSubmit(e) {
            e.preventDefault();
            
            const form = e.target;
            const submitButton = form.querySelector('button[type="submit"]');
            const originalText = submitButton?.textContent;
            
            // Form validasyonu
            const inputs = form.querySelectorAll('input[required]');
            let isValid = true;
            
            inputs.forEach(input => {
                if (!this.validateField(input)) {
                    isValid = false;
                }
            });
            
            if (!isValid) {
                this.showMessage('Lütfen tüm alanları doğru şekilde doldurun.', 'error');
                return;
            }
            
            // Submit butonunu devre dışı bırak
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="loading"></span> AI Kontrol Ediyor...';
                submitButton.classList.add('ai-loading');
            }
            
            try {
                // AI destekli güvenlik kontrolü
                await this.performAISecurityCheck(form);
                
                // Form verilerini gönder
                const formData = new FormData(form);
                const response = await this.submitForm(formData);
                
                if (response.success) {
                    this.showMessage('AI güvenlik kontrolü geçildi! Giriş başarılı.', 'success');
                    setTimeout(() => form.submit(), 1000);
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
                    submitButton.classList.remove('ai-loading');
                }
            }
        },

        async performAISecurityCheck(form) {
            // AI güvenlik kontrolü simülasyonu
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);
            
            // Basit güvenlik kontrolleri
            const checks = [
                this.checkSuspiciousPatterns(data),
                this.checkPasswordStrength(data.password),
                this.checkEmailValidity(data.email)
            ];
            
            const results = await Promise.all(checks);
            const hasIssues = results.some(result => !result.pass);
            
            if (hasIssues) {
                const issues = results.filter(result => !result.pass);
                throw new Error(`AI güvenlik uyarısı: ${issues[0].message}`);
            }
            
            // Simüle edilmiş AI işlem süresi
            await new Promise(resolve => setTimeout(resolve, 1000 + Math.random() * 2000));
        },

        async checkSuspiciousPatterns(data) {
            // Şüpheli pattern kontrolü
            const suspiciousPatterns = [
                /admin/i,
                /password/i,
                /123456/,
                /qwerty/i
            ];
            
            for (const pattern of suspiciousPatterns) {
                if (pattern.test(data.username) || pattern.test(data.password)) {
                    return {
                        pass: false,
                        message: 'Güvenlik nedeniyle bu kullanıcı adı/şifre kullanılamaz.'
                    };
                }
            }
            
            return { pass: true };
        },

        async checkPasswordStrength(password) {
            const strength = this.calculatePasswordStrength(password);
            
            if (strength < 3) {
                return {
                    pass: false,
                    message: 'Şifre gücü yetersiz. Daha güçlü bir şifre seçin.'
                };
            }
            
            return { pass: true };
        },

        async checkEmailValidity(email) {
            if (!CONFIG.EMAIL_REGEX.test(email)) {
                return {
                    pass: false,
                    message: 'Geçersiz e-posta formatı.'
                };
            }
            
            return { pass: true };
        },

        async submitForm(formData) {
            // Form gönderme simülasyonu
            await new Promise(resolve => setTimeout(resolve, 500));
            
            return {
                success: true,
                message: 'Form başarıyla gönderildi!'
            };
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

    // AI Destekli Tema Yöneticisi
    const aiThemeManager = {
        init() {
            this.createThemeToggle();
            this.bindEvents();
            this.loadTheme();
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
        },

        loadTheme() {
            const savedTheme = localStorage.getItem('auth-theme') || 'light';
            this.setTheme(savedTheme === 'dark');
        },

        setTheme(dark) {
            document.body.classList.toggle('dark', dark);
            
            const toggle = utils.$('.theme-toggle');
            if (toggle) {
                toggle.innerHTML = dark ? '☀️' : '🌙';
            }
            
            localStorage.setItem('auth-theme', dark ? 'dark' : 'light');
            
            // AI tema önerisi
            this.suggestTheme(dark);
        },

        toggleTheme() {
            const isDark = document.body.classList.contains('dark');
            this.setTheme(!isDark);
        },

        suggestTheme(dark) {
            const hour = new Date().getHours();
            const isNight = hour < 6 || hour > 18;
            
            if (dark !== isNight) {
                const suggestion = isNight ? 'dark' : 'light';
                const message = isNight 
                    ? '🤖 AI: Gece saatleri için koyu tema öneriyorum!'
                    : '🤖 AI: Gün ışığı için açık tema öneriyorum!';
                
                aiFormValidator.showMessage(message, 'info');
            }
        }
    };

    // Performans İzleyici
    const performanceMonitor = {
        init() {
            this.measureFormPerformance();
        },

        measureFormPerformance() {
            const form = utils.$('.auth-container form');
            if (!form) return;

            const startTime = performance.now();
            
            form.addEventListener('submit', () => {
                const endTime = performance.now();
                const duration = endTime - startTime;
                console.log(`Form işlem süresi: ${duration.toFixed(2)}ms`);
            });
        }
    };

    // Ana uygulama sınıfı
    class AuthScriptApp {
        constructor() {
            this.modules = [
                aiFormValidator,
                aiThemeManager,
                performanceMonitor
            ];
        }

        init() {
            console.log('🤖 AI Auth Script başlatılıyor...');
            
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

                console.log('✅ AI Auth Script başarıyla başlatıldı!');
                
            } catch (error) {
                console.error('❌ AI Auth Script başlatma hatası:', error);
            }
        }
    }

    // Global fonksiyonlar (geriye uyumluluk için)
    window.setTheme = (dark) => aiThemeManager.setTheme(dark);
    window.toggleTheme = () => aiThemeManager.toggleTheme();

    // Uygulamayı başlat
    const app = new AuthScriptApp();
    app.init();

    // Public API
    return {
        app,
        utils,
        aiFormValidator,
        aiThemeManager,
        performanceMonitor,
        CONFIG
    };

})();

// Global hata yakalayıcı
window.addEventListener('error', (event) => {
    console.error('Auth Script Global error:', event.error);
});

// Unhandled promise rejection yakalayıcı
window.addEventListener('unhandledrejection', (event) => {
    console.error('Auth Script Unhandled promise rejection:', event.reason);
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AuthScript;
}
