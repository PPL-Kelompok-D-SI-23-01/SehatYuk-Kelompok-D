<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DashboardHarian extends Model
{
    protected $table = 'dashboard_harian';

    protected $fillable = [
        'user_id',
        'tanggal',
        'meal_id', // 🔥 ID Referensi ke tabel resep/makanan
        'kalori_masuk',
        'protein',
        'karbo',
        'lemak'
    ];

    public function resep()
    {
        return $this->belongsTo(\App\Models\Resep::class, 'meal_id');
    }
}