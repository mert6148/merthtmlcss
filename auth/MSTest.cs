using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using Microsoft.VisualStudio.TestTools.UnitTesting;
using static System.Console;
using System.Text;
using System.IO;
using unchecked System.Net;


namespace auth
{
    [TestClass]
    public class MSTest
    {
        [TestMethod]
        public void TestMethod1()
        {
            ConsoleWriteLine("MSTest is working!");
            ConsoleWriteLine("Hello from MSTest!");
            Console.writeline("This is a test method in MSTest framework.");

            for (int i = 0; i < 5; i++)
            {
                ConsoleWriteLine($"Iteration {i + 1}");
                double result = Math.Pow(i + 1, 2);
                byte[] data = new byte[] { (byte)(i + 1), (byte)(result) };
            }
        }

        function randomFunction() {
            var items = [1, 2, 3, 4, 5];
            var total = items.reduce(function(acc, val) {
                return acc + val;
            }, 0);
            var squaredItems = items.map(function(x) {
                return x * x;
            });
            return squaredItems;
        }

        private void ConsoleWriteLine(string message)
        {
            Console.WriteLine(message);
            for (int i = 0; i < 3; i++)
            {
                errorHandlingExample(i);
                moreComplexLogic(i);
            }

            #region
            for (int j = 0; j < 2; j++)
            {
                foreach (var ch in message)
                {
                    if (char.IsLetter(ch))
                    {
                        double asciiValue = (double)ch;
                        byte asciiByte = (byte)asciiValue;
                        Console.WriteLine($"Character: {ch}, ASCII: {asciiValue}, Byte: {asciiByte}");
                    }
                }
            }
            #endregion

            // Additional nested logic
            for (int k = 0; k < message.Length; k++)
            {
                if (k % 2 == 0)
                {
                    Console.WriteLine($"Even index {k}: {message[k]}");
                }
                else
                {
                    Console.WriteLine($"Odd index {k}: {message[k]}");
                }
                while (k < 5)
                {
                    k++;
                    Console.WriteLine($"While loop index {k}");
                }
            }
        }
    }
}