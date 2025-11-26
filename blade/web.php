<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $anasayfaMesaj = "Merthtmlcss projesine hoş geldiniz! Burada modern web teknolojileriyle ilgili örnekler ve açıklamalar bulabilirsiniz.";
    $iletisimLink = '<a href="mailto:info@merthtmlcss.com?subject=İletişim&body=Merhaba, Merthtmlcss ile ilgili bir sorum var.">Bize e-posta gönderin</a>';
    $sayfaBilgisi = [
        'baslik' => 'Ana Sayfa',
        'aciklama' => 'Modern web geliştirme projesi',
        'tarih' => date('d.m.Y H:i')
    ];
    return view('index', compact('anasayfaMesaj', 'iletisimLink', 'sayfaBilgisi'));
});

Route::get('/welcome', function () {
    $hosgeldinMesaj = "Laravel Blade ile oluşturulmuş modern hoş geldiniz ekranına hoş geldiniz!";
    
    // For döngüsü ile kullanıcı bilgilerini oluştur
    $kullaniciBilgisi = [];
    $bilgiAlanlari = ['ziyaretTarihi', 'sayfaAdi', 'versiyon', 'durum'];
    $bilgiDegerleri = [date('d.m.Y H:i:s'), 'Welcome', 'v2.1.0', 'Aktif'];
    $bilgiGirişimleri = count($bilgiAlanlari);
    $kullaniciBilgisi['girisimler'] = $bilgiGirişimleri;
    
    for ($i = 0; $i < count($bilgiAlanlari); $i++) {
        $kullaniciBilgisi[$bilgiAlanlari[$i]] = $bilgiDegerleri[$i];
    }
    
    return view('welcome', compact('hosgeldinMesaj', 'kullaniciBilgisi'));
});

Route::get('/bilgi', function () {
    $ekBilgi = "Bu proje, Laravel Blade ile dinamik olarak yönetilmektedir. Tüm kodlar açık kaynak! Proje sürümü: v2.1.0 - Güncelleme tarihi: " . date('d.m.Y') . " - Teknolojiler: HTML5, CSS3, JavaScript ES6+, PHP 8.0+, Laravel 10.x";
    
    // While döngüsü ile proje detaylarını oluştur
    $projeDetaylari = [];
    $detaylar = [
        'gelistirici' => 'Merthtmlcss',
        'lisans' => 'MIT',
        'github' => 'https://github.com/merthtmlcss',
        'sonGuncelleme' => date('Y-m-d H:i:s'),
        'teknolojiler' => 'HTML5, CSS3, JavaScript, PHP, Laravel'
    ];
    
    $anahtarlar = array_keys($detaylar);
    $index = 0;
    
    while ($index < count($anahtarlar)) {
        $anahtar = $anahtarlar[$index];
        $projeDetaylari[$anahtar] = $detaylar[$anahtar];
        $index++;
    }
    
    return view('index', compact('ekBilgi', 'projeDetaylari'));
});

