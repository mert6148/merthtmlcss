#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Merthtmlcss API Server v2.0
Gelişmiş RESTful API sunucusu
"""

from flask import Flask, request, jsonify, session, g
from flask_cors import CORS
from flask_limiter import Limiter
from flask_limiter.util import get_remote_address
import json
import datetime
import os
import logging
import hashlib
import jwt
from functools import wraps
from typing import Dict, Any, Optional, List, Union
from dataclasses import dataclass, asdict
from pathlib import Path
import asyncio
import aiohttp

# Kendi modüllerimizi import ediyoruz
from auth_manager import AuthManager
from database_manager import DatabaseManager

# Konfigürasyon
@dataclass
class APIConfig:
    """API konfigürasyon ayarları"""
    secret_key: str = "merthtmlcss-secret-key-2024"
    jwt_secret: str = "jwt-secret-key-merthtmlcss"
    rate_limit: str = "100 per minute"
    debug_mode: bool = True
    cors_origins: List[str] = field(default_factory=lambda: ["*"])
    api_version: str = "v2.0"

# Flask uygulaması
app = Flask(__name__)
app.config['SECRET_KEY'] = APIConfig.secret_key
app.config['JWT_SECRET_KEY'] = APIConfig.jwt_secret

# CORS ve rate limiting
CORS(app, origins=APIConfig.cors_origins)
limiter = Limiter(
    app=app,
    key_func=get_remote_address,
    default_limits=[APIConfig.rate_limit]
)

# Logging ayarları
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s',
    handlers=[
        logging.FileHandler('api_server.log', encoding='utf-8'),
        logging.StreamHandler()
    ]
)

# Global nesneler
auth_manager = AuthManager()
db_manager = DatabaseManager()

# Middleware ve decorator'lar
def require_auth(f):
    """Kimlik doğrulama gerektiren endpoint'ler için decorator"""
    @wraps(f)
    def decorated_function(*args, **kwargs):
        token = request.headers.get('Authorization')
        if not token:
            return jsonify({"success": False, "message": "Token gerekli"}), 401
        
        try:
            # Bearer token formatını kontrol et
            if token.startswith('Bearer '):
                token = token[7:]
            
            # JWT token'ı doğrula
            payload = jwt.decode(token, APIConfig.jwt_secret, algorithms=['HS256'])
            g.current_user = payload
            return f(*args, **kwargs)
        except jwt.ExpiredSignatureError:
            return jsonify({"success": False, "message": "Token süresi dolmuş"}), 401
        except jwt.InvalidTokenError:
            return jsonify({"success": False, "message": "Geçersiz token"}), 401
    
    return decorated_function

def admin_required(f):
    """Admin yetkisi gerektiren endpoint'ler için decorator"""
    @wraps(f)
    def decorated_function(*args, **kwargs):
        if not hasattr(g, 'current_user'):
            return jsonify({"success": False, "message": "Kimlik doğrulama gerekli"}), 401
        
        if g.current_user.get('role') != 'admin':
            return jsonify({"success": False, "message": "Admin yetkisi gerekli"}), 403
        
        return f(*args, **kwargs)
    
    return decorated_function

@app.route('/')
def home():
    """Ana sayfa"""
    return jsonify({
        "message": "Merthtmlcss API Server",
        "version": APIConfig.api_version,
        "status": "active",
        "timestamp": datetime.datetime.now().isoformat(),
        "endpoints": {
            "auth": "/api/auth",
            "users": "/api/users",
            "messages": "/api/messages",
            "projects": "/api/projects",
            "stats": "/api/stats",
            "health": "/api/health",
            "docs": "/api/docs"
        },
        "features": [
            "JWT Authentication",
            "Rate Limiting",
            "CORS Support",
            "Logging",
            "Admin Panel"
        ]
    })

