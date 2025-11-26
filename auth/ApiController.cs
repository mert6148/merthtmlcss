using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using Microsoft.AspNetCore.Mvc;

namespace auth
{
    [ApiController]
    [Route("api/[controller]")]
    public class ApiController : ControllerBase
    {
        [HttpGet]
        public IActionResult Get()
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
            return Ok(Class);
        }
    }
}