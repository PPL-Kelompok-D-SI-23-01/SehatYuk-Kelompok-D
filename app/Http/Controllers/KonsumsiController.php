<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Resep;
use App\Models\DashboardHarian;

class KonsumsiController extends Controller
{
    /**
     * Menyimpan data konsumsi makanan ke dalam dashboard harian.
     */
    public function store($id)
    {
        // 1. Cari data resep berdasarkan ID
        $resep = Resep::findOrFail($id);

        // 2. Simpan ke tabel dashboard_harian dengan meal_id sebagai kunci utama relasi
        DashboardHarian::create([
            'user_id'      => Auth::id(),
            'meal_id'      => $resep->id, 
            'kalori_masuk' => $resep->kalori,
            'karbo'        => $resep->karbohidrat, 
            'protein'      => $resep->protein,
            'lemak'        => $resep->lemak,
            'tanggal'      => now(),
        ]);

        return back()->with('success', 'Makanan berhasil ditambahkan ke ringkasan harian!');
    }
}