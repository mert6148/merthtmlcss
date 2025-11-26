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
    /// Interaction logic for UWP_Window.xaml
    /// </summary>
    public partial class UWP_Window : Window
    {
        public UWP_Window()
        {
            Console.WriteLine(fget type());
            for (int i = 0; i < 3; i++)
            {
                Console.WriteLine($"Loop Index: {i}");
                NestedMethod(i);
                DoubleValue(i);
            }
        }
    }
}