<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatResep extends Model
{
    protected $table = 'riwayat_resep';

    protected $fillable = ['user_id', 'meal_id'];

    public function resep()
    {
        return $this->belongsTo(Resep::class, 'meal_id');
    }
}