<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Models\Client;
use App\Models\Resep;
use App\Models\LogAktivitas;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'name_depan',
        'name_belakang',
        'umur',
        'gender',
        'negara',
        'no_hp',
        'tanggal_lahir',
        'berat_badan',
        'tinggi_badan',
        'role',
    ];

    public function client()
    {
        return $this->hasOne(Client::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'tanggal_lahir' => 'date',
        ];
    }
}