@app.route('/api/health')
def health_check():
    """Sağlık kontrolü endpoint'i"""
    try:
        # Veritabanı bağlantısını kontrol et
        db_status = "healthy" if db_manager.check_connection() else "unhealthy"
        
        return jsonify({
            "status": "healthy",
            "timestamp": datetime.datetime.now().isoformat(),
            "services": {
                "api": "healthy",
                "database": db_status,
                "auth": "healthy"
            },
            "uptime": "running"
        }), 200
    except Exception as e:
        logging.error(f"Health check hatası: {e}")
        return jsonify({
            "status": "unhealthy",
            "error": str(e)
        }), 500

@app.route('/api/docs')
def api_docs():
    """API dokümantasyonu"""
    return jsonify({
        "title": "Merthtmlcss API Dokümantasyonu",
        "version": APIConfig.api_version,
        "endpoints": {
            "authentication": {
                "POST /api/auth/register": "Kullanıcı kaydı",
                "POST /api/auth/login": "Kullanıcı girişi",
                "POST /api/auth/logout": "Kullanıcı çıkışı",
                "POST /api/auth/refresh": "Token yenileme"
            },
            "users": {
                "GET /api/users": "Kullanıcı listesi (Admin)",
                "GET /api/users/<username>": "Kullanıcı bilgisi",
                "PUT /api/users/<username>": "Kullanıcı güncelleme",
                "DELETE /api/users/<username>": "Kullanıcı silme (Admin)"
            },
            "projects": {
                "GET /api/projects": "Proje listesi",
                "POST /api/projects": "Proje ekleme",
                "PUT /api/projects/<id>": "Proje güncelleme",
                "DELETE /api/projects/<id>": "Proje silme"
            }
        },
        "authentication": "JWT Bearer Token",
        "rate_limits": APIConfig.rate_limit
    })

# ==================== AUTH ENDPOINTS ====================

@app.route('/api/auth/register', methods=['POST'])
@limiter.limit("5 per minute")
def register():
    """Kullanıcı kaydı"""
    try:
        data = request.get_json()
        username = data.get('username')
        password = data.get('password')
        email = data.get('email')
        role = data.get('role', 'user')
        
        # Veri doğrulama
        if not all([username, password, email]):
            return jsonify({
                "success": False, 
                "message": "Tüm alanlar gerekli",
                "required_fields": ["username", "password", "email"]
            }), 400
        
        # Şifre güvenliği kontrolü
        if len(password) < 8:
            return jsonify({
                "success": False,
                "message": "Şifre en az 8 karakter olmalıdır"
            }), 400
        
        # Email format kontrolü
        if '@' not in email or '.' not in email:
            return jsonify({
                "success": False,
                "message": "Geçerli bir email adresi giriniz"
            }), 400
        
        result = auth_manager.register_user(username, password, email, role)
        
        if result["success"]:
            # Veritabanına da ekle
            db_manager.add_user(username, email, auth_manager.hash_password(password), role)
            db_manager.add_log("INFO", f"Yeni kullanıcı kaydı: {username}")
            
            # JWT token oluştur
            token = jwt.encode({
                'username': username,
                'email': email,
                'role': role,
                'exp': datetime.datetime.utcnow() + datetime.timedelta(hours=24)
            }, APIConfig.jwt_secret, algorithm='HS256')
            
            result["token"] = token
            result["expires_in"] = "24h"
        
        return jsonify(result), 200 if result["success"] else 400
        
    except Exception as e:
        logging.error(f"Kayıt hatası: {str(e)}")
        db_manager.add_log("ERROR", f"Kayıt hatası: {str(e)}")
        return jsonify({"success": False, "message": "Sunucu hatası"}), 500

