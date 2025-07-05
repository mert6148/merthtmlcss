#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Merthtmlcss API Server
RESTful API sunucusu
"""

from flask import Flask, request, jsonify
from flask_cors import CORS
import json
import datetime
import os
from typing import Dict, Any

# Kendi modüllerimizi import ediyoruz
from auth_manager import AuthManager
from database_manager import DatabaseManager

app = Flask(__name__)
CORS(app)  # Cross-origin isteklerine izin ver

# Global nesneler
auth_manager = AuthManager()
db_manager = DatabaseManager()

@app.route('/')
def home():
    """Ana sayfa"""
    return jsonify({
        "message": "Merthtmlcss API Server",
        "version": "1.0.0",
        "endpoints": {
            "auth": "/api/auth",
            "users": "/api/users",
            "messages": "/api/messages",
            "projects": "/api/projects",
            "stats": "/api/stats"
        }
    })

# ==================== AUTH ENDPOINTS ====================

@app.route('/api/auth/register', methods=['POST'])
def register():
    """Kullanıcı kaydı"""
    try:
        data = request.get_json()
        username = data.get('username')
        password = data.get('password')
        email = data.get('email')
        role = data.get('role', 'user')
        
        if not all([username, password, email]):
            return jsonify({"success": False, "message": "Tüm alanlar gerekli"}), 400
        
        result = auth_manager.register_user(username, password, email, role)
        
        if result["success"]:
            # Veritabanına da ekle
            db_manager.add_user(username, email, auth_manager.hash_password(password), role)
            db_manager.add_log("INFO", f"Yeni kullanıcı kaydı: {username}")
        
        return jsonify(result), 200 if result["success"] else 400
        
    except Exception as e:
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
        db_manager.add_log("ERROR", f"Session doğrulama hatası: {str(e)}")
        return jsonify({"success": False, "message": "Sunucu hatası"}), 500

# ==================== USER ENDPOINTS ====================

@app.route('/api/users', methods=['GET'])
def get_users():
    """Tüm kullanıcıları getir"""
    try:
        users = auth_manager.list_users()
        return jsonify({
            "success": True,
            "users": users,
            "count": len(users)
        }), 200
        
    except Exception as e:
        db_manager.add_log("ERROR", f"Kullanıcı listesi hatası: {str(e)}")
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
        projects = db_manager.get_projects(status)
        
        return jsonify({
            "success": True,
            "projects": projects,
            "count": len(projects)
        }), 200
        
    except Exception as e:
        db_manager.add_log("ERROR", f"Proje listesi hatası: {str(e)}")
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
    print("🚀 Merthtmlcss API Server Başlatılıyor...")
    print("📡 Sunucu http://localhost:5000 adresinde çalışacak")
    print("📚 API dokümantasyonu: http://localhost:5000")
    
    # Test verileri oluştur
    print("\n📝 Test verileri oluşturuluyor...")
    
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
    print("\n🌐 Sunucu başlatılıyor...")
    
    # Sunucuyu başlat
    app.run(host='0.0.0.0', port=5000, debug=True)

if __name__ == "__main__":
    main() 