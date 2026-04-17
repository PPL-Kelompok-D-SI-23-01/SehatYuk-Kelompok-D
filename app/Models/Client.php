<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Client extends Model
{
    protected $table = 'client';

    protected $fillable = [
        'user_id',
        'umur',
        'tinggi',
        'berat',
        'tanggal_lahir',
        'target_kalori',
        'target_mingguan',
        'target_protein',
        'target_karbo',
        'target_lemak',
        'target_berat',
        'gender',
        'negara',
        'no_hp',
        'bmr',
        'bmi',
        'kalori_harian',
        'protein_harian',
        'karbo_harian',
        'aktivitas',
        'rekomendasi',
        'goal',
        'lemak_harian'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date:Y-m-d',
        ];
    }
}