@app.route('/api/auth/login', methods=['POST'])
def login():
    """Kullanıcı girişi"""
    try:
        data = request.get_json()
        username = data.get('username')
        password = data.get('password')
        
        if not all([username, password]):
            return jsonify({"success": False, "message": "Kullanıcı adı ve şifre gerekli"}), 400
        
        result = auth_manager.login_user(username, password)
        
        if result["success"]:
            db_manager.update_user_login(username)
            db_manager.add_log("INFO", f"Kullanıcı girişi: {username}")
        
        return jsonify(result), 200 if result["success"] else 401
        
    except Exception as e:
        db_manager.add_log("ERROR", f"Giriş hatası: {str(e)}")
        return jsonify({"success": False, "message": "Sunucu hatası"}), 500

@app.route('/api/auth/logout', methods=['POST'])
def logout():
    """Kullanıcı çıkışı"""
    try:
        data = request.get_json()
        session_id = data.get('session_id')
        
        if not session_id:
            return jsonify({"success": False, "message": "Session ID gerekli"}), 400
        
        result = auth_manager.logout_user(session_id)
        return jsonify(result), 200
        
    except Exception as e:
        db_manager.add_log("ERROR", f"Çıkış hatası: {str(e)}")
        return jsonify({"success": False, "message": "Sunucu hatası"}), 500

@app.route('/api/auth/validate', methods=['POST'])
def validate_session():
    """Session doğrulama"""
    try:
        data = request.get_json()
        session_id = data.get('session_id')
        
        if not session_id:
            return jsonify({"success": False, "message": "Session ID gerekli"}), 400
        
        session = auth_manager.validate_session(session_id)
        
        if session:
            return jsonify({
                "success": True,
                "session": session
            }), 200
        else:
            return jsonify({"success": False, "message": "Geçersiz session"}), 401
        
    except Exception as e:
        logging.error(f"Session doğrulama hatası: {str(e)}")
        db_manager.add_log("ERROR", f"Session doğrulama hatası: {str(e)}")
        return jsonify({"success": False, "message": "Sunucu hatası"}), 500

@app.route('/api/auth/refresh', methods=['POST'])
@limiter.limit("10 per minute")
def refresh_token():
    """JWT token yenileme"""
    try:
        data = request.get_json()
        refresh_token = data.get('refresh_token')
        
        if not refresh_token:
            return jsonify({"success": False, "message": "Refresh token gerekli"}), 400
        
        try:
            # Refresh token'ı doğrula
            payload = jwt.decode(refresh_token, APIConfig.jwt_secret, algorithms=['HS256'])
            username = payload.get('username')
            
            # Yeni access token oluştur
            new_token = jwt.encode({
                'username': username,
                'email': payload.get('email'),
                'role': payload.get('role'),
                'exp': datetime.datetime.utcnow() + datetime.timedelta(hours=1)
            }, APIConfig.jwt_secret, algorithm='HS256')
            
            return jsonify({
                "success": True,
                "access_token": new_token,
                "expires_in": "1h"
            }), 200
            
        except jwt.ExpiredSignatureError:
            return jsonify({"success": False, "message": "Refresh token süresi dolmuş"}), 401
        except jwt.InvalidTokenError:
            return jsonify({"success": False, "message": "Geçersiz refresh token"}), 401
            
    except Exception as e:
        logging.error(f"Token yenileme hatası: {str(e)}")
        return jsonify({"success": False, "message": "Sunucu hatası"}), 500

@app.route('/api/auth/forgot-password', methods=['POST'])
@limiter.limit("3 per hour")
def forgot_password():
    """Şifre sıfırlama isteği"""
    try:
        data = request.get_json()
        email = data.get('email')
        
        if not email:
            return jsonify({"success": False, "message": "Email adresi gerekli"}), 400
        
        # Email format kontrolü
        if '@' not in email or '.' not in email:
            return jsonify({"success": False, "message": "Geçerli bir email adresi giriniz"}), 400
        
        # Şifre sıfırlama token'ı oluştur
        reset_token = hashlib.sha256(f"{email}{datetime.datetime.now()}".encode()).hexdigest()
        
        # Veritabanına kaydet
        db_manager.add_password_reset(email, reset_token)
        
        # Email gönderimi simülasyonu
        logging.info(f"Şifre sıfırlama isteği: {email}, Token: {reset_token}")
        
        return jsonify({
            "success": True,
            "message": "Şifre sıfırlama linki email adresinize gönderildi",
            "reset_token": reset_token  # Gerçek uygulamada bu gönderilmez
        }), 200
        
    except Exception as e:
        logging.error(f"Şifre sıfırlama hatası: {str(e)}")
        return jsonify({"success": False, "message": "Sunucu hatası"}), 500

