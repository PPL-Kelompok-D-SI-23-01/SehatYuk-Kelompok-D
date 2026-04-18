<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Artikel extends Model
{
    protected $table = 'artikel';

    protected $fillable = [
        'judul',
        'isi',
        'link',
        'tipe',
        'kategori',
        // 'kondisi',
        // 'intensitas',
        // 'durasi',
        'gambar_edukasi'
    ];
}