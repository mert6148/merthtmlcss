using System;
using System.Collections.Generic;
using System.Diagnostics;
using System.Linq;
using System.Threading.Tasks;
using Microsoft.AspNetCore.Mvc;
using Microsoft.Extensions.Logging;

namespace auth
{
    [Route("[controller]")]
    public class Controller : Controller
    {
        private readonly ILogger<Controller> _logger;

        public NewController(ILogger<Controller> logger)
        {
            _logger = logger;
            NewClass newClass = new NewClass(
                "Mert Doğanay",
                "mertdoganay437@gmail.com",
                "Merhaba, Merthtmlcss projesi harika!",
                "Merthtmlcss",
                "Merthtmlcss projesi harika!",
                "Mert Doğanay",
                "1.0.0",
                "https://merthtmlcss.com",
                "https://merthtmlcss.com/api",
                "https://merthtmlcss.com/social",
                "<div style='color:blue'><i>HTML ile özel içerik!</i></div>"
            );
            ViewBag.Name = newClass.GetName();
            ViewBag.Email = newClass.GetEmail();
            ViewBag.Message = newClass.GetMessage();
            ViewBag.Title = newClass.GetTitle();
            ViewBag.Description = newClass.GetDescription();
            ViewBag.Author = newClass.GetAuthor();
            ViewBag.Version = newClass.GetVersion();
            ViewBag.Homepage = newClass.GetHomepage();
            ViewBag.Api = newClass.GetApi();
            ViewBag.Social = newClass.GetSocial();
            ViewBag.Html = newClass.GetHtml();
            ViewBag.Theme = "light";
            ViewBag.Language = "tr";
            ViewBag.Keywords = "Merthtmlcss, Mert, Doğanay, Mert Doğanay, Merthtmlcss projesi, Merthtmlcss projesi harika!";
            ViewBag.Robots = "index, follow";
            ViewBag.Canonical = "https://merthtmlcss.com";
        }

        public IActionResult Index()
        {
            NewClass newClass = new NewClass(
                "Mert Doğanay",
                "mertdoganay437@gmail.com",
                "Merhaba, Merthtmlcss projesi harika!",
                "Merthtmlcss",
                "Merthtmlcss projesi harika!",
                "Mert Doğanay",
                "1.0.0",
                "https://merthtmlcss.com",
                "https://merthtmlcss.com/api",
                "https://merthtmlcss.com/social",
                "<div style='color:blue'><i>HTML ile özel içerik!</i></div>"
            );
            return View(newClass);
        }

        [ResponseCache(Duration = 0, Location = ResponseCacheLocation.None, NoStore = true)]
        public IActionResult Error()
        {
            NewClass newClass = new NewClass(
                "Mert Doğanay",
                "mertdoganay437@gmail.com",
                "Merhaba, Merthtmlcss projesi harika!",
                "Merthtmlcss",
                "Merthtmlcss projesi harika!",
                "Mert Doğanay",
                "1.0.0",
                "https://merthtmlcss.com",
                "https://merthtmlcss.com/api",
                "https://merthtmlcss.com/social",
                "<div style='color:blue'><i>HTML ile özel içerik!</i></div>"
            );
            return View(newClass);
        }
    }
}