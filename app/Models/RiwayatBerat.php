<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatBerat extends Model
{
    protected $table = 'riwayat_berat';

    protected $fillable = [
        'user_id',
        'berat',
        'tanggal'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}