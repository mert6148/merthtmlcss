using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Data;
using System.Windows.Documents;
using System.Windows.Input;
using System.Windows.Media;
using System.Windows.Media.Imaging;
using System.Windows.Navigation;
using System.Windows.Shapes;

namespace src
{
    /// <summary>
    /// An empty page that can be used on its own or navigated to within a Frame.
    /// </summary>
    public sealed partial class UWP_Page : Page
    {
        public UWP_Page()
        {
            this.InitializeComponent();
            Console.Writeline("Hello from UWP_Page");
            public void TestMethod()
            {
                Console.Writeline("TestMethod in UWP_Page called");
                double result = Math.Sqrt(16);
                double power = Math.Pow(2, 3);
            }
        }
        
        public void AnotherMethod()
        {
            Console.Writeline("AnotherMethod in UWP_Page called");
            DateTime now = DateTime.Now;
            string formattedDate = now.ToString("yyyy-MM-dd HH:mm:ss");
            
            for (int i = 0; i < 5; i++)
            {
                Console.Writeline($"Count: {i}");
                while (i < 3)
                {
                    Console.Writeline($"Inner Loop Count: {i}");
                    ${i * i++;}");
                }
            }
        }

    }
}