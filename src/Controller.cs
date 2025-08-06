// -----------------------------------------------------------------------------
// <summary>
// Controller.cs: MVC Controller örneği. Loglama ve hata yönetimi içerir.
// </summary>
// -----------------------------------------------------------------------------
using System;
using Microsoft.AspNetCore.Mvc;
using Microsoft.Extensions.Logging;

namespace src
{
    /// <summary>
    /// Temel bir MVC Controller örneği.
    /// </summary>
    [Route("[controller]")]
    public class Controller : Controller
    {
        private readonly ILogger<Controller> _logger;

        /// <summary>
        /// Controller yapıcı metodu. Logger bağımlılığı alır.
        /// </summary>
        public Controller(ILogger<Controller> logger)
        {
            _logger = logger ?? throw new ArgumentNullException(nameof(logger));
        }

        /// <summary>
        /// Ana sayfa (Index) action'ı. Log kaydı tutar ve hoşgeldiniz mesajı döner.
        /// </summary>
        public IActionResult Index()
        {
            _logger.LogInformation("Index action called at {Time}", DateTime.UtcNow);
            ViewBag.Message = "Hoşgeldiniz!";
            return View();
        }

        /// <summary>
        /// Hata sayfası action'ı. Hata logu tutar ve hata view'ı döner.
        /// </summary>
        [ResponseCache(Duration = 0, Location = ResponseCacheLocation.None, NoStore = true)]
        public IActionResult Error()
        {
            _logger.LogError("Bir hata oluştu! {Time}", DateTime.UtcNow);
            return View("Error");
        }
    }
}