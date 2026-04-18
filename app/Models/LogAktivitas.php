<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAktivitas extends Model
{
    protected $table = 'log_aktivitas';

    protected $fillable = [
        'user_id',
        'jenis',
        'durasi',
        'kalori',
        'jarak',
        'tanggal',
        'target_kalori',
        'target_mingguan'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}