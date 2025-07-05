/**
 * MERHTMLCSS - Gelişmiş JavaScript Kütüphanesi
 * Modern web uygulamaları için interaktif özellikler
 * @version 2.1.0
 * @author Merthtmlcss
 */

class MerthtmlcssApp {
    constructor() {
        this.isInitialized = false;
        this.animations = new AnimationManager();
        this.forms = new FormManager();
        this.ui = new UIManager();
        this.utils = new UtilityManager();
        this.init();
    }

    init() {
        if (this.isInitialized) return;
        
        document.addEventListener('DOMContentLoaded', () => {
            this.setupEventListeners();
            this.initializeComponents();
            this.startAnimations();
            this.isInitialized = true;
            console.log('🚀 Merthtmlcss App başlatıldı!');
            this.loadHakkindaFromXML();
        });
    }

    setupEventListeners() {
        // Form submit olayları
        this.forms.setupFormHandlers();
        
        // Buton tıklama olayları
        this.setupButtonHandlers();
        
        // Input focus olayları
        this.setupInputHandlers();
        
        // Sayfa yükleme animasyonu
        this.setupPageLoadAnimation();
        
        // Scroll olayları
        this.setupScrollHandlers();
        
        // Resize olayları
        this.setupResizeHandlers();
    }

    setupButtonHandlers() {
        // İletişim butonu
        const contactBtn = document.getElementById('js-buton');
        if (contactBtn) {
            contactBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleContactButton();
            });
        }

        // Tüm butonlar için ripple efekti
        document.querySelectorAll('.btn-primary, .btn-success, .btn-info, .btn-danger').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.animations.createRippleEffect(e);
            });
        });
    }

    setupInputHandlers() {
        const inputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="password"]');
        inputs.forEach(input => {
            // Focus animasyonu
            input.addEventListener('focus', () => {
                this.animations.inputFocusAnimation(input);
            });

            // Blur animasyonu
            input.addEventListener('blur', () => {
                this.animations.inputBlurAnimation(input);
            });

            // Real-time validation
            input.addEventListener('input', (e) => {
                this.forms.validateField(e.target);
            });

            // Enter key handler
            input.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    this.handleEnterKey(e);
                }
            });
        });
    }

    setupPageLoadAnimation() {
        const container = document.querySelector('.bilgi-kutusu');
        if (container) {
            setTimeout(() => {
                container.style.opacity = '1';
                container.style.transform = 'translateY(0)';
            }, 100);
        }
    }

    setupScrollHandlers() {
        let scrollTimeout;
        window.addEventListener('scroll', () => {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => {
                this.handleScrollEnd();
            }, 150);
        });
    }

    setupResizeHandlers() {
        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                this.handleResize();
            }, 250);
        });
    }

    initializeComponents() {
        // Tooltip sistemi
        this.ui.initializeTooltips();
        
        // Loading göstergeleri
        this.ui.initializeLoadingIndicators();
        
        // Toast mesajları
        this.ui.initializeToastSystem();
        
        // Modal sistemi
        this.ui.initializeModalSystem();
    }

    startAnimations() {
        // Sayfa elementlerini animate et
        this.animations.animatePageElements();
        
        // Parallax efekti
        this.animations.initParallaxEffect();
        
        // Typing animasyonu
        this.animations.initTypingAnimation();
    }

    handleContactButton() {
        const contactBtn = document.getElementById('js-buton');
        const messageEl = document.getElementById('js-mesaj');
        
        if (contactBtn && messageEl) {
            // Loading durumu
            contactBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>İşleniyor...</span>';
            contactBtn.disabled = true;

            // Simüle edilmiş işlem
            setTimeout(() => {
                // E-posta gönderme
                window.location.href = "mailto:info@merthtmlcss.com?subject=İletişim&body=Merhaba, Merthtmlcss ile ilgili bir sorum var.";
                
                // Mesaj gösterme
                messageEl.textContent = "E-posta uygulamanız açılmadıysa, lütfen info@merthtmlcss.com adresine manuel olarak e-posta gönderebilirsiniz.";
                messageEl.style.color = "#667eea";
                messageEl.style.fontWeight = "bold";
                
                // Toast mesajı
                this.ui.showToast('E-posta bağlantısı açıldı!', 'success');
                
                // Butonu reset et
                contactBtn.innerHTML = '<i class="fas fa-envelope"></i> <span>Bize Ulaşın</span>';
                contactBtn.disabled = false;
            }, 1000);
        }
    }

    handleEnterKey(e) {
        const form = e.target.closest('form');
        if (form) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.click();
            }
        }
    }

    handleScrollEnd() {
        // Scroll bittiğinde yapılacak işlemler
        this.animations.handleScrollEnd();
    }

    handleResize() {
        // Pencere boyutu değiştiğinde yapılacak işlemler
        this.ui.handleResize();
    }

    loadHakkindaFromXML() {
        fetch('hakkinda.xml')
            .then(response => response.text())
            .then(str => (new window.DOMParser()).parseFromString(str, "text/xml"))
            .then(data => {
                document.getElementById('hakkinda-baslik').textContent = data.getElementsByTagName('baslik')[0]?.textContent || '';
                document.getElementById('hakkinda-aciklama').textContent = data.getElementsByTagName('aciklama')[0]?.textContent || '';
                document.getElementById('hakkinda-gelistirici').textContent = 'Geliştirici: ' + (data.getElementsByTagName('gelistirici')[0]?.textContent || '');
                const iletisim = data.getElementsByTagName('iletisim')[0]?.textContent;
                if(iletisim) {
                    document.getElementById('hakkinda-iletisim').innerHTML = '<b>İletişim:</b> <a href="mailto:' + iletisim + '">' + iletisim + '</a>';
                }
                const twitter = data.getElementsByTagName('twitter')[0]?.textContent;
                const github = data.getElementsByTagName('github')[0]?.textContent;
                let sosyal = '';
                if(twitter) sosyal += '<a href="' + twitter + '" target="_blank">Twitter</a> ';
                if(github) sosyal += '<a href="' + github + '" target="_blank">GitHub</a>';
                if(sosyal) document.getElementById('hakkinda-sosyal').innerHTML = '<b>Sosyal:</b> ' + sosyal;
            });
    }
}

