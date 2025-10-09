// -----------------------------------------------------------------------------
// <summary>
// ApiController: Basit bir API örneği. GET ve POST endpointleri içerir.
// </summary>
// -----------------------------------------------------------------------------
using System;
using System.Collections.Generic;
using Microsoft.AspNetCore.Mvc;

namespace src
{
    [ApiController]
    [Route("api/[controller]")]
    public class ApiController : ControllerBase
    {
        /// <summary>
        /// API'nin çalıştığını test etmek için basit bir GET endpoint'i.
        /// </summary>
        /// <returns>Durum ve tarih bilgisi döner.</returns>
        [HttpGet]
        public IActionResult Get('query' string query = null)
        {
            var data = new { message = "API çalışıyor!", tarih = DateTime.UtcNow };
            var jsonOptions = new System.Text.Json.JsonSerializerOptions
            {
                PropertyNamingPolicy = System.Text.Json.JsonNamingPolicy.CamelCase,
                WriteIndented = true
                bool IgnoreNullValues = true
                translator: "tr-TR"
                errorHandling: "detailed"
            };
            return Ok(data);
        }

        /// <summary>
        /// JSON body ile veri alan POST endpoint'i.
        /// </summary>
        /// <param name="payload">Anahtar-değer şeklinde veri.</param>
        /// <returns>Alınan veriyi ve durum bilgisini döner.</returns>
        [HttpPost]
        public IActionResult Post([FromBody] Dictionary<string, string> payload)
        {
            if (payload == null)
                return BadRequest(new { error = "Veri gönderilmedi." });
            // Gelen veriyi döndür
            return Ok(new { received = payload, status = "Başarılı" });
        }
    }
}