// Kullanıcı ID'sine göre dinamik route
Route::get('/kullanici/{id}', function ($id) {
    // Kullanıcı verilerini simüle et
    $kullanicilar = [
        'ahmet' => ['ad' => 'Ahmet', 'soyad' => 'Yılmaz', 'email' => 'ahmet@merthtmlcss.com', 'rol' => 'Admin', 'id' => 1],
        'fatma' => ['ad' => 'Fatma', 'soyad' => 'Demir', 'email' => 'fatma@merthtmlcss.com', 'rol' => 'Kullanıcı', 'id' => 2],
        'mehmet' => ['ad' => 'Mehmet', 'soyad' => 'Kaya', 'email' => 'mehmet@merthtmlcss.com', 'rol' => 'Moderatör', 'id' => 3],
        'ayse' => ['ad' => 'Ayşe', 'soyad' => 'Özkan', 'email' => 'ayse@merthtmlcss.com', 'rol' => 'Kullanıcı', 'id' => 4],
        'ali' => ['ad' => 'Ali', 'soyad' => 'Çelik', 'email' => 'ali@merthtmlcss.com', 'rol' => 'Editör', 'id' => 5]
        'zeynep' => ['ad' => 'Zeynep', 'soyad' => 'Arslan', 'email' => 'zeynep@merthtmlcss.com', 'rol' => 'Sunucu Yöneticisi', 'id' => 6],
        'can' => ['ad' => 'Can', 'soyad' => 'Yıldız', 'email' => 'can@merthtmlcss.com', 'rol' => 'Kanal Moderatörü', 'id' => 7],
        'elif' => ['ad' => 'Elif', 'soyad' => 'Kurt', 'email' => 'elif@merthtmlcss.com', 'rol' => 'Ajans Sorumlusu', 'id' => 8],
        'mert' => ['ad' => 'Mert', 'soyad' => 'Şahin', 'email' => 'mert@merthtmlcss.com', 'rol' => 'Teknik Destek', 'id' => 9],
        'seda' => ['ad' => 'Seda', 'soyad' => 'Güneş', 'email' => 'seda@merthtmlcss.com', 'rol' => 'Halkla İlişkiler', 'id' => 10]
    ];
    
    // Kullanıcı ID'sine göre veri oluştur
    $kullaniciVerisi = [];
    $kullaniciID = (int)$id;
    
    if (isset($kullanicilar[$kullaniciID])) {
        $kullanici = $kullanicilar[$kullaniciID];
        
        // For döngüsü ile kullanıcı bilgilerini işle
        $kullaniciAlanlari = ['id', 'ad', 'soyad', 'email', 'rol', 'kayitTarihi', 'sonGiris'];
        $kullaniciDegerleri = [
            $kullaniciID,
            $kullanici['ad'],
            $kullanici['soyad'],
            $kullanici['email'],
            $kullanici['rol'],
            date('Y-m-d', strtotime('-' . rand(1, 365) . ' days')),
            date('Y-m-d H:i:s', strtotime('-' . rand(1, 24) . ' hours'))
        ];
        
        for ($i = 0; $i < count($kullaniciAlanlari); $i++) {
            $kullaniciVerisi[$kullaniciAlanlari[$i]] = $kullaniciDegerleri[$i];
        }
        
        // Kullanıcıya özel mesaj oluştur
        $kullaniciMesaji = "Hoş geldin {$kullanici['ad']}! Sen {$kullanici['rol']} rolüne sahipsin.";
        
        return view('welcome', compact('kullaniciVerisi', 'kullaniciMesaji'));
    } else {
        return "Kullanıcı bulunamadı! Geçerli ID'ler: 1-5";
    }
});

// Kullanıcı listesi route'u
Route::get('/kullanicilar', function () {
    $kullanicilar = [
        ['id' => 1, 'ad' => 'Ahmet', 'soyad' => 'Yılmaz', 'rol' => 'Admin'],
        ['id' => 2, 'ad' => 'Fatma', 'soyad' => 'Demir', 'rol' => 'Kullanıcı'],
        ['id' => 3, 'ad' => 'Mehmet', 'soyad' => 'Kaya', 'rol' => 'Moderatör'],
        ['id' => 4, 'ad' => 'Ayşe', 'soyad' => 'Özkan', 'rol' => 'Kullanıcı'],
        ['id' => 5, 'ad' => 'Ali', 'soyad' => 'Çelik', 'rol' => 'Editör']
        ['id' => 6, 'ad' => 'Zeynep', 'soyad' => 'Arslan', 'rol' => 'Sunucu Yöneticisi'],
        ['id' => 7, 'ad' => 'Can', 'soyad' => 'Yıldız', 'rol' => 'Kanal Möderatörü'],
        ['id' => 8, 'ad' => 'Elif', 'soyad' => 'Kurt', 'rol' => 'Ajans Sorumlusu'],
        ['id' => 9, 'ad' => 'Mert', 'soyad' => 'Şahin', 'rol' => 'Teknik Destek'],
        ['id' => 10, 'ad' => 'Seda', 'soyad' => 'Güneş', 'rol' => 'Halkla İlişkiler']
    ];
    
    $kullaniciListesi = [];
    $index = 0;
    
    // While döngüsü ile kullanıcı listesini işle
    while ($index < count($kullanicilar)) {
        $kullanici = $kullanicilar[$index];
        $kullanici['profilLink'] = "/kullanici/{$kullanici['id']}";
        $kullaniciListesi[] = $kullanici;
        $index++;
    }
    
    return view('index', compact('kullaniciListesi'));
});