// Animasyon Yöneticisi
class AnimationManager {
    constructor() {
        this.animations = new Map();
    }

    createRippleEffect(e) {
        const button = e.currentTarget;
        const ripple = document.createElement('span');
        
        const rect = button.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        
        ripple.style.cssText = `
            position: absolute;
            width: ${size}px;
            height: ${size}px;
            left: ${x}px;
            top: ${y}px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: scale(0);
            animation: ripple 0.6s linear;
            pointer-events: none;
        `;
        
        button.appendChild(ripple);
        
        setTimeout(() => {
            ripple.remove();
        }, 600);
    }

    inputFocusAnimation(input) {
        const parent = input.parentElement;
        parent.style.transform = 'scale(1.02)';
        parent.style.transition = 'transform 0.3s ease';
        
        // Label animasyonu (eğer varsa)
        const label = parent.querySelector('label');
        if (label) {
            label.style.color = '#667eea';
            label.style.transform = 'translateY(-20px) scale(0.8)';
        }
    }

    inputBlurAnimation(input) {
        const parent = input.parentElement;
        parent.style.transform = 'scale(1)';
        
        // Label animasyonu (eğer varsa)
        const label = parent.querySelector('label');
        if (label) {
            label.style.color = '#666';
            label.style.transform = 'translateY(0) scale(1)';
        }
    }

