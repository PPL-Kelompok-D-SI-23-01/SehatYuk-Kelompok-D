<?php

namespace App\Http\Controllers;

use App\Models\Resep;
use App\Models\FavoriteResep;
use App\Models\RiwayatResep;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class KatalogController extends Controller
{
    /**
     * Menampilkan daftar semua makanan, rekomendasi pintar, dan daftar favorit user.
     * Mendukung Filter Kategori via Request.
     */
    public function index(Request $request)
    {
        // 1. AMBIL DATA FILTER DARI REQUEST
        $kategori = $request->kategori;

        // 2. QUERY DASAR MENGGUNAKAN MODEL RESEP
        $query = Resep::query();

        // 3. LOGIKA FILTER KATEGORI
        if ($kategori && $kategori != 'all' && $kategori != 'Semua') {
            $query->where('kategori', $kategori);
        }

        // 4. AMBIL DATA LATEST
        $reseps = $query->latest()->get();

        $favorites = [];
        $rekom = null;

        if (Auth::check()) {
            $userId = Auth::id();

            // AMBIL FAVORIT USER
            $favorites = FavoriteResep::where('user_id', $userId)
                            ->with('resep')
                            ->get();

            // LOGIKA REKOMENDASI PINTAR
            $favKategori = FavoriteResep::where('user_id', $userId)
                ->with('resep')
                ->get()
                ->pluck('resep.kategori');

            $historyKategori = RiwayatResep::where('user_id', $userId)
                ->with('resep')
                ->get()
                ->pluck('resep.kategori');

            $kategoriFavoritUser = $favKategori
                ->merge($historyKategori)
                ->filter()
                ->countBy()
                ->sortDesc()
                ->keys()
                ->first();

            if ($kategoriFavoritUser) {
                $rekom = Resep::where('kategori', $kategoriFavoritUser)->first();
            }

            if (!$rekom) {
                $rekom = Resep::inRandomOrder()->first();
            }
        } else {
            $rekom = Resep::inRandomOrder()->first();
        }

        return view('katalog.index', compact('reseps', 'favorites', 'rekom'));
    }

}