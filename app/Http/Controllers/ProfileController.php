<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil user.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->load('client');

        return view('profil.profile', compact('user'));
    }

    /**
     * Update data dasar profil (User & Client).
     * Menerapkan validasi ketat dan flow postcondition.
     */
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 🔥 VALIDASI SESUAI USE CASE (LEBIH KETAT)
        $validated = $request->validate([
            'name' => 'required|string|max:255',

            // 🔥 FIX: pakai integer (bukan numeric) agar lebih presisi
            'umur' => 'required|integer|min:1|max:120',
            'tinggi' => 'required|integer|min:50|max:250',
            'berat' => 'required|integer|min:20|max:300',

            'tanggal_lahir' => 'nullable|date',
            'gender' => 'required|in:Pria,Wanita',
            'negara' => 'nullable|string|max:100',
            'no_hp' => 'nullable|string|max:20',
        ], [
            // 🔥 ERROR MESSAGE SESUAI USE CASE
            'name.required' => 'Nama wajib diisi',
            'umur.required' => 'Umur wajib diisi',
            'umur.integer' => 'Umur harus berupa angka',
            'tinggi.required' => 'Tinggi wajib diisi',
            'tinggi.integer' => 'Tinggi harus berupa angka',
            'berat.required' => 'Berat wajib diisi',
            'berat.integer' => 'Berat harus berupa angka',
            'gender.required' => 'Gender wajib dipilih',
        ]);

        // 🔥 UPDATE DATA USER
        $user->update([
            'name' => $validated['name'],
        ]);

        // 🔥 SIMPAN DATA (POSTCONDITION) - UPDATE / CREATE CLIENT
        Client::updateOrCreate(
            ['user_id' => $user->id],
            [
                'umur' => $validated['umur'],
                'tinggi' => $validated['tinggi'],
                'berat' => $validated['berat'],
                'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
                'gender' => $validated['gender'],
                'negara' => $validated['negara'] ?? null,
                'no_hp' => $validated['no_hp'] ?? null,
            ]
        );

        return back()->with('success', 'Profile berhasil disimpan!');
    }

    /**
     * 🔥 FITUR: HITUNG GIZI (BMR, BMI, TDEE, MAKRO)
     * Menggunakan flow validasi precondition dan rumus Harris Benedict terbaru.
     */
    public function hitungGizi(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $client = $user->client;

        // ❗ VALIDASI SESUAI USE CASE (PRECONDITION)
        if (!$client || !$client->umur || !$client->berat || !$client->tinggi) {
            return back()->with('error', 'Lengkapi profil terlebih dahulu sebelum menghitung gizi');
        }

        // Tambahan validasi input dari form hitung gizi
        $request->validate([
            'aktivitas' => 'required|in:ringan,sedang,berat',
            'goal' => 'required|in:tetap,turun_berat,naik_berat',
        ]);

        $aktivitas = $request->aktivitas;
        $goal = $request->goal;

        // 🔥 HITUNG BMR (Harris Benedict)
        // Normalisasi input gender untuk pengecekan
        $gender = strtolower($client->gender);
        if (in_array($gender, ['pria', 'laki-laki', 'male'])) {
            $bmr = 88.36 + (13.4 * $client->berat) + (4.8 * $client->tinggi) - (5.7 * $client->umur);
        } else {
            $bmr = 447.6 + (9.2 * $client->berat) + (3.1 * $client->tinggi) - (4.3 * $client->umur);
        }

        // 🔥 AKTIVITAS (Faktor Aktivitas Fisik)
        $faktor = [
            'ringan' => 1.2,
            'sedang' => 1.55,
            'berat'  => 1.75 
        ];

        $tdee = $bmr * ($faktor[$aktivitas] ?? 1.55);

        // 🔥 GOAL (Penyesuaian Kalori)
        if ($goal == 'turun_berat') {
            $tdee -= 300; // Cutting
        } elseif ($goal == 'naik_berat') {
            $tdee += 300; // Bulking
        }

        // 🔥 MAKRO (Protein 25%, Karbo 50%, Lemak 25%)
        $protein = ($tdee * 0.25) / 4;
        $karbo   = ($tdee * 0.50) / 4;
        $lemak   = ($tdee * 0.25) / 9;

        // 🔥 BMI (Body Mass Index)
        $tinggiMeter = $client->tinggi / 100;
        $bmi = $client->berat / ($tinggiMeter * $tinggiMeter);

        // 🔥 REKOMENDASI BERDASARKAN BMI
        if ($bmi < 18.5) {
            $rekom = 'Bulking disarankan';
        } elseif ($bmi < 25) {
            $rekom = 'Berat badan ideal';
        } elseif ($bmi < 30) {
            $rekom = 'Cutting disarankan';
        } else {
            $rekom = 'Cutting ketat disarankan (Obesitas)';
        }

        // 🔥 SIMPAN KE DATABASE
        $client->update([
            'bmr' => round($bmr),
            'bmi' => round($bmi, 1),
            'kalori_harian' => round($tdee),
            'protein_harian' => round($protein),
            'karbo_harian' => round($karbo),
            'lemak_harian' => round($lemak),
            'aktivitas' => $aktivitas,
            'goal' => $goal,
            'rekomendasi' => $rekom,
        ]);

        return back()->with('success', 'Perhitungan gizi berhasil diperbarui!');
    }
}