    animatePageElements() {
        const elements = document.querySelectorAll('.bilgi-kutusu, .info-box, .message-success, .message-error, .message-info');
        elements.forEach((el, index) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                el.style.transition = 'all 0.6s ease';
                el.style.opacity = '1';
                el.style.transform = 'translateY(0)';
            }, index * 100);
        });
    }

    initParallaxEffect() {
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const parallaxElements = document.querySelectorAll('.bilgi-kutusu');
            
            parallaxElements.forEach(el => {
                const speed = 0.5;
                el.style.transform = `translateY(${scrolled * speed}px)`;
            });
        });
    }

    initTypingAnimation() {
        const typingElements = document.querySelectorAll('h1');
        typingElements.forEach(el => {
            const text = el.textContent;
            el.textContent = '';
            el.style.borderRight = '2px solid #667eea';
            
            let i = 0;
            const typeWriter = () => {
                if (i < text.length) {
                    el.textContent += text.charAt(i);
                    i++;
                    setTimeout(typeWriter, 100);
                } else {
                    el.style.borderRight = 'none';
                }
            };
            
            setTimeout(typeWriter, 1000);
        });
    }

    handleScrollEnd() {
        // Scroll bittiğinde animasyonları durdur
        const parallaxElements = document.querySelectorAll('.bilgi-kutusu');
        parallaxElements.forEach(el => {
            el.style.transition = 'transform 0.3s ease';
        });
    }
}

// Form Yöneticisi
class FormManager {
    constructor() {
        this.forms = new Map();
    }

    setupFormHandlers() {
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                this.handleFormSubmit(e);
            });
            
            // Form validasyonu
            this.setupFormValidation(form);
        });
    }

    handleFormSubmit(e) {
        const form = e.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        
        if (submitBtn) {
            // Loading durumu
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>İşleniyor...</span>';
            submitBtn.disabled = true;
            
            // Form validasyonu
            if (!this.validateForm(form)) {
                e.preventDefault();
                submitBtn.innerHTML = submitBtn.getAttribute('data-original-text') || '<span>Tekrar Dene</span>';
                submitBtn.disabled = false;
                return;
            }
        }
    }

    setupFormValidation(form) {
        const inputs = form.querySelectorAll('input[required]');
        inputs.forEach(input => {
            input.addEventListener('blur', () => {
                this.validateField(input);
            });
        });
    }

    validateField(field) {
        const value = field.value.trim();
        const fieldName = field.name;
        let isValid = true;
        let errorMessage = '';

        // Alan bazlı validasyon
        switch (fieldName) {
            case 'kullanici_adi':
            case 'yeni_kullanici_adi':
                if (value.length < 3) {
                    isValid = false;
                    errorMessage = 'Kullanıcı adı en az 3 karakter olmalıdır.';
                }
                break;
                
            case 'email':
            case 'yeni_email':
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    isValid = false;
                    errorMessage = 'Geçerli bir e-posta adresi giriniz.';
                }
                break;
                
            case 'yeni_ad':
            case 'yeni_soyad':
                if (value.length < 2) {
                    isValid = false;
                    errorMessage = 'Ad/Soyad en az 2 karakter olmalıdır.';
                }
                break;
        }

        this.showFieldValidation(field, isValid, errorMessage);
        return isValid;
    }

    validateForm(form) {
        const inputs = form.querySelectorAll('input[required]');
        let isValid = true;

        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });

        return isValid;
    }

    showFieldValidation(field, isValid, errorMessage) {
        const parent = field.parentElement;
        let errorElement = parent.querySelector('.field-error');

        if (!isValid) {
            if (!errorElement) {
                errorElement = document.createElement('div');
                errorElement.className = 'field-error';
                parent.appendChild(errorElement);
            }
            errorElement.textContent = errorMessage;
            errorElement.style.display = 'block';
            field.style.borderColor = '#dc3545';
        } else {
            if (errorElement) {
                errorElement.style.display = 'none';
            }
            field.style.borderColor = '#28a745';
        }
    }
}

// UI Yöneticisi
class UIManager {
    constructor() {
        this.toastContainer = null;
        this.modalContainer = null;
    }