# ==================== USER ENDPOINTS ====================

@app.route('/api/users', methods=['GET'])
@require_auth
@admin_required
def get_users():
    """Tüm kullanıcıları getir (Admin only)"""
    try:
        page = request.args.get('page', 1, type=int)
        per_page = request.args.get('per_page', 20, type=int)
        search = request.args.get('search', '')
        
        users = auth_manager.list_users()
        
        # Arama filtresi
        if search:
            users = [u for u in users if search.lower() in u.get('username', '').lower()]
        
        # Sayfalama
        total = len(users)
        start = (page - 1) * per_page
        end = start + per_page
        paginated_users = users[start:end]
        
        return jsonify({
            "success": True,
            "users": paginated_users,
            "pagination": {
                "page": page,
                "per_page": per_page,
                "total": total,
                "pages": (total + per_page - 1) // per_page
            }
        }), 200
        
    except Exception as e:
        logging.error(f"Kullanıcı listesi hatası: {str(e)}")
        db_manager.add_log("ERROR", f"Kullanıcı listesi hatası: {str(e)}")
        return jsonify({"success": False, "message": "Sunucu hatası"}), 500

@app.route('/api/users/profile', methods=['GET'])
@require_auth
def get_profile():
    """Mevcut kullanıcının profilini getir"""
    try:
        username = g.current_user.get('username')
        user = auth_manager.get_user_info(username)
        
        if user:
            # Hassas bilgileri gizle
            user.pop('password', None)
            return jsonify({
                "success": True,
                "user": user
            }), 200
        else:
            return jsonify({"success": False, "message": "Kullanıcı bulunamadı"}), 404
        
    except Exception as e:
        logging.error(f"Profil getirme hatası: {str(e)}")
        return jsonify({"success": False, "message": "Sunucu hatası"}), 500

@app.route('/api/users/profile', methods=['PUT'])
@require_auth
def update_profile():
    """Kullanıcı profilini güncelle"""
    try:
        username = g.current_user.get('username')
        data = request.get_json()
        
        allowed_fields = ['email', 'full_name', 'bio', 'avatar']
        update_data = {k: v for k, v in data.items() if k in allowed_fields}
        
        if not update_data:
            return jsonify({"success": False, "message": "Güncellenecek alan bulunamadı"}), 400
        
        result = auth_manager.update_user_info(username, update_data)
        
        if result["success"]:
            db_manager.add_log("INFO", f"Profil güncellendi: {username}")
        
        return jsonify(result), 200 if result["success"] else 400
        
    except Exception as e:
        logging.error(f"Profil güncelleme hatası: {str(e)}")
        return jsonify({"success": False, "message": "Sunucu hatası"}), 500

@app.route('/api/users/<username>', methods=['GET'])
def get_user(username):
    """Belirli kullanıcıyı getir"""
    try:
        user = auth_manager.get_user_info(username)
        
        if user:
            return jsonify({
                "success": True,
                "user": user
            }), 200
        else:
            return jsonify({"success": False, "message": "Kullanıcı bulunamadı"}), 404
        
    except Exception as e:
        db_manager.add_log("ERROR", f"Kullanıcı getirme hatası: {str(e)}")
        return jsonify({"success": False, "message": "Sunucu hatası"}), 500

