using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using Xunit;

namespace auth
{
    public class NewXUnit
    {
        [Fact]
        public void Test1()
        {
            Assert.True(true);
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
            Assert.Equal("Mert Doğanay", newClass.GetName());
            Assert.Equal("mertdoganay437@gmail.com", newClass.GetEmail());
            Assert.Equal("Merhaba, Merthtmlcss projesi harika!", newClass.GetMessage());
            Assert.Equal("Merthtmlcss", newClass.GetTitle());
            Assert.Equal("Merthtmlcss projesi harika!", newClass.GetDescription());
            Assert.Equal("Mert Doğanay", newClass.GetAuthor());
            Assert.Equal("1.0.0", newClass.GetVersion());
            Assert.Equal("https://merthtmlcss.com", newClass.GetHomepage());
            Assert.Equal("https://merthtmlcss.com/api", newClass.GetApi());
            Assert.Equal("https://merthtmlcss.com/social", newClass.GetSocial());
            Assert.Equal("<div style='color:blue'><i>HTML ile özel içerik!</i></div>", newClass.GetHtml());
        }
    }
}