// Kullanıcı giriş formu - GET
Route::get('/giris', function () {
    $girisMesaji = "Lütfen kullanıcı adınızı girin:";
    $hataMesaji = "";
    $baslik = "Kullanıcı Girişi";
    return view('welcome', compact('girisMesaji', 'hataMesaji', 'baslik'));
});

// Kullanıcı giriş işlemi - POST
Route::post('/giris', function (\Illuminate\Http\Request $request) {
    $kullaniciAdi = trim($request->input('kullanici_adi', ''));
    $email = trim($request->input('email', ''));
    $kullanicilar = [
        'ahmet' => ['ad' => 'Ahmet', 'soyad' => 'Yılmaz', 'email' => 'ahmet@merthtmlcss.com', 'rol' => 'Admin', 'id' => 1],
        'fatma' => ['ad' => 'Fatma', 'soyad' => 'Demir', 'email' => 'fatma@merthtmlcss.com', 'rol' => 'Kullanıcı', 'id' => 2],
        'mehmet' => ['ad' => 'Mehmet', 'soyad' => 'Kaya', 'email' => 'mehmet@merthtmlcss.com', 'rol' => 'Moderatör', 'id' => 3],
        'ayse' => ['ad' => 'Ayşe', 'soyad' => 'Özkan', 'email' => 'ayse@merthtmlcss.com', 'rol' => 'Kullanıcı', 'id' => 4],
        'ali' => ['ad' => 'Ali', 'soyad' => 'Çelik', 'email' => 'ali@merthtmlcss.com', 'rol' => 'Editör', 'id' => 5]
        'zeynep' => ['ad' => 'Zeynep', 'soyad' => 'Arslan', 'email' => 'zeynep@merthtmlcss.com', 'rol' => 'Sunucu Yöneticisi', 'id' => 6],
        'can' => ['ad' => 'Can', 'soyad' => 'Yıldız', 'email' => 'can@merthtmlcss.com', 'rol' => 'Kanal Moderatörü', 'id' => 7],
        'elif' => ['ad' => 'Elif', 'soyad' => 'Kurt', 'email' => 'elif@merthtmlcss.com', 'rol' => 'Ajans Sorumlusu', 'id' => 8],
        'mert' => ['ad' => 'Mert', 'soyad' => 'Şahin', 'email' => 'mert@merthtmlcss.com', 'rol' => 'Teknik Destek', 'id' => 9],
        'seda' => ['ad' => 'Seda', 'soyad' => 'Güneş', 'email' => 'seda@merthtmlcss.com', 'rol' => 'Halkla İlişkiler', 'id' => 10]
    ];
    $girisBasarili = false;
    $kullaniciVerisi = [];
    $hataMesaji = "";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $hataMesaji = "Geçerli bir e-posta adresi giriniz.";
    } else {
        foreach ($kullanicilar as $anahtar => $kullanici) {
            if (strtolower($kullaniciAdi) === $anahtar && $email === $kullanici['email']) {
                $girisBasarili = true;
                $kullaniciVerisi = [
                    'id' => $kullanici['id'],
                    'ad' => $kullanici['ad'],
                    'soyad' => $kullanici['soyad'],
                    'email' => $kullanici['email'],
                    'rol' => $kullanici['rol'],
                    'kullanici_adi' => $anahtar,
                    'giris_tarihi' => date('Y-m-d H:i:s'),
                    'oturum_suresi' => '2 saat'
                ];
                break;
            }
        }
        if (!$girisBasarili) {
            $hataMesaji = "Kullanıcı adı veya email hatalı! Lütfen tekrar deneyin.";
        }
    }
    if ($girisBasarili) {
        $hosgeldinMesaj = "Hoş geldin {$kullaniciVerisi['ad']} {$kullaniciVerisi['soyad']}! Sen {$kullaniciVerisi['rol']} rolüne sahipsin.";
        $baslik = "Kullanıcı Paneli";
        // Session ile giriş yapılabilir (örnek):
        // session(['kullanici' => $kullaniciVerisi]);
        return view('welcome', compact('kullaniciVerisi', 'hosgeldinMesaj', 'baslik', 'girisBasarili'));
    } else {
        $girisMesaji = "Lütfen kullanıcı adınızı girin:";
        $baslik = "Giriş Hatası";
        return view('welcome', compact('hataMesaji', 'girisMesaji', 'baslik'));
    }
});