@app.route('/api/users/<username>/change-password', methods=['POST'])
def change_password(username):
    """Şifre değiştirme"""
    try:
        data = request.get_json()
        old_password = data.get('old_password')
        new_password = data.get('new_password')
        
        if not all([old_password, new_password]):
            return jsonify({"success": False, "message": "Eski ve yeni şifre gerekli"}), 400
        
        result = auth_manager.change_password(username, old_password, new_password)
        
        if result["success"]:
            db_manager.add_log("INFO", f"Şifre değiştirildi: {username}")
        
        return jsonify(result), 200 if result["success"] else 400
        
    except Exception as e:
        db_manager.add_log("ERROR", f"Şifre değiştirme hatası: {str(e)}")
        return jsonify({"success": False, "message": "Sunucu hatası"}), 500

# ==================== MESSAGE ENDPOINTS ====================

@app.route('/api/messages', methods=['POST'])
def add_message():
    """Mesaj ekle"""
    try:
        data = request.get_json()
        name = data.get('name')
        email = data.get('email')
        message = data.get('message')
        
        if not all([name, email, message]):
            return jsonify({"success": False, "message": "Tüm alanlar gerekli"}), 400
        
        result = db_manager.add_message(name, email, message)
        
        if result["success"]:
            db_manager.add_log("INFO", f"Yeni mesaj: {name} ({email})")
        
        return jsonify(result), 200 if result["success"] else 400
        
    except Exception as e:
        db_manager.add_log("ERROR", f"Mesaj ekleme hatası: {str(e)}")
        return jsonify({"success": False, "message": "Sunucu hatası"}), 500

@app.route('/api/messages', methods=['GET'])
def get_messages():
    """Mesajları getir"""
    try:
        limit = request.args.get('limit', 10, type=int)
        offset = request.args.get('offset', 0, type=int)
        
        messages = db_manager.get_messages(limit, offset)
        
        return jsonify({
            "success": True,
            "messages": messages,
            "count": len(messages)
        }), 200
        
    except Exception as e:
        db_manager.add_log("ERROR", f"Mesaj listesi hatası: {str(e)}")
        return jsonify({"success": False, "message": "Sunucu hatası"}), 500

@app.route('/api/messages/<int:message_id>/read', methods=['POST'])
def mark_message_read(message_id):
    """Mesajı okundu olarak işaretle"""
    try:
        result = db_manager.mark_message_read(message_id)
        return jsonify(result), 200 if result["success"] else 400
        
    except Exception as e:
        db_manager.add_log("ERROR", f"Mesaj işaretleme hatası: {str(e)}")
        return jsonify({"success": False, "message": "Sunucu hatası"}), 500

# ==================== PROJECT ENDPOINTS ====================

@app.route('/api/projects', methods=['POST'])
def add_project():
    """Proje ekle"""
    try:
        data = request.get_json()
        title = data.get('title')
        description = data.get('description')
        technologies = data.get('technologies')
        status = data.get('status', 'active')
        
        if not all([title, description, technologies]):
            return jsonify({"success": False, "message": "Başlık, açıklama ve teknolojiler gerekli"}), 400
        
        result = db_manager.add_project(title, description, technologies, status)
        
        if result["success"]:
            db_manager.add_log("INFO", f"Yeni proje eklendi: {title}")
        
        return jsonify(result), 200 if result["success"] else 400
        
    except Exception as e:
        db_manager.add_log("ERROR", f"Proje ekleme hatası: {str(e)}")
        return jsonify({"success": False, "message": "Sunucu hatası"}), 500

