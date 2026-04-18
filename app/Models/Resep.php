<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resep extends Model
{
    protected $table = 'resep';

    protected $fillable = [
        'user_id',
        'nama_makanan',
        'kategori',
        'kalori',
        'protein',
        'karbohidrat',
        'lemak',
        'tanggal',
        'image',
        'deskripsi',
        'bahan',
        'waktu',
        'kesulitan',
        'porsi',
        'langkah',
        'gi'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}