    initializeTooltips() {
        const tooltipElements = document.querySelectorAll('[data-tooltip]');
        tooltipElements.forEach(el => {
            el.addEventListener('mouseenter', (e) => {
                this.showTooltip(e.target);
            });
            
            el.addEventListener('mouseleave', (e) => {
                this.hideTooltip(e.target);
            });
        });
    }

    initializeLoadingIndicators() {
        // Global loading göstergesi
        this.createLoadingIndicator();
    }

    initializeToastSystem() {
        this.createToastContainer();
    }

    initializeModalSystem() {
        this.createModalContainer();
    }

    showTooltip(element) {
        const tooltipText = element.getAttribute('data-tooltip');
        const tooltip = document.createElement('div');
        tooltip.className = 'tooltip';
        tooltip.textContent = tooltipText;
        
        document.body.appendChild(tooltip);
        
        const rect = element.getBoundingClientRect();
        tooltip.style.cssText = `
            position: absolute;
            top: ${rect.top - 40}px;
            left: ${rect.left + rect.width / 2}px;
            transform: translateX(-50%);
            background: #333;
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 12px;
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease;
        `;
        
        setTimeout(() => {
            tooltip.style.opacity = '1';
        }, 10);
        
        element.tooltip = tooltip;
    }

    hideTooltip(element) {
        if (element.tooltip) {
            element.tooltip.remove();
            element.tooltip = null;
        }
    }

    createLoadingIndicator() {
        const loading = document.createElement('div');
        loading.id = 'global-loading';
        loading.innerHTML = '<div class="spinner"></div>';
        loading.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        `;
        
        document.body.appendChild(loading);
    }

    showLoading() {
        const loading = document.getElementById('global-loading');
        if (loading) {
            loading.style.display = 'flex';
        }
    }

    hideLoading() {
        const loading = document.getElementById('global-loading');
        if (loading) {
            loading.style.display = 'none';
        }
    }

    createToastContainer() {
        this.toastContainer = document.createElement('div');
        this.toastContainer.id = 'toast-container';
        this.toastContainer.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
        `;
        
        document.body.appendChild(this.toastContainer);
    }

    showToast(message, type = 'info', duration = 3000) {
        if (!this.toastContainer) {
            this.createToastContainer();
        }

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <i class="fas fa-${this.getToastIcon(type)}"></i>
            <span>${message}</span>
        `;
        
        toast.style.cssText = `
            background: ${this.getToastColor(type)};
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transform: translateX(100%);
            transition: transform 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        `;
        
        this.toastContainer.appendChild(toast);
        
        setTimeout(() => {
            toast.style.transform = 'translateX(0)';
        }, 10);
        
        setTimeout(() => {
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, duration);
    }

    getToastIcon(type) {
        const icons = {
            success: 'check-circle',
            error: 'exclamation-triangle',
            warning: 'exclamation-circle',
            info: 'info-circle'
        };
        return icons[type] || 'info-circle';
    }

    getToastColor(type) {
        const colors = {
            success: '#28a745',
            error: '#dc3545',
            warning: '#ffc107',
            info: '#17a2b8'
        };
        return colors[type] || '#17a2b8';
    }

    createModalContainer() {
        this.modalContainer = document.createElement('div');
        this.modalContainer.id = 'modal-container';
        this.modalContainer.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 10001;
        `;
        
        document.body.appendChild(this.modalContainer);
    }

