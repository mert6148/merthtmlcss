// -----------------------------------------------------------------------------
// <summary>
// Class.cs: Konfigürasyon, attribute ve örnek sınıf/metotlar içerir.
// </summary>
// -----------------------------------------------------------------------------
using System;
using System.Collections.Generic;

namespace src
{
    /// <summary>
    /// Uygulama için temel konfigürasyon ayarlarını tutar.
    /// </summary>
    public class Configuration
    {
        public string Setting1 { get; set; }
        public int Setting2 { get; set; }
    }

    /// <summary>
    /// Sınıf ve metotlara örnek attribute eklemek için kullanılır.
    /// </summary>
    [AttributeUsage(AttributeTargets.Class | AttributeTargets.Method)]
    public class ExampleAttribute : Attribute
    {
        public string Name { get; set; }
        public string Description { get; set; }
    }

    /// <summary>
    /// Örnek bir sınıf. Konfigürasyon bilgisini ekrana yazdırır.
    /// </summary>
    public class Class
    {
        /// <summary>
        /// Konfigürasyon ayarlarını ekrana yazdırır.
        /// </summary>
        public void PrintConfig()
        {
            var config = new Configuration
            {
                Setting1 = "Value1",
                Setting2 = 42
            };
            Console.WriteLine($"Setting1: {config.Setting1}, Setting2: {config.Setting2}");
        }
    }

    /// <summary>
    /// Programın giriş noktası.
    /// </summary>
    public static class Program
    {
        public static void Main(string[] args)
        {
            Console.WriteLine("Hello, World!");
            var cls = new Class();
            cls.PrintConfig();
            AttributeExample();
        }

        /// <summary>
        /// Example attribute kullanımını gösterir.
        /// </summary>
        [Example(Name = "Example", Description = "Bu bir örnek attribute.")]
        public static void AttributeExample()
        {
            var attr = new ExampleAttribute { Name = "Example", Description = "Bu bir örnek attribute." };
            Console.WriteLine($"Attribute Name: {attr.Name}, Description: {attr.Description}");
        }
    }
}