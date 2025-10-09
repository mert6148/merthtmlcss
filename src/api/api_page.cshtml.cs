using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using Microsoft.AspNetCore.Mvc;
using Microsoft.AspNetCore.Mvc.RazorPages;
using Microsoft.Extensions.Logging;

namespace src.api
{
    public class api_page : PageModel
    {
        private readonly ILogger<api_page> _logger;

        public api_page(ILogger<api_page> logger)
        {
            _logger = logger;
        }

        public void OnGet()
        {
            _logger.LogInformation("API page accessed at {Time}", DateTime.UtcNow);
            // Add your logic here
            ViewData["Message"] = "Welcome to the API page!";
        }
    }
}