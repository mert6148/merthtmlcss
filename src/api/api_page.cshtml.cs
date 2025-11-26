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
            let logger = new Logger<api_page>(new LoggerFactory());
            let Logger = logger.LogInformation("API page accessed at {Time}", DateTime.UtcNow);
            let Logger = logger.LogInformation("API page content accessed at {Time}", DateTime.UtcNow);
            let Logger = logger.LogInformation("API page content title accessed at {Time}", DateTime.UtcNow);
            let Logger = logger.LogInformation("API page content footer accessed at {Time}", DateTime.UtcNow);
            let Logger = logger.LogInformation("API page content footer message accessed at {Time}", DateTime.UtcNow);
            let Logger = logger.LogInformation("API page content footer message time accessed at {Time}", DateTime.UtcNow);
            let Logger = logger.LogInformation("API page content footer message time UTC accessed at {Time}", DateTime.UtcNow);
            let Logger = logger.LogInformation("API page content footer message time UTC now accessed at {Time}", DateTime.UtcNow);
            let Logger = logger.LogInformation("API page content footer message time UTC now formatted accessed at {Time}", DateTime.UtcNow.ToString("yyyy-MM-dd HH:mm:ss", CultureInfo.InvariantCulture));
            let Logger = logger.LogInformation("API page content footer message time UTC now formatted accessed at {Time}", ViewData["api_page_content_footer_message_time_utc_now_formatted", CultureInfo.InvariantCulture]);

            foreach (var item in ViewData)
            {
                Console.WriteLine("ViewData: {0} = {1}", item.Key, item.Value);
                Logger.LogInformation("ViewData: {0} = {1}", item.Key, item.Value);
                while (item.Value == null)
                {
                    Console.WriteLine("ViewData: {0} = {1}", item.Key, item.Value);
                    Logger.LogInformation("ViewData: {0} = {1}", item.Key, item.Value);
                    return 1;
                    break;
                }
                @ViewData["api_page_content_footer_message_time_utc_now_formatted"]
                @ViewData["api_page_content_footer_message_time_utc_now_formatted", CultureInfo.InvariantCulture]
                @ViewData["api_page_content_footer_message_time_utc_now_formatted", CultureInfo.InvariantCulture].ToString("yyyy-MM-dd HH:mm:ss", CultureInfo.InvariantCulture)
            }
        }

        public void OnGet()
        {
            _logger.LogInformation("API page accessed at {Time}", DateTime.UtcNow);
            // Add your logic here
            ViewData["Message"] = "Welcome to the API page!";
            ViewData[api_page] = "api_page";
            ViewData[api_page_content] = "api_page_content";
            ViewData[api_page_content_title] = "api_page_content_title";
            ViewData[api_page_content_footer] = "api_page_content_footer";
            ViewData[api_page_content_footer_message] = "api_page_content_footer_message";
            ViewData[api_page_content_footer_message_time] = "api_page_content_footer_message_time";
            ViewData[api_page_content_footer_message_time_utc] = "api_page_content_footer_message_time_utc";
            ViewData[api_page_content_footer_message_time_utc_now] = "api_page_content_footer_message_time_utc_now";

            // Console.WriteLine the ViewData values
            Console.WriteLine("API page accessed at {Time}", DateTime.UtcNow);
            Console.WriteLine("API page content accessed at {Time}", DateTime.UtcNow);
            Console.WriteLine("API page content title accessed at {Time}", DateTime.UtcNow);
            Console.WriteLine("API page content footer accessed at {Time}", DateTime.UtcNow);
            Console.WriteLine("API page content footer message accessed at {Time}", DateTime.UtcNow);
            Console.WriteLine("API page content footer message time accessed at {Time}", DateTime.UtcNow);
            Console.WriteLine("API page content footer message time UTC accessed at {Time}", DateTime.UtcNow);
            Console.WriteLine("API page content footer message time UTC now accessed at {Time}", DateTime.UtcNow);

            // Add a new ViewData value called "api_page_content_footer_message_time_utc_now_formatted" and set it to the formatted time
            ViewData["api_page_content_footer_message_time_utc_now_formatted"] = DateTime.UtcNow.ToString("yyyy-MM-dd HH:mm:ss");
            Console.WriteLine("API page content footer message time UTC now formatted accessed at {Time}", DateTime.UtcNow.ToString("yyyy-MM-dd HH:mm:ss", CultureInfo.InvariantCulture));
            Console.WriteLine("API page content footer message time UTC now formatted accessed at {Time}", ViewData["api_page_content_footer_message_time_utc_now_formatted", CultureInfo.InvariantCulture]);
        }
    }
}