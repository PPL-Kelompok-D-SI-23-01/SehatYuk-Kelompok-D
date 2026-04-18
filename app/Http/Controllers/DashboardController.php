<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
use App\Models\RiwayatBerat; 
use App\Models\LogAktivitas;      
use App\Models\Resep;           
use App\Models\FavoriteResep;       
use App\Models\DashboardHarian; 
use Carbon\Carbon; 

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard utama dengan data grafik lengkap.
     */
    public function index()
    {
        try {
            // 1. Ambil data user yang sedang login
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $user->load('client');
            $client = $user->client;
            $today = now()->toDateString();

            // Tambahkan parameter filter
            $filter = request('filter', 'mingguan'); // default mingguan

            // 2. 🔥 RIWAYAT BERAT (30 DATA TERAKHIR - Grafik Garis di Card Berat)
            $riwayatData = RiwayatBerat::where('user_id', $user->id)
                ->orderBy('tanggal', 'desc')
                ->take(30)
                ->get()
                ->reverse();

            $riwayat = $riwayatData->pluck('berat')->toArray();
            $tanggal = $riwayatData->map(function($data) {
                return Carbon::parse($data->tanggal)->format('d M');
            })->values()->toArray();


            // 3. 🔥 QUERY AKTIVITAS (HANYA UNTUK DATA KALORI TERBAKAR)
            if ($filter == 'harian') {
                $aktivitas = LogAktivitas::where('user_id', $user->id)
                    ->whereDate('tanggal', now())
                    ->selectRaw('DATE(tanggal) as tanggal, SUM(kalori) as total_kalori')
                    ->groupBy(DB::raw('DATE(tanggal)'))
                    ->orderBy('tanggal')
                    ->get();
            } elseif ($filter == 'mingguan') {
                $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY); 
                $endOfWeek = Carbon::now()->endOfWeek(Carbon::SUNDAY);     

                $aktivitas = LogAktivitas::where('user_id', $user->id)
                    ->whereBetween('tanggal', [$startOfWeek, $endOfWeek])
                    ->selectRaw('DATE(tanggal) as tanggal, SUM(kalori) as total_kalori')
                    ->groupBy(DB::raw('DATE(tanggal)'))
                    ->orderBy('tanggal')
                    ->get();
            } else { // bulanan
                $aktivitas = LogAktivitas::where('user_id', $user->id)
                    ->whereMonth('tanggal', now()->month)
                    ->whereYear('tanggal', now()->year)
                    ->selectRaw('DATE(tanggal) as tanggal, SUM(kalori) as total_kalori')
                    ->groupBy(DB::raw('DATE(tanggal)'))
                    ->orderBy('tanggal')
                    ->get();
            }

            // 4. 🔥 PERBAIKI LABEL (FIX LABEL BIAR SESUAI TANGGAL KALENDER)
            if ($filter == 'harian') {
                $labelAktivitas = [now()->format('d M')];
            } elseif ($filter == 'mingguan') {
                $labelAktivitas = collect(range(0, 6))->map(function ($i) {
                    return now()->startOfWeek()->addDays($i)->format('D');
                })->toArray();
            } else {
                $labelAktivitas = collect(range(0, now()->daysInMonth - 1))->map(function ($i) {
                    return now()->startOfMonth()->addDays($i)->format('d');
                })->toArray();
            }

            // DATA KALORI TERBAKAR - Map ke Label agar tidak bergeser
            $dataKalori = collect($labelAktivitas)->map(function($label, $index) use ($user, $filter) {
                if ($filter == 'harian') {
                    $date = now();
                } elseif ($filter == 'mingguan') {
                    $date = now()->startOfWeek()->addDays($index);
                } else {
                    $date = now()->startOfMonth()->addDays($index);
                }

                return LogAktivitas::where('user_id', $user->id)
                    ->whereDate('tanggal', $date)
                    ->sum('kalori') ?? 0;
            })->toArray();

            // ==========================================
            // 🔥 HITUNG TARGET KALORI (BMR)
            // ==========================================
            $isProfileLengkap =
                $client &&
                $client->berat &&
                $client->tinggi &&
                $client->umur &&
                $client->gender; 

            if ($isProfileLengkap) {
                $bb = $client->berat;
                $tb = $client->tinggi;
                $umur = $client->umur;
                $jk = $client->gender;

                if (in_array(strtolower($jk), ['pria', 'laki-laki', 'male'])) {
                    $bmr = 88.36 + (13.4 * $bb) + (4.8 * $tb) - (5.7 * $umur);
                } else {
                    $bmr = 447.6 + (9.2 * $bb) + (3.1 * $tb) - (4.3 * $umur);
                }
                $targetKaloriMasuk = round($bmr * 1.55);
            } else {
                $targetKaloriMasuk = 0; 
            }

            $targetKaloriMasukDefault = $targetKaloriMasuk > 0 ? $targetKaloriMasuk : ($client->kalori_harian ?? 2000);

            // 🔥 DATA KALORI MASUK (UNTUK GRAFIK UTAMA)
            $dataKaloriMasuk = collect($labelAktivitas)->map(function($label, $index) use ($user, $filter) {
                if ($filter == 'harian') {
                    $date = now();
                } elseif ($filter == 'mingguan') {
                    $date = now()->startOfWeek()->addDays($index);
                } else {
                    $date = now()->startOfMonth()->addDays($index);
                }

                return DashboardHarian::where('user_id', $user->id)
                    ->whereDate('tanggal', $date)
                    ->sum('kalori_masuk') ?? 0;
            })->toArray();

            // LOGIC NUTRISI % GRAFIK
            $dataNutrisi = collect($labelAktivitas)->map(function($label, $index) use ($targetKaloriMasukDefault, $dataKaloriMasuk) {
                $kalori = $dataKaloriMasuk[$index] ?? 0;
                return $targetKaloriMasukDefault > 0 
                    ? min(100, round(($kalori / $targetKaloriMasukDefault) * 100)) 
                    : 0;
            })->toArray();

            // 🔥 AMBIL DATA BERAT PER HARI (MAP UNTUK BMI)
            $riwayatBeratMap = RiwayatBerat::where('user_id', $user->id)
                ->get()
                ->keyBy(function($item){
                    return Carbon::parse($item->tanggal)->format('Y-m-d');
                });

            // 🔥 BMI CALCULATION (SEKARANG/HARI INI)
            if ($client && $client->tinggi && $client->berat) {
                $tinggiMeter = $client->tinggi / 100;
                $bmiSekarang = round($client->berat / ($tinggiMeter * $tinggiMeter), 1);
            } else {
                $bmiSekarang = 0;
            }

            // 🔥 STEP 2: FIX DATA BMI (STOP DI HARI INI AGAR TIDAK KE MASA DEPAN)
            $dataBMI = collect($labelAktivitas)->map(function($label, $index) use ($riwayatBeratMap, $client, $filter, $user) {
                if ($filter == 'harian') {
                    $date = now();
                } elseif ($filter == 'mingguan') {
                    $date = now()->startOfWeek()->addDays($index);
                } else {
                    $date = now()->startOfMonth()->addDays($index);
                }

                // 🔥 STOP KALAU MASA DEPAN (AGAR GRAFIK TIDAK TAMPIL KE DEPAN)
                if ($date->gt(now())) {
                    return null; 
                }

                $tanggalKey = $date->format('Y-m-d');

                // Ambil berat di tanggal tersebut, atau cari berat terakhir sebelumnya, atau fallback ke profil
                $berat = $riwayatBeratMap[$tanggalKey]->berat 
                    ?? RiwayatBerat::where('user_id', $user->id)
                        ->where('tanggal', '<=', $tanggalKey)
                        ->orderBy('tanggal', 'desc')
                        ->value('berat')
                    ?? $client->berat ?? 0;

                if ($client && $client->tinggi && $berat > 0) {
                    $tinggiMeter = $client->tinggi / 100;
                    return round($berat / ($tinggiMeter * $tinggiMeter), 1);
                }

                return null;
            })->toArray();


            // 5. 🔥 AMBIL REKOMENDASI & FAVORIT
            $rekomendasi = Resep::latest()->take(2)->get();
            $favorites = FavoriteResep::with('resep')
                ->where('user_id', $user->id)
                ->get();

            // ==========================================
            // 🔥 DATA RINGKASAN HARI INI
            // ==========================================
            $kaloriTerbakar = LogAktivitas::where('user_id', $user->id)
                ->whereDate('tanggal', $today)
                ->sum('kalori') ?? 0;

            $totalKaloriMakro = DashboardHarian::where('user_id', $user->id)
                ->whereDate('tanggal', now())
                ->sum('kalori_masuk') ?? 0;

            $totalKalori = $totalKaloriMakro;
            $sisaKalori = $targetKaloriMasuk - ($totalKalori - $kaloriTerbakar);


            // ==========================================
            // 🔥 RINGKASAN NUTRISI (MAKRO)
            // ==========================================
            $totalKarbo = DashboardHarian::where('user_id', $user->id)
                ->whereDate('tanggal', now())
                ->sum('karbo') ?? 0;

            $totalProtein = DashboardHarian::where('user_id', $user->id)
                ->whereDate('tanggal', now())
                ->sum('protein') ?? 0;

            $totalLemak = DashboardHarian::where('user_id', $user->id)
                ->whereDate('tanggal', now())
                ->sum('lemak') ?? 0;

            $nutrisiPersen = $targetKaloriMasuk > 0
                ? min(100, round(($totalKaloriMakro / $targetKaloriMasuk) * 100))
                : 0;

            $targetKarboIndiv = $client->karbo_harian ?? (($targetKaloriMasuk * 0.5) / 4);
            $targetProteinIndiv = $client->protein_harian ?? (($targetKaloriMasuk * 0.25) / 4);
            $targetLemakIndiv = $client->lemak_harian ?? (($targetKaloriMasuk * 0.25) / 9);

            $persenKarbo = $targetKarboIndiv > 0 ? min(100, ($totalKarbo / $targetKarboIndiv) * 100) : 0;
            $persenProtein = $targetProteinIndiv > 0 ? min(100, ($totalProtein / $targetProteinIndiv) * 100) : 0;
            $persenLemak = $targetLemakIndiv > 0 ? min(100, ($totalLemak / $targetLemakIndiv) * 100) : 0;


            // ==========================================
            // 🔥 STATUS BMI & WARNA
            // ==========================================
            $statusBMI = 'Normal';
            if ($bmiSekarang > 0) {
                if ($bmiSekarang < 18.5) {
                    $statusBMI = 'Kurus';
                } elseif ($bmiSekarang >= 25 && $bmiSekarang < 30) {
                    $statusBMI = 'Gemuk';
                } elseif ($bmiSekarang >= 30) {
                    $statusBMI = 'Obesitas';
                }
            } else {
                $statusBMI = 'Data Belum Lengkap';
            }

            $warnaKalori = 'green';
            if ($nutrisiPersen >= 100) {
                $warnaKalori = 'red';
            } elseif ($nutrisiPersen >= 80) {
                $warnaKalori = 'orange';
            }

            // 6. 🔥 Kirimkan semua variabel ke view 'dashboard'
            return view('dashboard', compact(
                'user',
                'client',
                'riwayat',
                'tanggal',
                'labelAktivitas',
                'dataKalori',
                'dataNutrisi',
                'dataKaloriMasuk', 
                'dataBMI', // 🔥 STEP 1: JANGAN KOSONGKAN BMI (KEMBALI NORMAL)
                'rekomendasi',
                'favorites',
                'totalKalori',
                'kaloriTerbakar',
                'sisaKalori',
                'nutrisiPersen',
                'warnaKalori',
                'isProfileLengkap',
                'targetKaloriMasuk',
                'totalKaloriMakro',
                'totalKarbo',
                'totalProtein',
                'totalLemak',
                'persenKarbo',
                'persenProtein',
                'persenLemak',
                'bmiSekarang',
                'statusBMI',
                'filter'
            ));

        } catch (\Exception $e) {
            return view('dashboard')->with('error', 'Gagal memuat data: ' . $e->getMessage());
        }
    }
}