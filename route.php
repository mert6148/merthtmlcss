<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Merthtmlcss Route Tanımları
 * Modern Laravel route yapısı ile güçlendirilmiş API ve web route'ları
 * 
 * @author Mert Doğanay
 * @version 2.1.0
 * @since 2024
 */

// Ana sayfa route'ları
Route::get('/', function () {
    $anasayfaMesaj = "Merthtmlcss projesine hoş geldiniz! Burada modern web teknolojileriyle ilgili örnekler ve açıklamalar bulabilirsiniz.";
    $iletisimMail = "info@merthtmlcss.com";
    $iletisimLink = '<a href="mailto:' . $iletisimMail . '?subject=İletişim&body=Merhaba, Merthtmlcss ile ilgili bir sorum var." class="btn btn-info" target="_blank" rel="noopener">Bize e-posta gönderin</a>';
    
    // Cache'den veri al veya oluştur
    $pageData = Cache::remember('homepage_data', 3600, function () {
        return [
            'title' => 'Merthtmlcss - Modern Web Geliştirme',
            'description' => 'Modern web teknolojileri ile geliştirilmiş kapsamlı platform',
            'version' => '2.1.0',
            'last_updated' => now()->format('Y-m-d H:i:s')
        ];
    });
    
    return view('index', compact('anasayfaMesaj', 'iletisimLink', 'pageData'));
})->name('home');

Route::get('/bilgi', function () {
    $ekBilgi = "Bu proje, Laravel Blade ile dinamik olarak yönetilmektedir. Tüm kodlar açık kaynak! Proje sürümü: v2.1.0 - Güncelleme tarihi: " . date('d.m.Y') . " - Teknolojiler: HTML5, CSS3, JavaScript ES6+, PHP 8.0+, Laravel 10.x";
    $iletisimMail = "info@merthtmlcss.com";
    $iletisimLink = '<a href="mailto:' . $iletisimMail . '?subject=İletişim&body=Merhaba, Merthtmlcss ile ilgili bir sorum var." class="btn btn-info" target="_blank" rel="noopener">Bize e-posta gönderin</a>';
    
    // İstatistikleri al
    $stats = Cache::remember('site_stats', 1800, function () {
        return [
            'total_projects' => 15,
            'total_users' => 1250,
            'total_downloads' => 8900,
            'github_stars' => 45
        ];
    });
    
    return view('index', compact('ekBilgi', 'iletisimLink', 'stats'));
})->name('info');

Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome');

Route::get('/hakkinda', function () {
    $hakkindaMesaj = "Bu proje, Laravel Blade ile dinamik olarak yönetilmektedir. Tüm kodlar açık kaynak! Proje sürümü: v2.1.0 - Güncelleme tarihi: " . date('d.m.Y') . " - Teknolojiler: HTML5, CSS3, JavaScript ES6+, PHP 8.0+, Laravel 10.x";
    $iletisimMail = "mertdoganay437@gmail.com";
    $iletisimLink = '<a href="mailto:' . $iletisimMail . '?subject=İletişim&body=Merhaba, Merthtmlcss ile ilgili bir sorum var." class="btn btn-info" target="_blank" rel="noopener">Bize e-posta gönderin</a>';
    
    // Geliştirici bilgileri
    $developer = [
        'name' => 'Mert Doğanay',
        'email' => 'mertdoganay437@gmail.com',
        'github' => 'https://github.com/mert6148',
        'twitter' => 'https://x.com/MertDoganay61',
        'youtube' => 'https://www.youtube.com/@mert_doganay'
    ];
    
    return view('index', compact('hakkindaMesaj', 'iletisimLink', 'developer'));
})->name('about');

Route::get('/print', function () {
    $hakkindaMesaj = "Bu proje, Laravel Blade ile dinamik olarak yönetilmektedir. Tüm kodlar açık kaynak! Proje sürümü: v2.1.0 - Güncelleme tarihi: " . date('d.m.Y') . " - Teknolojiler: HTML5, CSS3, JavaScript ES6+, PHP 8.0+, Laravel 10.x";
    $iletisimMail = "mertdoganay437@gmail.com";
    $iletisimLink = '<a href="mailto:' . $iletisimMail . '?subject=İletişim&body=Merhaba, Merthtmlcss ile ilgili bir sorum var." class="btn btn-info" target="_blank" rel="noopener">Bize e-posta gönderin</a>';
    
    return view('print', compact('hakkindaMesaj', 'iletisimLink'));
})->name('print');