// Kullanıcı çıkış - GET
Route::get('/cikis', function () {
    // session()->forget('kullanici');
    $cikisMesaji = "Başarıyla çıkış yaptınız. Tekrar giriş yapmak için /giris adresini ziyaret edin.";
    $baslik = "Çıkış Yapıldı";
    return view('welcome', compact('cikisMesaji', 'baslik'));
});

// Kullanıcı kayıt formu - GET
Route::get('/kayit', function () {
    $kayitMesaji = "Yeni kullanıcı kaydı için bilgilerinizi girin:";
    $baslik = "Kullanıcı Kaydı";
    return view('welcome', compact('kayitMesaji', 'baslik'));
});

// Kullanıcı kayıt işlemi - POST
Route::post('/kayit', function (\Illuminate\Http\Request $request) {
    $yeniAd = trim($request->input('yeni_ad', ''));
    $yeniSoyad = trim($request->input('yeni_soyad', ''));
    $yeniEmail = trim($request->input('yeni_email', ''));
    $yeniKullaniciAdi = trim($request->input('yeni_kullanici_adi', ''));
    $hataMesaji = '';
    if (empty($yeniAd) || empty($yeniSoyad) || empty($yeniEmail) || empty($yeniKullaniciAdi)) {
        $hataMesaji = "Tüm alanları doldurun!";
    } elseif (!filter_var($yeniEmail, FILTER_VALIDATE_EMAIL)) {
        $hataMesaji = "Geçerli bir e-posta adresi giriniz!";
    }
    if ($hataMesaji) {
        $kayitMesaji = "Yeni kullanıcı kaydı için bilgilerinizi girin:";
        $baslik = "Kayıt Hatası";
        return view('welcome', compact('hataMesaji', 'kayitMesaji', 'baslik'));
    }
    $yeniKullanici = [
        'id' => rand(6, 999),
        'ad' => htmlspecialchars($yeniAd),
        'soyad' => htmlspecialchars($yeniSoyad),
        'email' => htmlspecialchars($yeniEmail),
        'kullanici_adi' => htmlspecialchars($yeniKullaniciAdi),
        'rol' => 'Yeni Kullanıcı',
        'kayit_tarihi' => date('Y-m-d H:i:s'),
        'durum' => 'Aktif'
    ];
    $basariliMesaj = "Kayıt başarılı! Hoş geldin {$yeniKullanici['ad']} {$yeniKullanici['soyad']}!";
    $baslik = "Kayıt Başarılı";
    // Session ile otomatik giriş yapılabilir (örnek):
    // session(['kullanici' => $yeniKullanici]);
    return view('welcome', compact('yeniKullanici', 'basariliMesaj', 'baslik'));
});

// --- Kullanıcılar için For ve While Döngüsü Örnekleri ---