    showModal(content, title = 'Modal') {
        if (!this.modalContainer) {
            this.createModalContainer();
        }

        const modal = document.createElement('div');
        modal.className = 'modal';
        modal.innerHTML = `
            <div class="modal-header">
                <h3>${title}</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                ${content}
            </div>
        `;
        
        modal.style.cssText = `
            background: white;
            border-radius: 12px;
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            transform: scale(0.7);
            transition: transform 0.3s ease;
        `;
        
        this.modalContainer.appendChild(modal);
        this.modalContainer.style.display = 'flex';
        
        setTimeout(() => {
            modal.style.transform = 'scale(1)';
        }, 10);
        
        // Close button
        const closeBtn = modal.querySelector('.modal-close');
        closeBtn.addEventListener('click', () => {
            this.hideModal();
        });
        
        // Click outside to close
        this.modalContainer.addEventListener('click', (e) => {
            if (e.target === this.modalContainer) {
                this.hideModal();
            }
        });
    }

    hideModal() {
        if (this.modalContainer) {
            const modal = this.modalContainer.querySelector('.modal');
            if (modal) {
                modal.style.transform = 'scale(0.7)';
                setTimeout(() => {
                    this.modalContainer.style.display = 'none';
                    this.modalContainer.innerHTML = '';
                }, 300);
            }
        }
    }

    handleResize() {
        // Responsive davranışlar
        const isMobile = window.innerWidth <= 768;
        
        // Mobil için özel ayarlar
        if (isMobile) {
            document.body.classList.add('mobile');
        } else {
            document.body.classList.remove('mobile');
        }
    }
}

// Utility Yöneticisi
class UtilityManager {
    constructor() {
        this.debounceTimers = new Map();
    }

    debounce(func, delay) {
        const key = func.toString();
        clearTimeout(this.debounceTimers.get(key));
        
        const timer = setTimeout(() => {
            func();
            this.debounceTimers.delete(key);
        }, delay);
        
        this.debounceTimers.set(key, timer);
    }

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
        }
    }

    formatDate(date) {
        return new Intl.DateTimeFormat('tr-TR', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        }).format(date);
    }

    generateId() {
        return Math.random().toString(36).substr(2, 9);
    }

    copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            console.log('Metin panoya kopyalandı:', text);
        }).catch(err => {
            console.error('Kopyalama hatası:', err);
        });
    }
}

// CSS Animasyonları
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #667eea;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    .field-error {
        color: #dc3545;
        font-size: 12px;
        margin-top: 5px;
        display: none;
    }
    
    .tooltip {
        position: absolute;
        z-index: 1000;
    }
    
    .button-group {
        display: flex;
        gap: 15px;
        justify-content: center;
        flex-wrap: wrap;
        margin: 20px 0;
    }
    
    .footer-actions {
        display: flex;
        gap: 15px;
        justify-content: center;
        flex-wrap: wrap;
        margin: 30px 0 20px 0;
    }
    
    .link-hover {
        display: inline-block;
        margin-top: 15px;
        padding: 10px 20px;
        background: rgba(102, 126, 234, 0.1);
        border-radius: 25px;
        transition: all 0.3s ease;
    }
    
    .link-hover:hover {
        background: rgba(102, 126, 234, 0.2);
        transform: translateY(-2px);
    }
    
    .bilgi-kutusu {
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.8s ease;
    }
    
    .btn-primary i,
    .btn-success i,
    .btn-info i,
    .btn-danger i {
        margin-right: 8px;
    }
    
    .message-success i,
    .message-error i,
    .message-info i {
        margin-right: 10px;
    }
    
    .info-box h3 i {
        margin-right: 10px;
    }
    
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        border-bottom: 1px solid #eee;
    }
    
    .modal-body {
        padding: 20px;
    }
    
    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #666;
    }
    
    .modal-close:hover {
        color: #333;
    }
    
    .mobile .bilgi-kutusu {
        margin: 10px;
        padding: 20px;
    }
    
    .mobile .button-group,
    .mobile .footer-actions {
        flex-direction: column;
    }
    
    .mobile .btn-primary,
    .mobile .btn-success,
    .mobile .btn-info,
    .mobile .btn-danger {
        width: 100%;
    }
`;

document.head.appendChild(style);

// Uygulamayı başlat
const app = new MerthtmlcssApp();

// Global erişim için
window.MerthtmlcssApp = app;