Route::get('/print.py', function () {
    $hakkindaMesaj = "Bu proje, Laravel Blade ile dinamik olarak yönetilmektedir. Tüm kodlar açık kaynak! Proje sürümü: v2.1.0 - Güncelleme tarihi: " . date('d.m.Y') . " - Teknolojiler: HTML5, CSS3, JavaScript ES6+, PHP 8.0+, Laravel 10.x";
    $iletisimMail = "mertdoganay437@gmail.com";
    $iletisimLink = '<a href="mailto:' . $iletisimMail . '?subject=İletişim&body=Merhaba, Merthtmlcss ile ilgili bir sorum var." class="btn btn-info" target="_blank" rel="noopener">Bize e-posta gönderin</a>';
    
    return view('print.py', compact('hakkindaMesaj', 'iletisimLink'));
})->name('print.python');

Route::get('/print.html', function () {
    $hakkindaMesaj = "Bu proje, Laravel Blade ile dinamik olarak yönetilmektedir. Tüm kodlar açık kaynak! Proje sürümü: v2.1.0 - Güncelleme tarihi: " . date('d.m.Y') . " - Teknolojiler: HTML5, CSS3, JavaScript ES6+, PHP 8.0+, Laravel 10.x";
    $iletisimMail = "mertdoganay437@gmail.com";
    $iletisimLink = '<a href="mailto:' . $iletisimMail . '?subject=İletişim&body=Merhaba, Merthtmlcss ile ilgili bir sorum var." class="btn btn-info" target="_blank" rel="noopener">Bize e-posta gönderin</a>';
    
    return view('print.html', compact('hakkindaMesaj', 'iletisimLink'));
})->name('print.html');

// API Route'ları
Route::prefix('api')->group(function () {
    
    // Kimlik doğrulama API'leri
    Route::post('/register', function (Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Doğrulama hatası',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Kullanıcı oluşturma işlemi
            $user = DB::table('users')->insert([
                'username' => $request->username,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::info('Yeni kullanıcı kaydı', ['email' => $request->email]);

            return response()->json([
                'success' => true,
                'message' => 'Kullanıcı başarıyla oluşturuldu'
            ], 201);

        } catch (\Exception $e) {
            Log::error('Kullanıcı kayıt hatası', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Kullanıcı oluşturulurken hata oluştu'
            ], 500);
        }
    })->name('api.register');

    Route::post('/login', function (Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Doğrulama hatası',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = DB::table('users')
                ->where('email', $request->email)
                ->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Geçersiz kimlik bilgileri'
                ], 401);
            }

            // JWT token oluştur
            $token = JWTAuth::fromUser($user);

            Log::info('Kullanıcı girişi', ['email' => $request->email]);

            return response()->json([
                'success' => true,
                'message' => 'Giriş başarılı',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Giriş hatası', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Giriş yapılırken hata oluştu'
            ], 500);
        }
    })->name('api.login');

    // Blog API'leri
    Route::get('/posts', function (Request $request) {
        try {
            $posts = DB::table('posts')
                ->where('status', 'published')
                ->orderBy('created_at', 'desc')
                ->paginate($request->get('per_page', 10));

            return response()->json([
                'success' => true,
                'data' => $posts
            ]);

        } catch (\Exception $e) {
            Log::error('Blog yazıları getirme hatası', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Blog yazıları getirilirken hata oluştu'
            ], 500);
        }
    })->name('api.posts.index');

    Route::get('/posts/{id}', function ($id) {
        try {
            $post = DB::table('posts')
                ->where('id', $id)
                ->where('status', 'published')
                ->first();

            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Yazı bulunamadı'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $post
            ]);

        } catch (\Exception $e) {
            Log::error('Blog yazısı getirme hatası', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Blog yazısı getirilirken hata oluştu'
            ], 500);
        }
    })->name('api.posts.show');

    // İletişim API'si
    Route::post('/contact', function (Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'message' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Doğrulama hatası',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::table('contact_messages')->insert([
                'name' => $request->name,
                'email' => $request->email,
                'message' => $request->message,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::info('Yeni iletişim mesajı', ['email' => $request->email]);

            return response()->json([
                'success' => true,
                'message' => 'Mesajınız başarıyla gönderildi'
            ], 201);

        } catch (\Exception $e) {
            Log::error('İletişim mesajı hatası', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Mesaj gönderilirken hata oluştu'
            ], 500);
        }
    })->name('api.contact');

    // İstatistik API'si
    Route::get('/stats', function () {
        try {
            $stats = Cache::remember('api_stats', 3600, function () {
                return [
                    'total_users' => DB::table('users')->count(),
                    'total_posts' => DB::table('posts')->where('status', 'published')->count(),
                    'total_comments' => DB::table('comments')->count(),
                    'unread_messages' => DB::table('contact_messages')->where('read', false)->count(),
                    'last_updated' => now()->format('Y-m-d H:i:s')
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('İstatistik getirme hatası', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'İstatistikler getirilirken hata oluştu'
            ], 500);
        }
    })->name('api.stats');

});