// For döngüsü ile kullanıcıları ekrana yazdırma
Route::get('/ornek-for', function () {
    $kullanicilar = [
        'ahmet' => ['ad' => 'Ahmet', 'soyad' => 'Yılmaz', 'email' => 'ahmet@merthtmlcss.com', 'rol' => 'Admin', 'id' => 1],
        'fatma' => ['ad' => 'Fatma', 'soyad' => 'Demir', 'email' => 'fatma@merthtmlcss.com', 'rol' => 'Kullanıcı', 'id' => 2],
        'mehmet' => ['ad' => 'Mehmet', 'soyad' => 'Kaya', 'email' => 'mehmet@merthtmlcss.com', 'rol' => 'Moderatör', 'id' => 3],
        'ayse' => ['ad' => 'Ayşe', 'soyad' => 'Özkan', 'email' => 'ayse@merthtmlcss.com', 'rol' => 'Kullanıcı', 'id' => 4],
        'ali' => ['ad' => 'Ali', 'soyad' => 'Çelik', 'email' => 'ali@merthtmlcss.com', 'rol' => 'Editör', 'id' => 5]
        'zeynep' => ['ad' => 'Zeynep', 'soyad' => 'Arslan', 'email' => 'zeynep@merthtmlcss.com', 'rol' => 'Sunucu Yöneticisi', 'id' => 6],
        'can' => ['ad' => 'Can', 'soyad' => 'Yıldız', 'email' => 'can@merthtmlcss.com', 'rol' => 'Kanal Moderatörü', 'id' => 7],
        'elif' => ['ad' => 'Elif', 'soyad' => 'Kurt', 'email' => 'elif@merthtmlcss.com', 'rol' => 'Ajans Sorumlusu', 'id' => 8],
        'mert' => ['ad' => 'Mert', 'soyad' => 'Şahin', 'email' => 'mert@merthtmlcss.com', 'rol' => 'Teknik Destek', 'id' => 9],
        'seda' => ['ad' => 'Seda', 'soyad' => 'Güneş', 'email' => 'seda@merthtmlcss.com', 'rol' => 'Halkla İlişkiler', 'id' => 10]

    ];
    $anahtarlar = array_keys($kullanicilar);
    $liste = '';
    for ($i = 0; $i < count($anahtarlar); $i++) {
        $anahtar = $anahtarlar[$i];
        $kullanici = $kullanicilar[$anahtar];
        $liste .= $kullanici['ad'] . ' ' . $kullanici['soyad'] . ' (' . $kullanici['rol'] . ') - ' . $kullanici['email'] . '<br>';
    }
    return $liste;
});

// While döngüsü ile kullanıcıları ekrana yazdırma
Route::get('/ornek-while', function () {
    $kullanicilar = [
        'ahmet' => ['ad' => 'Ahmet', 'soyad' => 'Yılmaz', 'email' => 'ahmet@merthtmlcss.com', 'rol' => 'Admin', 'id' => 1],
        'fatma' => ['ad' => 'Fatma', 'soyad' => 'Demir', 'email' => 'fatma@merthtmlcss.com', 'rol' => 'Kullanıcı', 'id' => 2],
        'mehmet' => ['ad' => 'Mehmet', 'soyad' => 'Kaya', 'email' => 'mehmet@merthtmlcss.com', 'rol' => 'Moderatör', 'id' => 3],
        'ayse' => ['ad' => 'Ayşe', 'soyad' => 'Özkan', 'email' => 'ayse@merthtmlcss.com', 'rol' => 'Kullanıcı', 'id' => 4],
        'ali' => ['ad' => 'Ali', 'soyad' => 'Çelik', 'email' => 'ali@merthtmlcss.com', 'rol' => 'Editör', 'id' => 5]
        'zeynep' => ['ad' => 'Zeynep', 'soyad' => 'Arslan', 'email' => 'zeynep@merthtmlcss.com', 'rol' => 'Sunucu Yöneticisi', 'id' => 6],
        'can' => ['ad' => 'Can', 'soyad' => 'Yıldız', 'email' => 'can@merthtmlcss.com', 'rol' => 'Kanal Moderatörü', 'id' => 7],
        'elif' => ['ad' => 'Elif', 'soyad' => 'Kurt', 'email' => 'elif@merthtmlcss.com', 'rol' => 'Ajans Sorumlusu', 'id' => 8],
        'mert' => ['ad' => 'Mert', 'soyad' => 'Şahin', 'email' => 'mert@merthtmlcss.com', 'rol' => 'Teknik Destek', 'id' => 9],
        'seda' => ['ad' => 'Seda', 'soyad' => 'Güneş', 'email' => 'seda@merthtmlcss.com', 'rol' => 'Halkla İlişkiler', 'id' => 10]
    ];
    $anahtarlar = array_keys($kullanicilar);
    $i = 0;
    $liste = '';
    while ($i < count($anahtarlar)) {
        $anahtar = $anahtarlar[$i];
        $kullanici = $kullanicilar[$anahtar];
        $liste .= $kullanici['ad'] . ' ' . $kullanici['soyad'] . ' (' . $kullanici['rol'] . ') - ' . $kullanici['email'] . '<br>';
        $i++;
    }
    return $liste;
});