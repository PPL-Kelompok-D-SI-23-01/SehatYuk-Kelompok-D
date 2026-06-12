<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Models\RiwayatBerat; 

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EdukasiController;
use App\Http\Controllers\KatalogController; 
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\KonsumsiController;
use App\Http\Controllers\RiwayatController; // Import Controller Riwayat

// ================= LANDING =================
Route::get('/', function () {
    return view('landing');
});

// ================= AUTH =================
Route::get('/login',[AuthController::class,'showLogin'])->name('login');
Route::post('/login',[AuthController::class,'login']);

Route::get('/register',[AuthController::class,'showRegister']);
Route::post('/register',[AuthController::class,'register']);

// ================= PROTECTED (Wajib Login) =================
Route::middleware(['auth'])->group(function () {

    // DASHBOARD
    Route::get('/dashboard',[DashboardController::class,'index']);

    // PROFILE
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'update']);

    // ================= GIZI =================
    Route::get('/gizi', function () {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->load('client'); 
        return view('gizi.index', compact('user'));
    });

    Route::post('/hitung-gizi', [ProfileController::class, 'hitungGizi']);

    // ================= TARGET BERAT =================
    Route::post('/update-target-berat', function(Request $request){

        $user = Auth::user();

        $request->validate([
            'target_berat' => 'required|numeric|min:30|max:200',
            'berat_harian' => 'required|numeric|min:30|max:200'
        ],[
            'berat_harian.required' => 'Berat hari ini wajib diisi',
            'berat_harian.numeric' => 'Berat harus berupa angka',
            'berat_harian.min' => 'Berat minimal 30 kg',
            'berat_harian.max' => 'Berat maksimal 200 kg',
        ]);

        $user->client->update([
            'target_berat' => $request->target_berat
        ]);

        $user->client->update([
            'berat' => $request->berat_harian
        ]);

        RiwayatBerat::updateOrCreate(
            [
                'user_id' => $user->id,
                'tanggal' => now()->toDateString()
            ],
            [
                'berat' => $request->berat_harian
            ]
        );

        return back()->with('success', 'Data berhasil disimpan!');
    });

    // ================= AKTIVITAS =================
    Route::get('/aktivitas',[ActivityController::class,'index'])->name('aktivitas');
    Route::post('/aktivitas',[ActivityController::class,'store']);
    Route::delete('/aktivitas/{id}', [ActivityController::class, 'destroy']);
    Route::post('/target/update',[ActivityController::class,'updateTarget']);
    Route::post('/target-mingguan/update', [ActivityController::class, 'updateTargetMingguan']);

    // ================= KATALOG & RESEP =================
    Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog');
    Route::get('/resep/{id}', [KatalogController::class, 'detail']);
    Route::get('/search-resep', [KatalogController::class, 'search'])->name('search.resep');
    Route::post('/favorite/{id}', [KatalogController::class, 'favorite']);
    Route::post('/favorite-toggle/{id}', [KatalogController::class, 'toggleFavorite']);
    Route::get('/katalog/{id}', [KatalogController::class, 'detail'])->name('katalog.detail');

    // ================= KONSUMSI =================
    Route::post('/konsumsi/{id}', [KonsumsiController::class, 'store']);

    // ================= RIWAYAT & EXPORT PDF =================
    Route::get('/riwayat', [RiwayatController::class, 'index']);
    Route::get('/riwayat/pdf', [RiwayatController::class, 'exportPdf']);
    Route::get('/riwayat/detail/{tanggal}', [RiwayatController::class, 'detail']);

    // ================= EDUKASI =================
    Route::get('/edukasi', [EdukasiController::class, 'index']);
    Route::get('/edukasi/artikel/{id}', [EdukasiController::class, 'detailArtikel']);
    Route::get('/edukasi/kategori/{kategori}', [EdukasiController::class, 'kategori']);
    Route::get('/edukasi/search', [EdukasiController::class, 'search'])->name('edukasi.search');
    Route::get('/edukasi/live-search', [EdukasiController::class, 'liveSearch']);
    Route::get('/edukasi/kamus-gi', [EdukasiController::class, 'kamusGI']);
    Route::get('/load-more', [EdukasiController::class, 'loadMore']);

    // ================= ADMIN (isAdmin Middleware) =================
    Route::middleware(['isAdmin'])->group(function () {

        Route::get('/admin',[AdminController::class,'index'])->name('admin');

        // RESEP
        Route::get('/admin/resep',[AdminController::class,'resep']);
        Route::post('/admin/resep',[AdminController::class,'store']);
        Route::put('/admin/resep/{id}',[AdminController::class,'update']);
        Route::delete('/admin/resep/{id}',[AdminController::class,'destroy']);

        // USER
        Route::get('/admin/user/{id}', [AdminController::class, 'detailUser']);
        Route::put('/admin/user/{id}', [AdminController::class, 'updateUser']);
        Route::delete('/admin/user/{id}', [AdminController::class, 'deleteUser']);

        // ARTIKEL + VIDEO (SUDAH MERGED)
        Route::post('/admin/artikel',[AdminController::class,'storeArtikel']);
        Route::put('/admin/artikel/{id}',[AdminController::class,'updateArtikel']);
        Route::delete('/admin/artikel/{id}',[AdminController::class,'deleteArtikel']);
        
    });

});

// ================= LOGOUT =================
Route::post('/logout',[AuthController::class,'logout']);