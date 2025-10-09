#include <iostream>
#include <string>
using namespace std;

// Fonksiyon bildirimleri
void api_helper();
void api_main();
void api();

// Yardımcı fonksiyon
void api_helper()
{
    cout << "API yardımcı fonksiyonu çalışıyor!" << endl;
}

// Ana API fonksiyonu
void api_main()
{
    cout << "API ana fonksiyonu çalışıyor!" << endl;
}

// Ana API fonksiyonu
void api()
{
    // API için gerekli olanlar
    api_helper();
    api_main();

    // Do-while döngüsü
    bool condition = true;
    do
    {
        cout << "API döngüsü" << endl;
        condition = false; // Sonsuz döngüyü önle
    } while (condition);

    // For döngüsü
    for (int i = 0; i < 10; i++)
    {
        cout << "API sayısı: " << i << endl;
    }

    // While döngüsü
    int api_status = 1;
    while (api_status > 0)
    {
        cout << "API while döngüsü" << endl;
        cout << "API durumu: " << api_status << endl;

        switch (api_status)
        {
        case 1:
            cout << "API durumu 1" << endl;
            api_status = 2;
            break;
        case 2:
            cout << "API durumu 2" << endl;
            api_status = 0; // Döngüyü sonlandır
            break;
        default:
            cout << "Bilinmeyen API durumu" << endl;
            api_status = 0;
            break;
        }
    }
}

// Ana program
int main()
{
    cout << "API çalışıyor!" << endl;
    api();
    return 0;
}