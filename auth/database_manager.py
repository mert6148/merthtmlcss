#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Merthtmlcss Database Manager
Veritabanı yönetimi ve işlemleri
"""

import sqlite3
import json
import datetime
import os
from typing import Dict, List, Optional, Any

class DatabaseManager:
    """Veritabanı yöneticisi"""
    
    def __init__(self, db_file: str = "merthtmlcss.db"):
        self.db_file = db_file
        self.init_database()
    
    def init_database(self) -> None:
        """Veritabanını başlat ve tabloları oluştur"""
        with sqlite3.connect(self.db_file) as conn:
            cursor = conn.cursor()
            
            # Kullanıcılar tablosu
            cursor.execute('''
                CREATE TABLE IF NOT EXISTS users (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    username TEXT UNIQUE NOT NULL,
                    email TEXT UNIQUE NOT NULL,
                    password_hash TEXT NOT NULL,
                    role TEXT DEFAULT 'user',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    last_login TIMESTAMP,
                    is_active BOOLEAN DEFAULT 1
                )
            ''')
            
            # Mesajlar tablosu
            cursor.execute('''
                CREATE TABLE IF NOT EXISTS messages (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name TEXT NOT NULL,
                    email TEXT NOT NULL,
                    message TEXT NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    is_read BOOLEAN DEFAULT 0
                )
            ''')
            
            # Projeler tablosu
            cursor.execute('''
                CREATE TABLE IF NOT EXISTS projects (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    title TEXT NOT NULL,
                    description TEXT,
                    technologies TEXT,
                    status TEXT DEFAULT 'active',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ''')
            
            # Loglar tablosu
            cursor.execute('''
                CREATE TABLE IF NOT EXISTS logs (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    level TEXT NOT NULL,
                    message TEXT NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ''')
            
            conn.commit()
    
    def add_user(self, username: str, email: str, password_hash: str, role: str = "user") -> Dict:
        """Kullanıcı ekle"""
        try:
            with sqlite3.connect(self.db_file) as conn:
                cursor = conn.cursor()
                cursor.execute('''
                    INSERT INTO users (username, email, password_hash, role)
                    VALUES (?, ?, ?, ?)
                ''', (username, email, password_hash, role))
                conn.commit()
                
                return {"success": True, "message": "Kullanıcı başarıyla eklendi", "user_id": cursor.lastrowid}
        except sqlite3.IntegrityError:
            return {"success": False, "message": "Kullanıcı adı veya email zaten mevcut"}
        except Exception as e:
            return {"success": False, "message": f"Veritabanı hatası: {str(e)}"}
    
    def get_user_by_username(self, username: str) -> Optional[Dict]:
        """Kullanıcı adına göre kullanıcı getir"""
        with sqlite3.connect(self.db_file) as conn:
            cursor = conn.cursor()
            cursor.execute('SELECT * FROM users WHERE username = ?', (username,))
            row = cursor.fetchone()
            
            if row:
                columns = [description[0] for description in cursor.description]
                return dict(zip(columns, row))
            return None
    
    def update_user_login(self, username: str) -> None:
        """Kullanıcının son giriş zamanını güncelle"""
        with sqlite3.connect(self.db_file) as conn:
            cursor = conn.cursor()
            cursor.execute('''
                UPDATE users SET last_login = CURRENT_TIMESTAMP 
                WHERE username = ?
            ''', (username,))
            conn.commit()
    
    def add_message(self, name: str, email: str, message: str) -> Dict:
        """Mesaj ekle"""
        try:
            with sqlite3.connect(self.db_file) as conn:
                cursor = conn.cursor()
                cursor.execute('''
                    INSERT INTO messages (name, email, message)
                    VALUES (?, ?, ?)
                ''', (name, email, message))
                conn.commit()
                
                return {"success": True, "message": "Mesaj başarıyla kaydedildi", "message_id": cursor.lastrowid}
        except Exception as e:
            return {"success": False, "message": f"Veritabanı hatası: {str(e)}"}
    
    def get_messages(self, limit: int = 10, offset: int = 0) -> List[Dict]:
        """Mesajları getir"""
        with sqlite3.connect(self.db_file) as conn:
            cursor = conn.cursor()
            cursor.execute('''
                SELECT * FROM messages 
                ORDER BY created_at DESC 
                LIMIT ? OFFSET ?
            ''', (limit, offset))
            
            columns = [description[0] for description in cursor.description]
            return [dict(zip(columns, row)) for row in cursor.fetchall()]
    
    def mark_message_read(self, message_id: int) -> Dict:
        """Mesajı okundu olarak işaretle"""
        try:
            with sqlite3.connect(self.db_file) as conn:
                cursor = conn.cursor()
                cursor.execute('''
                    UPDATE messages SET is_read = 1 
                    WHERE id = ?
                ''', (message_id,))
                conn.commit()
                
                return {"success": True, "message": "Mesaj okundu olarak işaretlendi"}
        except Exception as e:
            return {"success": False, "message": f"Veritabanı hatası: {str(e)}"}
    
    def add_project(self, title: str, description: str, technologies: str, status: str = "active") -> Dict:
        """Proje ekle"""
        try:
            with sqlite3.connect(self.db_file) as conn:
                cursor = conn.cursor()
                cursor.execute('''
                    INSERT INTO projects (title, description, technologies, status)
                    VALUES (?, ?, ?, ?)
                ''', (title, description, technologies, status))
                conn.commit()
                
                return {"success": True, "message": "Proje başarıyla eklendi", "project_id": cursor.lastrowid}
        except Exception as e:
            return {"success": False, "message": f"Veritabanı hatası: {str(e)}"}
    
    def get_projects(self, status: str = None) -> List[Dict]:
        """Projeleri getir"""
        with sqlite3.connect(self.db_file) as conn:
            cursor = conn.cursor()
            
            if status:
                cursor.execute('SELECT * FROM projects WHERE status = ? ORDER BY created_at DESC', (status,))
            else:
                cursor.execute('SELECT * FROM projects ORDER BY created_at DESC')
            
            columns = [description[0] for description in cursor.description]
            return [dict(zip(columns, row)) for row in cursor.fetchall()]
    
    def update_project(self, project_id: int, **kwargs) -> Dict:
        """Proje güncelle"""
        try:
            with sqlite3.connect(self.db_file) as conn:
                cursor = conn.cursor()
                
                # Güncellenebilir alanlar
                allowed_fields = ['title', 'description', 'technologies', 'status']
                update_fields = []
                values = []
                
                for field, value in kwargs.items():
                    if field in allowed_fields:
                        update_fields.append(f"{field} = ?")
                        values.append(value)
                
                if not update_fields:
                    return {"success": False, "message": "Güncellenebilir alan bulunamadı"}
                
                update_fields.append("updated_at = CURRENT_TIMESTAMP")
                values.append(project_id)
                
                query = f"UPDATE projects SET {', '.join(update_fields)} WHERE id = ?"
                cursor.execute(query, values)
                conn.commit()
                
                return {"success": True, "message": "Proje başarıyla güncellendi"}
        except Exception as e:
            return {"success": False, "message": f"Veritabanı hatası: {str(e)}"}
    
    def add_log(self, level: str, message: str) -> None:
        """Log ekle"""
        with sqlite3.connect(self.db_file) as conn:
            cursor = conn.cursor()
            cursor.execute('''
                INSERT INTO logs (level, message)
                VALUES (?, ?)
            ''', (level, message))
            conn.commit()
    
    def get_logs(self, level: str = None, limit: int = 50) -> List[Dict]:
        """Logları getir"""
        with sqlite3.connect(self.db_file) as conn:
            cursor = conn.cursor()
            
            if level:
                cursor.execute('''
                    SELECT * FROM logs WHERE level = ? 
                    ORDER BY created_at DESC LIMIT ?
                ''', (level, limit))
            else:
                cursor.execute('''
                    SELECT * FROM logs 
                    ORDER BY created_at DESC LIMIT ?
                ''', (limit,))
            
            columns = [description[0] for description in cursor.description]
            return [dict(zip(columns, row)) for row in cursor.fetchall()]
    
    def get_statistics(self) -> Dict:
        """İstatistikleri getir"""
        with sqlite3.connect(self.db_file) as conn:
            cursor = conn.cursor()
            
            stats = {}
            
            # Kullanıcı sayısı
            cursor.execute('SELECT COUNT(*) FROM users')
            stats['total_users'] = cursor.fetchone()[0]
            
            # Aktif kullanıcı sayısı
            cursor.execute('SELECT COUNT(*) FROM users WHERE is_active = 1')
            stats['active_users'] = cursor.fetchone()[0]
            
            # Toplam mesaj sayısı
            cursor.execute('SELECT COUNT(*) FROM messages')
            stats['total_messages'] = cursor.fetchone()[0]
            
            # Okunmamış mesaj sayısı
            cursor.execute('SELECT COUNT(*) FROM messages WHERE is_read = 0')
            stats['unread_messages'] = cursor.fetchone()[0]
            
            # Toplam proje sayısı
            cursor.execute('SELECT COUNT(*) FROM projects')
            stats['total_projects'] = cursor.fetchone()[0]
            
            # Aktif proje sayısı
            cursor.execute('SELECT COUNT(*) FROM projects WHERE status = "active"')
            stats['active_projects'] = cursor.fetchone()[0]
            
            return stats
    
    def backup_database(self, backup_file: str = None) -> Dict:
        """Veritabanı yedeği al"""
        if not backup_file:
            timestamp = datetime.datetime.now().strftime("%Y%m%d_%H%M%S")
            backup_file = f"backup_{timestamp}.db"
        
        try:
            with sqlite3.connect(self.db_file) as source_conn:
                with sqlite3.connect(backup_file) as backup_conn:
                    source_conn.backup(backup_conn)
            
            return {"success": True, "message": f"Yedek başarıyla oluşturuldu: {backup_file}"}
        except Exception as e:
            return {"success": False, "message": f"Yedekleme hatası: {str(e)}"}

def main():
    """Test fonksiyonu"""
    print("🗄️ Merthtmlcss Database Manager Başlatılıyor...")
    
    db = DatabaseManager()
    
    # Test kullanıcısı ekle
    print("\n👤 Test kullanıcısı ekleniyor...")
    result = db.add_user("testuser", "test@example.com", "hashed_password_123", "user")
    print(f"Kullanıcı ekleme: {result['message']}")
    
    # Test mesajı ekle
    print("\n💬 Test mesajı ekleniyor...")
    msg_result = db.add_message("Test Kullanıcı", "test@example.com", "Bu bir test mesajıdır.")
    print(f"Mesaj ekleme: {msg_result['message']}")
    
    # Test projesi ekle
    print("\n📁 Test projesi ekleniyor...")
    proj_result = db.add_project(
        "Test Projesi", 
        "Bu bir test projesidir", 
        "Python, SQLite, HTML", 
        "active"
    )
    print(f"Proje ekleme: {proj_result['message']}")
    
    # İstatistikleri göster
    print("\n📊 Veritabanı istatistikleri:")
    stats = db.get_statistics()
    for key, value in stats.items():
        print(f"- {key}: {value}")
    
    # Mesajları listele
    print("\n📨 Son mesajlar:")
    messages = db.get_messages(limit=5)
    for msg in messages:
        print(f"- {msg['name']}: {msg['message'][:50]}...")
    
    # Projeleri listele
    print("\n📂 Projeler:")
    projects = db.get_projects()
    for proj in projects:
        print(f"- {proj['title']}: {proj['status']}")
    
    print("\n✅ Database Manager testi tamamlandı!")

if __name__ == "__main__":
    main() 