@app.route('/api/projects', methods=['GET'])
def get_projects():
    """Projeleri getir"""
    try:
        status = request.args.get('status')
        category = request.args.get('category')
        search = request.args.get('search', '')
        page = request.args.get('page', 1, type=int)
        per_page = request.args.get('per_page', 10, type=int)
        
        projects = db_manager.get_projects(status)
        
        # Kategori filtresi
        if category:
            projects = [p for p in projects if p.get('category') == category]
        
        # Arama filtresi
        if search:
            projects = [p for p in projects if 
                       search.lower() in p.get('title', '').lower() or 
                       search.lower() in p.get('description', '').lower()]
        
        # Sayfalama
        total = len(projects)
        start = (page - 1) * per_page
        end = start + per_page
        paginated_projects = projects[start:end]
        
        return jsonify({
            "success": True,
            "projects": paginated_projects,
            "pagination": {
                "page": page,
                "per_page": per_page,
                "total": total,
                "pages": (total + per_page - 1) // per_page
            },
            "filters": {
                "status": status,
                "category": category,
                "search": search
            }
        }), 200
        
    except Exception as e:
        logging.error(f"Proje listesi hatası: {str(e)}")
        db_manager.add_log("ERROR", f"Proje listesi hatası: {str(e)}")
        return jsonify({"success": False, "message": "Sunucu hatası"}), 500

@app.route('/api/projects/categories', methods=['GET'])
def get_project_categories():
    """Proje kategorilerini getir"""
    try:
        categories = db_manager.get_project_categories()
        return jsonify({
            "success": True,
            "categories": categories
        }), 200
        
    except Exception as e:
        logging.error(f"Kategori listesi hatası: {str(e)}")
        return jsonify({"success": False, "message": "Sunucu hatası"}), 500

@app.route('/api/projects/<int:project_id>/like', methods=['POST'])
@require_auth
def like_project(project_id):
    """Projeyi beğen"""
    try:
        username = g.current_user.get('username')
        result = db_manager.like_project(project_id, username)
        
        if result["success"]:
            db_manager.add_log("INFO", f"Proje beğenildi: ID {project_id} by {username}")
        
        return jsonify(result), 200 if result["success"] else 400
        
    except Exception as e:
        logging.error(f"Proje beğenme hatası: {str(e)}")
        return jsonify({"success": False, "message": "Sunucu hatası"}), 500

@app.route('/api/projects/<int:project_id>/comment', methods=['POST'])
@require_auth
def add_project_comment(project_id):
    """Projeye yorum ekle"""
    try:
        username = g.current_user.get('username')
        data = request.get_json()
        comment = data.get('comment')
        
        if not comment:
            return jsonify({"success": False, "message": "Yorum metni gerekli"}), 400
        
        result = db_manager.add_project_comment(project_id, username, comment)
        
        if result["success"]:
            db_manager.add_log("INFO", f"Proje yorumu eklendi: ID {project_id} by {username}")
        
        return jsonify(result), 200 if result["success"] else 400
        
    except Exception as e:
        logging.error(f"Yorum ekleme hatası: {str(e)}")
        return jsonify({"success": False, "message": "Sunucu hatası"}), 500

@app.route('/api/projects/<int:project_id>', methods=['PUT'])
def update_project(project_id):
    """Proje güncelle"""
    try:
        data = request.get_json()
        result = db_manager.update_project(project_id, **data)
        
        if result["success"]:
            db_manager.add_log("INFO", f"Proje güncellendi: ID {project_id}")
        
        return jsonify(result), 200 if result["success"] else 400
        
    except Exception as e:
        db_manager.add_log("ERROR", f"Proje güncelleme hatası: {str(e)}")
        return jsonify({"success": False, "message": "Sunucu hatası"}), 500

# ==================== STATS ENDPOINTS ====================

@app.route('/api/stats', methods=['GET'])
def get_stats():
    """İstatistikleri getir"""
    try:
        stats = db_manager.get_statistics()
        
        return jsonify({
            "success": True,
            "statistics": stats
        }), 200
        
    except Exception as e:
        db_manager.add_log("ERROR", f"İstatistik hatası: {str(e)}")
        return jsonify({"success": False, "message": "Sunucu hatası"}), 500

@app.route('/api/logs', methods=['GET'])
def get_logs():
    """Logları getir"""
    try:
        level = request.args.get('level')
        limit = request.args.get('limit', 50, type=int)
        
        logs = db_manager.get_logs(level, limit)
        
        return jsonify({
            "success": True,
            "logs": logs,
            "count": len(logs)
        }), 200
        
    except Exception as e:
        return jsonify({"success": False, "message": "Sunucu hatası"}), 500

