using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;

namespace auth
{
    public class NewClass
    {
        public string Name { get; set; }
        public string Email { get; set; }
        public string Message { get; set; }
        public string Title { get; set; }
        public string Description { get; set; }
        public string Author { get; set; }
        public string Version { get; set; }
        public string Homepage { get; set; }
        public string Api { get; set; }
        public string Social { get; set; }
        public string Html { get; set; }

        public NewClass(string name, string email, string message,
                        string title = "Merthtmlcss",
                        string description = "Merthtmlcss projesi harika!",
                        string author = "Mert Doğanay",
                        string version = "1.0.0",
                        string homepage = "https://merthtmlcss.com",
                        string api = "https://merthtmlcss.com/api",
                        string social = "https://merthtmlcss.com/social",
                        string html = "<b>Merthtmlcss projesi harika!</b>")
        {
            Name = name;
            Email = email;
            Message = message;
            Title = title;
            Description = description;
            Author = author;
            Version = version;
            Homepage = homepage;
            Api = api;
            Social = social;
            Html = html;
        }

        public string GetName() => Name;
        public string GetEmail() => Email;
        public string GetMessage() => Message;
        public string GetTitle() => Title;
        public string GetDescription() => Description;
        public string GetAuthor() => Author;
        public string GetVersion() => Version;
        public string GetHomepage() => Homepage;
        public string GetApi() => Api;
        public string GetSocial() => Social;
        public string GetHtml() => Html;
    }
}