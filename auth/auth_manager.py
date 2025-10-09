#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Merthtmlcss Auth Manager
Kullanıcı kimlik doğrulama ve yetkilendirme yöneticisi
"""

import hashlib
import json
import datetime
import os
from typing import Dict, Optional, List

class AuthManager:
    """Kullanıcı kimlik doğrulama yöneticisi"""
    
    def __init__(self, users_file: str = "users.json"):
        self.users_file = users_file
        self.users = self.load_users()
        self.sessions = {}
        
        # Auth sistemi için CSS stilleri
        self.css_styles = """
        /* Merthtmlcss Auth Manager CSS Styles */
        .auth-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            color: white;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .auth-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .auth-header h1 {
            font-size: 28px;
            margin: 0;
            font-weight: 600;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .auth-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        
        .auth-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .form-group {
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 14px;
            opacity: 0.9;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: none;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 16px;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }
        
        .form-group input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }
        
        .form-group input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
        }
        
        .auth-button {
            background: linear-gradient(45deg, #ff6b6b, #ee5a24);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .auth-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.4);
        }
        
        .auth-button:active {
            transform: translateY(0);
        }
        
        .auth-links {
            text-align: center;
            margin-top: 20px;
        }
        
        .auth-links a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 14px;
            margin: 0 10px;
            transition: color 0.3s ease;
        }
        
        .auth-links a:hover {
            color: white;
            text-decoration: underline;
        }
        
        .auth-message {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
        }
        
        .auth-message.success {
            background: rgba(46, 204, 113, 0.2);
            border: 1px solid rgba(46, 204, 113, 0.3);
            color: #2ecc71;
        }
        
        .auth-message.error {
            background: rgba(231, 76, 60, 0.2);
            border: 1px solid rgba(231, 76, 60, 0.3);
            color: #e74c3c;
        }
        
        .auth-message.warning {
            background: rgba(241, 196, 15, 0.2);
            border: 1px solid rgba(241, 196, 15, 0.3);
            color: #f1c40f;
        }
        
        .user-info {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
        
        .user-info h3 {
            margin: 0 0 15px 0;
            font-size: 18px;
            color: #f39c12;
        }
        
        .user-detail {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .user-detail strong {
            color: #3498db;
        }
        
        .session-info {
            background: rgba(52, 152, 219, 0.1);
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            border-left: 4px solid #3498db;
        }
        
        .session-info h4 {
            margin: 0 0 10px 0;
            color: #3498db;
            font-size: 16px;
        }
        
        .session-detail {
            font-size: 12px;
            opacity: 0.8;
            margin-bottom: 5px;
        }
        
        /* Responsive Design */
        @media (max-width: 480px) {
            .auth-container {
                margin: 20px;
                padding: 20px;
            }
            
            .auth-header h1 {
                font-size: 24px;
            }
            
            .form-group input {
                font-size: 14px;
            }
            
            .auth-button {
                font-size: 14px;
                padding: 12px;
            }
        }
        
        /* Animation Effects */
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
        
        .auth-container {
            animation: fadeIn 0.6s ease-out;
        }
        
        .form-group {
            animation: fadeIn 0.6s ease-out;
            animation-fill-mode: both;
        }
        
        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }
        .form-group:nth-child(3) { animation-delay: 0.3s; }
        .form-group:nth-child(4) { animation-delay: 0.4s; }
        
        /* Loading Spinner */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Password Strength Indicator */
        .password-strength {
            height: 4px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 2px;
            margin-top: 8px;
            overflow: hidden;
        }
        
        .password-strength-bar {
            height: 100%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }
        
        .strength-weak { background: #e74c3c; width: 25%; }
        .strength-medium { background: #f39c12; width: 50%; }
        .strength-strong { background: #27ae60; width: 75%; }
        .strength-very-strong { background: #2ecc71; width: 100%; }
        """
        
    def get_css_styles(self) -> str:
        """CSS stillerini döndür"""
        return self.css_styles
    
    def generate_login_html(self) -> str:
        """Giriş sayfası HTML'i oluştur"""
        return f"""
        <!DOCTYPE html>
        <html lang="tr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Merthtmlcss - Giriş</title>
            <style>{self.css_styles}</style>
        </head>
        <body>
            <div class="auth-container">
                <div class="auth-header">
                    <h1>🔐 Giriş Yap</h1>
                    <p>Hesabınıza giriş yapın</p>
                </div>
                
                <form class="auth-form" id="loginForm">
                    <div class="form-group">
                        <label for="username">Kullanıcı Adı</label>
                        <input type="text" id="username" name="username" placeholder="Kullanıcı adınızı girin" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Şifre</label>
                        <input type="password" id="password" name="password" placeholder="Şifrenizi girin" required>
                        <div class="password-strength">
                            <div class="password-strength-bar" id="strengthBar"></div>
                        </div>
                    </div>
                    
                    <button type="submit" class="auth-button">
                        <span id="buttonText">Giriş Yap</span>
                        <span class="loading" id="loadingSpinner" style="display: none;"></span>
                    </button>
                </form>
                
                <div class="auth-links">
                    <a href="#" onclick="showRegister()">Hesap Oluştur</a>
                    <a href="#" onclick="showForgotPassword()">Şifremi Unuttum</a>
                </div>
                
                <div id="message"></div>
            </div>
            
            <script>
                document.getElementById('loginForm').addEventListener('submit', function(e) {{
                    e.preventDefault();
                    const username = document.getElementById('username').value;
                    const password = document.getElementById('password').value;
                    
                    // Loading göster
                    document.getElementById('buttonText').style.display = 'none';
                    document.getElementById('loadingSpinner').style.display = 'inline-block';
                    
                    // API çağrısı simülasyonu
                    setTimeout(() => {{
                        showMessage('Giriş başarılı! Yönlendiriliyorsunuz...', 'success');
                        setTimeout(() => {{
                            window.location.href = '/dashboard';
                        }}, 2000);
                    }}, 1500);
                }});
                
                function showMessage(text, type) {{
                    const messageDiv = document.getElementById('message');
                    messageDiv.innerHTML = `<div class="auth-message ${{type}}">${{text}}</div>`;
                }}
                
                function showRegister() {{
                    showMessage('Kayıt sayfasına yönlendiriliyorsunuz...', 'warning');
                }}
                
                function showForgotPassword() {{
                    showMessage('Şifre sıfırlama sayfasına yönlendiriliyorsunuz...', 'warning');
                }}
            </script>
        </body>
        </html>
        """
    
    def generate_register_html(self) -> str:
        """Kayıt sayfası HTML'i oluştur"""
        return f"""
        <!DOCTYPE html>
        <html lang="tr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Merthtmlcss - Kayıt Ol</title>
            <style>{self.css_styles}</style>
        </head>
        <body>
            <div class="auth-container">
                <div class="auth-header">
                    <h1>📝 Hesap Oluştur</h1>
                    <p>Yeni hesap oluşturun</p>
                </div>
                
                <form class="auth-form" id="registerForm">
                    <div class="form-group">
                        <label for="username">Kullanıcı Adı</label>
                        <input type="text" id="username" name="username" placeholder="Kullanıcı adınızı girin" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">E-posta</label>
                        <input type="email" id="email" name="email" placeholder="E-posta adresinizi girin" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Şifre</label>
                        <input type="password" id="password" name="password" placeholder="Şifrenizi girin" required>
                        <div class="password-strength">
                            <div class="password-strength-bar" id="strengthBar"></div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirmPassword">Şifre Tekrar</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Şifrenizi tekrar girin" required>
                    </div>
                    
                    <button type="submit" class="auth-button">
                        <span id="buttonText">Hesap Oluştur</span>
                        <span class="loading" id="loadingSpinner" style="display: none;"></span>
                    </button>
                </form>
                
                <div class="auth-links">
                    <a href="#" onclick="showLogin()">Zaten hesabım var</a>
                </div>
                
                <div id="message"></div>
            </div>
            
            <script>
                document.getElementById('registerForm').addEventListener('submit', function(e) {{
                    e.preventDefault();
                    const username = document.getElementById('username').value;
                    const email = document.getElementById('email').value;
                    const password = document.getElementById('password').value;
                    const confirmPassword = document.getElementById('confirmPassword').value;
                    
                    if (password !== confirmPassword) {{
                        showMessage('Şifreler eşleşmiyor!', 'error');
                        return;
                    }}
                    
                    // Loading göster
                    document.getElementById('buttonText').style.display = 'none';
                    document.getElementById('loadingSpinner').style.display = 'inline-block';
                    
                    // API çağrısı simülasyonu
                    setTimeout(() => {{
                        showMessage('Hesap başarıyla oluşturuldu! Giriş sayfasına yönlendiriliyorsunuz...', 'success');
                        setTimeout(() => {{
                            showLogin();
                        }}, 2000);
                    }}, 1500);
                }});
                
                function showMessage(text, type) {{
                    const messageDiv = document.getElementById('message');
                    messageDiv.innerHTML = `<div class="auth-message ${{type}}">${{text}}</div>`;
                }}
                
                function showLogin() {{
                    showMessage('Giriş sayfasına yönlendiriliyorsunuz...', 'warning');
                }}
            </script>
        </body>
        </html>
        """
    
    def load_users(self) -> Dict:
        """Kullanıcıları dosyadan yükle"""
        if os.path.exists(self.users_file):
            try:
                with open(self.users_file, 'r', encoding='utf-8') as f:
                    return json.load(f)
            except:
                return {}
        return {}
    
    def save_users(self) -> None:
        """Kullanıcıları dosyaya kaydet"""
        with open(self.users_file, 'w', encoding='utf-8') as f:
            json.dump(self.users, f, indent=2, ensure_ascii=False)
    
    def hash_password(self, password: str) -> str:
        """Şifreyi hash'le"""
        return hashlib.sha256(password.encode()).hexdigest()
    
    def register_user(self, username: str, password: str, email: str, role: str = "user") -> Dict:
        """Yeni kullanıcı kaydı"""
        if username in self.users:
            return {"success": False, "message": "Kullanıcı adı zaten mevcut"}
        
        if len(password) < 6:
            return {"success": False, "message": "Şifre en az 6 karakter olmalıdır"}
        
        user_data = {
            "username": username,
            "password_hash": self.hash_password(password),
            "email": email,
            "role": role,
            "created_at": datetime.datetime.now().isoformat(),
            "last_login": None,
            "is_active": True
        }
        
        self.users[username] = user_data
        self.save_users()
        
        return {"success": True, "message": "Kullanıcı başarıyla kaydedildi", "user": user_data}
    
    def login_user(self, username: str, password: str) -> Dict:
        """Kullanıcı girişi"""
        if username not in self.users:
            return {"success": False, "message": "Kullanıcı bulunamadı"}
        
        user = self.users[username]
        
        if not user["is_active"]:
            return {"success": False, "message": "Hesap devre dışı"}
        
        if user["password_hash"] != self.hash_password(password):
            return {"success": False, "message": "Hatalı şifre"}
        
        # Son giriş zamanını güncelle
        user["last_login"] = datetime.datetime.now().isoformat()
        self.save_users()
        
        # Session oluştur
        session_id = self.create_session(username)
        
        return {
            "success": True, 
            "message": "Giriş başarılı",
            "session_id": session_id,
            "user": {
                "username": user["username"],
                "email": user["email"],
                "role": user["role"]
            }
        }
    
    def create_session(self, username: str) -> str:
        """Session oluştur"""
        session_id = hashlib.md5(f"{username}{datetime.datetime.now()}".encode()).hexdigest()
        self.sessions[session_id] = {
            "username": username,
            "created_at": datetime.datetime.now().isoformat(),
            "expires_at": (datetime.datetime.now() + datetime.timedelta(hours=24)).isoformat()
        }
        return session_id
    
    def validate_session(self, session_id: str) -> Optional[Dict]:
        """Session doğrula"""
        if session_id not in self.sessions:
            return None
        
        session = self.sessions[session_id]
        expires_at = datetime.datetime.fromisoformat(session["expires_at"])
        
        if datetime.datetime.now() > expires_at:
            del self.sessions[session_id]
            return None
        
        return session
    
    def logout_user(self, session_id: str) -> Dict:
        """Kullanıcı çıkışı"""
        if session_id in self.sessions:
            del self.sessions[session_id]
            return {"success": True, "message": "Çıkış başarılı"}
        
        return {"success": False, "message": "Geçersiz session"}
    
    def change_password(self, username: str, old_password: str, new_password: str) -> Dict:
        """Şifre değiştirme"""
        if username not in self.users:
            return {"success": False, "message": "Kullanıcı bulunamadı"}
        
        user = self.users[username]
        
        if user["password_hash"] != self.hash_password(old_password):
            return {"success": False, "message": "Mevcut şifre hatalı"}
        
        if len(new_password) < 6:
            return {"success": False, "message": "Yeni şifre en az 6 karakter olmalıdır"}
        
        user["password_hash"] = self.hash_password(new_password)
        self.save_users()
        
        return {"success": True, "message": "Şifre başarıyla değiştirildi"}
    
    def get_user_info(self, username: str) -> Optional[Dict]:
        """Kullanıcı bilgilerini getir"""
        if username in self.users:
            user = self.users[username].copy()
            del user["password_hash"]
            return user
        return None
    
    def list_users(self) -> List[Dict]:
        """Tüm kullanıcıları listele"""
        users_list = []
        for username, user_data in self.users.items():
            user_info = user_data.copy()
            del user_info["password_hash"]
            users_list.append(user_info)
        return users_list
    
    def deactivate_user(self, username: str) -> Dict:
        """Kullanıcıyı devre dışı bırak"""
        if username not in self.users:
            return {"success": False, "message": "Kullanıcı bulunamadı"}
        
        self.users[username]["is_active"] = False
        self.save_users()
        
        return {"success": True, "message": "Kullanıcı devre dışı bırakıldı"}

def main():
    """Test fonksiyonu"""
    print("🔐 Merthtmlcss Auth Manager Başlatılıyor...")
    
    auth = AuthManager()
    
    # Test kullanıcıları oluştur
    print("\n📝 Test kullanıcıları oluşturuluyor...")
    
    result1 = auth.register_user("admin", "admin123", "admin@merthtmlcss.com", "admin")
    print(f"Admin kullanıcı: {result1['message']}")
    
    result2 = auth.register_user("mert", "mert123", "mert@merthtmlcss.com", "user")
    print(f"Mert kullanıcı: {result2['message']}")
    
    # Giriş testi
    print("\n🔑 Giriş testi yapılıyor...")
    login_result = auth.login_user("admin", "admin123")
    print(f"Giriş sonucu: {login_result['message']}")
    
    if login_result["success"]:
        session_id = login_result["session_id"]
        print(f"Session ID: {session_id}")
        
        # Session doğrulama
        session = auth.validate_session(session_id)
        if session:
            print(f"Session geçerli: {session['username']}")
        
        # Çıkış
        logout_result = auth.logout_user(session_id)
        print(f"Çıkış: {logout_result['message']}")
    
    # Kullanıcı listesi
    print("\n👥 Kullanıcı listesi:")
    users = auth.list_users()
    for user in users:
        print(f"- {user['username']} ({user['role']}) - {user['email']}")
    
    # CSS ve HTML dosyaları oluştur
    print("\n🎨 CSS ve HTML dosyaları oluşturuluyor...")
    
    # CSS dosyası oluştur
    with open("auth_styles.css", "w", encoding="utf-8") as f:
        f.write(auth.get_css_styles())
    print("✅ auth_styles.css oluşturuldu")
    
    # Giriş sayfası HTML'i oluştur
    with open("login.html", "w", encoding="utf-8") as f:
        f.write(auth.generate_login_html())
    print("✅ login.html oluşturuldu")
    
    # Kayıt sayfası HTML'i oluştur
    with open("register.html", "w", encoding="utf-8") as f:
        f.write(auth.generate_register_html())
    print("✅ register.html oluşturuldu")
    
    print("\n📁 Oluşturulan dosyalar:")
    print("- auth_styles.css (CSS stilleri)")
    print("- login.html (Giriş sayfası)")
    print("- register.html (Kayıt sayfası)")
    print("\n🌐 HTML dosyalarını tarayıcıda açarak test edebilirsiniz!")
    
    print("\n✅ Auth Manager testi tamamlandı!")

if __name__ == "__main__":
    main() 