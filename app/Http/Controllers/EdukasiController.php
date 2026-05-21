<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Artikel;
use App\Models\Client;
use App\Models\Resep;

class EdukasiController extends Controller
{
    /**
     * 🔥 HALAMAN UTAMA EDUKASI (SMART FILTER & REKOMENDASI)
     * Mengambil data tanpa filter kondisi kesehatan (Tampilan Umum).
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $client = Client::where('user_id', $user->id)->first();

        // Tetap ambil kondisi user hanya untuk keperluan display info di Blade (jika ada)
        $kondisiUser = $client->kondisi ?? null;

        // 🔥 1. AMBIL ARTIKEL (Tanpa Filter Kondisi)
        $articles = Artikel::where('tipe', 'artikel')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        // 🔥 2. AMBIL VIDEO (Urutkan agar link NULL berada di bawah)
        $videos = Artikel::where('tipe', 'video')
            ->orderByRaw('link IS NULL') // 🔥 Link yang ada isinya muncul duluan
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        return view('edukasi.index', compact(
            'articles',
            'videos',
            'kondisiUser',
            'rekomendasi',
            'kamusGI',
            'rekomendasiMakanan'
        ));
    }


    /**
     * 🔥 FILTER KATEGORI
     */
    public function kategori($kategori)
    {
        $articles = Artikel::where('kategori', $kategori)
            ->where('tipe', 'artikel')
            ->orderBy('id', 'desc')
            ->get();
        
        $videos = Artikel::where('kategori', $kategori)
            ->where('tipe', 'video')
            ->orderByRaw('link IS NULL')
            ->orderBy('id', 'desc')
            ->get();

        $kondisiUser = null; 
        $rekomendasi = collect(); 
        
        $kamusGI = Resep::whereNotNull('gi')->get();
        $rekomendasiMakanan = Resep::inRandomOrder()->take(5)->get();

        return view('edukasi.index', compact(
            'articles', 
            'videos', 
            'kondisiUser', 
            'rekomendasi', 
            'kamusGI', 
            'rekomendasiMakanan'
        ));
    }

    /**
     * 🔥 SEARCH EDUKASI (FIXED & FINAL)
     */
    public function search(Request $request)
    {
        $keyword = $request->keyword;

        $articles = Artikel::where('tipe', 'artikel')
            ->where('judul', 'like', '%' . $keyword . '%')
            ->orderBy('id', 'desc')
            ->get();

        $videos = Artikel::where('tipe', 'video')
            ->where('judul', 'like', '%' . $keyword . '%')
            ->orderByRaw('link IS NULL')
            ->orderBy('id', 'desc')
            ->get();

        $kondisiUser = null;
        $rekomendasi = collect();
        $kamusGI = Resep::whereNotNull('gi')->get();
        $rekomendasiMakanan = Resep::inRandomOrder()->take(5)->get();

        return view('edukasi.index', compact(
            'articles',
            'videos',
            'kondisiUser',
            'rekomendasi',
            'kamusGI',
            'rekomendasiMakanan'
        ));
    }

    /**
     * 🔥 LOAD MORE DATA (AJAX) - FINAL VERSION
     * Ditambahkan orderByRaw agar data tanpa link tetap di bawah saat pagination.
     */
    public function loadMore(Request $request)
    {
        $offset = $request->offset;
        $tipe = $request->tipe;
        $kategori = $request->kategori;

        $data = Artikel::when($kategori && !in_array(strtolower($kategori), ['semua','all','artikel umum']), function($q) use ($kategori){
                $q->whereRaw('LOWER(TRIM(kategori)) = ?', [trim(strtolower($kategori))]);
            })
            ->where('tipe', $tipe)
            ->orderByRaw('link IS NULL') // 🔥 Link NULL berada di urutan bawah
            ->orderBy('id','desc')
            ->skip($offset)
            ->take(5)
            ->select('id','judul','kategori','link')
            ->get();

        return response()->json($data);
    }

    /**
     * 🔥 LIVE SEARCH EDUKASI (AJAX) - IMPROVED
     * Mengembalikan data JSON dengan dukungan filter kategori aktif.
     */
    public function liveSearch(Request $request)
    {
        $keyword = $request->keyword;
        $kategori = strtolower($request->kategori);

        $articles = Artikel::where('tipe', 'artikel')
            ->when($kategori && $kategori != 'semua' && $kategori != 'all', function($q) use ($kategori){
                $q->whereRaw('LOWER(TRIM(kategori)) = ?', [$kategori]);
            })
            ->where('judul', 'like', '%' . $keyword . '%')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        $videos = Artikel::where('tipe', 'video')
            ->when($kategori && $kategori != 'semua' && $kategori != 'all', function($q) use ($kategori){
                $q->whereRaw('LOWER(TRIM(kategori)) = ?', [$kategori]);
            })
            ->where('judul', 'like', '%' . $keyword . '%')
            ->orderByRaw('link IS NULL')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'articles' => $articles,
            'videos' => $videos
        ]);
    }
}