<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Client;

class AuthController extends Controller
{
    // ================= LOGIN VIEW =================
    public function showLogin()
    {
        return view('auth.login');
    }

    // ================= REGISTER VIEW =================    
    public function showRegister()
    {
        return view('auth.register');
    }

    // ================= REGISTER =================
    public function register(Request $request)
    {
        // 🔥 CEK EMAIL SUDAH TERDAFTAR
        $cek = User::where('email', $request->email)->first();
        if ($cek) {
            return back()->with('error_email', 'Email sudah terdaftar');
        }

        // 🔥 VALIDASI
        $request->validate([
            'name_depan' => 'required',
            'name_belakang' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'umur' => 'nullable|numeric',
            'tanggal_lahir' => 'nullable|date',
        ]);

        // 🔥 SIMPAN USER + ROLE DEFAULT
        $user = User::create([
            'name' => $request->name_depan . ' ' . $request->name_belakang,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user', 
        ]);

        // 🔥 SIMPAN CLIENT (PENGGANTI PROFILE)
        Client::create([
            'user_id' => $user->id,
            'umur' => $request->umur,
            'tinggi' => null,
            'berat' => null,
            'tanggal_lahir' => $request->tanggal_lahir,
        ]);

        // 🔥 LOAD RELASI (DISESUAIKAN DENGAN NAMA FUNGSI DI MODEL USER)
        $user->load('client');

        // 🔥 AUTO LOGIN
        Auth::login($user);

        return redirect('/dashboard');
    }

    // ================= LOGIN =================
    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        // ❌ EMAIL TIDAK ADA
        if (!$user) {
            return back()->with('error_email', 'Email belum terdaftar');
        }

        // ❌ PASSWORD SALAH
        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error_password', 'Kata sandi anda salah');
        }

        // 🔥 LOAD CLIENT
        $user->load('client');

        // ✅ LOGIN BERHASIL
        Auth::login($user);

        return redirect('/dashboard');
    }

    // ================= LOGOUT =================
    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}