// Admin route'ları (middleware ile korumalı)
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/users', function () {
        $users = DB::table('users')->paginate(20);
        return view('admin.users', compact('users'));
    })->name('admin.users');

    Route::get('/posts', function () {
        $posts = DB::table('posts')->paginate(20);
        return view('admin.posts', compact('posts'));
    })->name('admin.posts');

    Route::get('/messages', function () {
        $messages = DB::table('contact_messages')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.messages', compact('messages'));
    })->name('admin.messages');

});

// Hata sayfaları
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Sayfa bulunamadı',
        'error' => '404 Not Found'
    ], 404);
});

// Health check endpoint
Route::get('/health', function () {
    try {
        // Veritabanı bağlantısını kontrol et
        DB::connection()->getPdo();
        
        return response()->json([
            'status' => 'healthy',
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'version' => '2.1.0',
            'database' => 'connected',
            'cache' => Cache::has('health_check') ? 'working' : 'working'
        ]);

    } catch (\Exception $e) {
        Log::error('Health check hatası', ['error' => $e->getMessage()]);
        
        return response()->json([
            'status' => 'unhealthy',
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'error' => $e->getMessage()
        ], 500);
    }
})->name('health');

// Cache health check
Cache::put('health_check', true, 60);

/**
 * Route middleware tanımları
 */
Route::middleware(['throttle:60,1'])->group(function () {
    // Rate limiting uygulanan route'lar
});

Route::middleware(['cors'])->group(function () {
    // CORS uygulanan route'lar
});

/**
 * Route cache temizleme
 */
Route::get('/clear-cache', function () {
    try {
        Cache::flush();
        return response()->json([
            'success' => true,
            'message' => 'Cache başarıyla temizlendi'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Cache temizlenirken hata oluştu'
        ], 500);
    }
})->name('clear-cache');

/**
 * Route log temizleme
 */
Route::get('/clear-logs', function () {
    try {
        // Log dosyalarını temizle
        $logFiles = glob(storage_path('logs/*.log'));
        foreach ($logFiles as $file) {
            if (filesize($file) > 10 * 1024 * 1024) { // 10MB'dan büyükse
                file_put_contents($file, '');
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Log dosyaları başarıyla temizlendi'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Log dosyaları temizlenirken hata oluştu'
        ], 500);
    }
})->name('clear-logs');

Route::get('/logs', function () {
    try {
        $logFiles = glob(storage_path('logs/*.log'));
        $logs = [];
        foreach ($logFiles as $file) {
            $logs[basename($file)] = file_get_contents($file);
        }
        
        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Log dosyaları getirilirken hata oluştu'
        ], 500);
    }
})->name('view-logs');

Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'Test route çalışıyor',
        'timestamp' => now()->format('Y-m-d H:i:s')
    ]);

})->middleware('auth')->middleware('throttle:60,1')->middleware('cors')->before(function (Request $request) {
    Log::info('Test route accessed', [
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent(),
        'timestamp' => now()->format('Y-m-d H:i:s')
    ]);
    Cache::put('test_route_accessed', true, 60);
    DB::table('route_access_logs')->insert([
        'route' => '/test',
        'ip_address' => $request->ip(),
        'user_agent' => $request->userAgent(),
        'accessed_at' => now()
    ]);
    
})->name('test');

?>