using Microsoft.AspNetCore.Mvc;
using System.Collections.Generic;
using System.Linq;
using System; // Added for Guid.NewGuid()

namespace Forge.Controllers
{
    // Kullanıcı modelini tanımla
    public class User
    {
        public int Id { get; set; }
        public string Name { get; set; }
        public string Email { get; set; }
        public string Password { get; set; } // Şifre alanı eklendi
    }

    // Login için istek modeli
    public class LoginRequest
    {
        public string Email { get; set; }
        public string Password { get; set; }
    }

    [ApiController]
    [Route("api/[controller]")]
    public class ApiControllerPhp : ControllerBase
    {
        // In-memory kullanıcı listesi (örnek veri deposu)
        private static List<User> users = new List<User>
        {
            new User { Id = 1, Name = "Ali Veli", Email = "ali@example.com", Password = "123456" },
            new User { Id = 2, Name = "Ayşe Yılmaz", Email = "ayse@example.com", Password = "654321" }
        };

        // TÜM KULLANICILARI GETİR
        [HttpGet]
        public IActionResult GetAll()
        {
            return Ok(users);
        }

        // ID'YE GÖRE KULLANICI GETİR
        [HttpGet("{id}")]
        public IActionResult GetById(int id)
        {
            var user = users.FirstOrDefault(u => u.Id == id);
            if (user == null)
                return NotFound(new { message = "Kullanıcı bulunamadı." });
            return Ok(user);
        }

        // YENİ KULLANICI EKLE
        [HttpPost]
        public IActionResult Create([FromBody] User user)
        {
            if (user == null || string.IsNullOrWhiteSpace(user.Name) || string.IsNullOrWhiteSpace(user.Email))
                return BadRequest(new { message = "Geçersiz kullanıcı verisi." });
            user.Id = users.Count > 0 ? users.Max(u => u.Id) + 1 : 1;
            users.Add(user);
            return CreatedAtAction(nameof(GetById), new { id = user.Id }, user);
        }

        // KULLANICI GÜNCELLE
        [HttpPut("{id}")]
        public IActionResult Update(int id, [FromBody] User updatedUser)
        {
            var user = users.FirstOrDefault(u => u.Id == id);
            if (user == null)
                return NotFound(new { message = "Kullanıcı bulunamadı." });
            if (updatedUser == null || string.IsNullOrWhiteSpace(updatedUser.Name) || string.IsNullOrWhiteSpace(updatedUser.Email))
                return BadRequest(new { message = "Geçersiz kullanıcı verisi." });
            user.Name = updatedUser.Name;
            user.Email = updatedUser.Email;
            return Ok(user);
        }

        // KULLANICI SİL
        [HttpDelete("{id}")]
        public IActionResult Delete(int id)
        {
            var user = users.FirstOrDefault(u => u.Id == id);
            if (user == null)
                return NotFound(new { message = "Kullanıcı bulunamadı." });
            users.Remove(user);
            return Ok(new { message = "Kullanıcı silindi." });
        }

        // KULLANICI GİRİŞİ (LOGIN)
        [HttpPost("login")]
        public IActionResult Login([FromBody] LoginRequest request)
        {
            if (request == null || string.IsNullOrWhiteSpace(request.Email) || string.IsNullOrWhiteSpace(request.Password))
                return BadRequest(new { message = "E-posta ve şifre gereklidir." });
            var user = users.FirstOrDefault(u => u.Email == request.Email && u.Password == request.Password);
            if (user == null)
                return Unauthorized(new { message = "Geçersiz e-posta veya şifre." });
            // Örnek token (gerçek uygulamada JWT vb. kullanılmalı)
            var token = Convert.ToBase64String(Guid.NewGuid().ToByteArray());
            return Ok(new { user = new { user.Id, user.Name, user.Email }, token });
        }
    }
}