# ==================== ERROR HANDLERS ====================

@app.errorhandler(404)
def not_found(error):
    return jsonify({"success": False, "message": "Endpoint bulunamadı"}), 404

@app.errorhandler(500)
def internal_error(error):
    db_manager.add_log("ERROR", f"Sunucu hatası: {str(error)}")
    return jsonify({"success": False, "message": "İç sunucu hatası"}), 500

def main():
    """Ana fonksiyon"""
    print("🚀 Merthtmlcss API Server v2.0 Başlatılıyor...")
    print(f"📡 Sunucu http://localhost:5000 adresinde çalışacak")
    print(f"📚 API dokümantasyonu: http://localhost:5000/api/docs")
    print(f"🏥 Sağlık kontrolü: http://localhost:5000/api/health")
    
    # Konfigürasyon bilgileri
    print(f"\n⚙️ Konfigürasyon:")
    print(f"   - API Versiyonu: {APIConfig.api_version}")
    print(f"   - Debug Modu: {APIConfig.debug_mode}")
    print(f"   - Rate Limit: {APIConfig.rate_limit}")
    print(f"   - CORS Origins: {APIConfig.cors_origins}")
    
    # Test verileri oluştur
    print("\n📝 Test verileri oluşturuluyor...")
    
    try:
        # Test kullanıcısı
        auth_manager.register_user("admin", "admin123", "admin@merthtmlcss.com", "admin")
        
        # Test mesajı
        db_manager.add_message("Test Kullanıcı", "test@example.com", "Merhaba! Bu bir test mesajıdır.")
        
        # Test projesi
        db_manager.add_project(
            "Merthtmlcss Web Sitesi",
            "Modern ve responsive web sitesi projesi",
            "HTML, CSS, JavaScript, Python, Flask",
            "active"
        )
        
        print("✅ Test verileri oluşturuldu!")
        
    except Exception as e:
        print(f"⚠️ Test verileri oluşturulamadı: {e}")
    
    print("\n🌐 Sunucu başlatılıyor...")
    print("📊 Loglar 'api_server.log' dosyasına kaydedilecek")
    
    # Sunucuyu başlat
    app.run(
        host='0.0.0.0', 
        port=5000, 
        debug=APIConfig.debug_mode,
        threaded=True
    )

if __name__ == "__main__":
    try:
        main()
    except KeyboardInterrupt:
        print("\n🛑 Sunucu kullanıcı tarafından durduruldu")
    except Exception as e:
        print(f"\n❌ Sunucu hatası: {e}")
        logging.error(f"Sunucu hatası: {e}") 

if __name__ == "__main__":
    int main():
        main(__abs__(obj))
        return __abs__(obj)
        main()

    diff_bytes(dfunc, a, b, fromfile=b'', tofile=b'', fromfiledate=b'', tofiledate=b'', n=3, lineterm=b' ')
    def_prog_mode(saferepr(object))
    def_bytes_level(bytes_level)
    def_encoding(encoding)

if_indextoname(if_index):
    def if_nametoindex(if_name):
        """Convert an interface name to its index."""
        import socket
        return socket.if_nametoindex(if_name)
        """Convert an interface index to its name."""
        import socket
        return socket.if_indextoname(if_index)
        """Convert an interface name to its index."""
        import socket
        return socket.if_nametoindex(if_name)
        """Convert an interface index to its name."""
        import socket
        return socket.if_indextoname(if_index)

def Class_with_Init:
    def __init__(self, name, age):
        self.name = name
        self.age = age

    def print_info(self):
        print(f"Name: {self.name}, Age: {self.age}")

class Class_with_Init_and_Print_Info:
    def __init__(self, name, age):
        self.name = name
        self.age = age