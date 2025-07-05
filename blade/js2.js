// Merthtmlcss - Form Validasyonu ve Etkileşim Yöneticisi
class FormManager {
    constructor() {
        this.forms = [];
        this.init();
    }

    init() {
        this.setupFormValidation();
        this.createInteractiveElements();
        this.setupScrollEffects();
        console.log("📝 Merthtmlcss Form Yöneticisi başlatıldı");
    }

    setupFormValidation() {
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            this.forms.push(form);
            form.addEventListener('submit', (e) => this.handleSubmit(e));
            form.addEventListener('input', (e) => this.validateField(e.target));
        });
    }

    validateField(field) {
        const value = field.value.trim();
        const fieldType = field.type;
        const fieldName = field.name || field.id;
        
        // Hata mesajını temizle
        this.removeFieldError(field);
        
        let isValid = true;
        let errorMessage = '';

        // Email validasyonu
        if (fieldType === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                isValid = false;
                errorMessage = 'Geçerli bir email adresi giriniz';
            }
        }

        // Telefon validasyonu
        if (fieldName.toLowerCase().includes('phone') && value) {
            const phoneRegex = /^[\+]?[0-9\s\-\(\)]{10,}$/;
            if (!phoneRegex.test(value)) {
                isValid = false;
                errorMessage = 'Geçerli bir telefon numarası giriniz';
            }
        }

        // Zorunlu alan kontrolü
        if (field.hasAttribute('required') && !value) {
            isValid = false;
            errorMessage = 'Bu alan zorunludur';
        }

        // Minimum uzunluk kontrolü
        const minLength = field.getAttribute('minlength');
        if (minLength && value.length < parseInt(minLength)) {
            isValid = false;
            errorMessage = `En az ${minLength} karakter giriniz`;
        }

        if (!isValid) {
            this.showFieldError(field, errorMessage);
        } else {
            this.showFieldSuccess(field);
        }

        return isValid;
    }

    showFieldError(field, message) {
        field.style.borderColor = '#ff4757';
        field.style.boxShadow = '0 0 0 2px rgba(255, 71, 87, 0.2)';
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error';
        errorDiv.textContent = message;
        errorDiv.style.cssText = `
            color: #ff4757;
            font-size: 12px;
            margin-top: 5px;
            animation: slideDown 0.3s ease-out;
        `;
        
        field.parentNode.appendChild(errorDiv);
    }

    showFieldSuccess(field) {
        field.style.borderColor = '#2ed573';
        field.style.boxShadow = '0 0 0 2px rgba(46, 213, 115, 0.2)';
    }

    removeFieldError(field) {
        const errorDiv = field.parentNode.querySelector('.field-error');
        if (errorDiv) {
            errorDiv.remove();
        }
    }

    handleSubmit(e) {
        e.preventDefault();
        const form = e.target;
        const fields = form.querySelectorAll('input, textarea, select');
        
        let isValid = true;
        fields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });

        if (isValid) {
            this.showSuccessMessage('Form başarıyla gönderildi!');
            form.reset();
            // Form alanlarını temizle
            fields.forEach(field => {
                field.style.borderColor = '';
                field.style.boxShadow = '';
            });
        } else {
            this.showErrorMessage('Lütfen tüm hataları düzeltiniz');
        }
    }

    createInteractiveElements() {
        // Buton hover efektleri
        const buttons = document.querySelectorAll('button, .button, input[type="submit"]');
        buttons.forEach(button => {
            button.addEventListener('mouseenter', () => {
                button.style.transform = 'translateY(-2px)';
                button.style.boxShadow = '0 8px 25px rgba(0,0,0,0.3)';
            });
            
            button.addEventListener('mouseleave', () => {
                button.style.transform = 'translateY(0)';
                button.style.boxShadow = '';
            });
        });

        // Kart hover efektleri
        const cards = document.querySelectorAll('.card, .box, [class*="card"]');
        cards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'scale(1.02)';
                card.style.boxShadow = '0 10px 30px rgba(0,0,0,0.2)';
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'scale(1)';
                card.style.boxShadow = '';
            });
        });
    }

    setupScrollEffects() {
        let ticking = false;
        
        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(() => {
                    this.updateScrollEffects();
                    ticking = false;
                });
                ticking = true;
            }
        });
    }

    updateScrollEffects() {
        const scrolled = window.pageYOffset;
        const parallaxElements = document.querySelectorAll('[data-parallax]');
        
        parallaxElements.forEach(element => {
            const speed = element.getAttribute('data-parallax') || 0.5;
            const yPos = -(scrolled * speed);
            element.style.transform = `translateY(${yPos}px)`;
        });
    }

    showSuccessMessage(message) {
        this.showNotification(message, 'success');
    }

    showErrorMessage(message) {
        this.showNotification(message, 'error');
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.textContent = message;
        notification.className = `notification ${type}`;
        
        const colors = {
            success: '#2ed573',
            error: '#ff4757',
            info: '#3742fa'
        };
        
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: ${colors[type]};
            color: white;
            padding: 15px 25px;
            border-radius: 25px;
            z-index: 1002;
            animation: slideDown 0.5s ease-out;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            font-weight: 500;
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideUp 0.5s ease-in';
            setTimeout(() => notification.remove(), 500);
        }, 3000);
    }
}

// CSS animasyonlarını ekle
const formStyle = document.createElement('style');
formStyle.textContent = `
    @keyframes slideDown {
        from { transform: translateX(-50%) translateY(-100%); opacity: 0; }
        to { transform: translateX(-50%) translateY(0); opacity: 1; }
    }
    
    @keyframes slideUp {
        from { transform: translateX(-50%) translateY(0); opacity: 1; }
        to { transform: translateX(-50%) translateY(-100%); opacity: 0; }
    }
    
    input, textarea, select {
        transition: all 0.3s ease;
    }
    
    button, .button {
        transition: all 0.3s ease;
    }
    
    .card, .box {
        transition: all 0.3s ease;
    }
`;
document.head.appendChild(formStyle);

// Form yöneticisini başlat
const formManager = new FormManager(); 