<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LogAktivitas;
use App\Models\DashboardHarian;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->load('client');

        $filter = $request->filter ?? 'mingguan';
        $search = $request->search;

        // 🔥 1. TENTUKAN RANGE TANGGAL BERDASARKAN FILTER (DINAMIS)
        if ($filter == 'harian') {
            $start = Carbon::today();
            $end = Carbon::today();
        } elseif ($filter == 'bulanan') {
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();
        } else { // default mingguan
            $start = Carbon::now()->startOfWeek(Carbon::MONDAY);
            $end = Carbon::now()->endOfWeek(Carbon::SUNDAY);
        }

        // 🔥 2. HITUNG TARGET KALORI (BMR) UNTUK SINKRONISASI GRAFIK
        $targetKaloriMasuk = 2000;
        $isProfileLengkap = $user->client && 
                            $user->client->berat && 
                            $user->client->tinggi && 
                            $user->client->umur && 
                            $user->client->gender;

        if ($isProfileLengkap) {
            $bb = $user->client->berat;
            $tb = $user->client->tinggi;
            $umur = $user->client->umur;
            $jk = $user->client->gender;

            if (in_array(strtolower($jk), ['pria', 'laki-laki', 'male'])) {
                $bmr = 88.36 + (13.4 * $bb) + (4.8 * $tb) - (5.7 * $umur);
            } else {
                $bmr = 447.6 + (9.2 * $bb) + (3.1 * $tb) - (4.3 * $umur);
            }
            $targetKaloriMasuk = round($bmr * 1.55);
        }

        // 🔥 3. QUERY LOG AKTIVITAS DENGAN SEARCH & FILTER
        $query = LogAktivitas::where('user_id', $user->id)
            ->whereBetween('tanggal', [$start, $end]);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('jenis', 'like', "%$search%")
                  ->orWhere('tanggal', 'like', "%$search%");
            });
        }

        $logs = $query->latest('tanggal')->paginate(5)->withQueryString();

        $aktivitasGrafik = LogAktivitas::where('user_id', $user->id)
            ->whereBetween('tanggal', [$start, $end])
            ->selectRaw('DATE(tanggal) as tanggal, SUM(kalori) as total_kalori')
            ->groupBy(DB::raw('DATE(tanggal)'))
            ->get();

        // 🔥 4. PREPARASI DATA GRAFIK (DINAMIS & FIX 00:00)
        $labels = [];
        $dataKalori = [];
        $dataNutrisi = [];
        $dataBMI = [];
        $dataKaloriMasuk = [];

        $period = CarbonPeriod::create($start, $end);

        foreach ($period as $date) {
            if ($filter == 'bulanan') {
                $labels[] = $date->format('d'); 
            } elseif ($filter == 'harian') {
                $labels[] = $date->format('d M'); 
            } else {
                $labels[] = $date->format('D'); 
            }

            $aktivitasHari = $aktivitasGrafik->firstWhere('tanggal', $date->toDateString());
            $dataKalori[] = $aktivitasHari->total_kalori ?? 0;

            $kaloriMasuk = DashboardHarian::where('user_id', $user->id)
                ->whereDate('tanggal', $date)
                ->sum('kalori_masuk') ?? 0;
            $dataKaloriMasuk[] = $kaloriMasuk;

            $dataNutrisi[] = $targetKaloriMasuk > 0 
                ? min(100, round(($kaloriMasuk / $targetKaloriMasuk) * 100)) 
                : 0;

            $bmiValue = 0;
            if ($user->client && $user->client->tinggi && $user->client->berat) {
                $tinggiMeter = $user->client->tinggi / 100;
                $bmiValue = round($user->client->berat / ($tinggiMeter * $tinggiMeter), 1);
            }
            $dataBMI[] = $aktivitasHari ? $bmiValue : 0;
        }

        return view('riwayat.index', compact(
            'labels',
            'dataKalori',
            'dataNutrisi',
            'dataBMI',
            'dataKaloriMasuk',
            'targetKaloriMasuk',
            'logs',
            'filter',
            'search'
        ));
    }

    /**
     * 🔥 MENGAMBIL DETAIL BERDASARKAN ID AKTIVITAS
     */
    public function detail($id)
    {
        $user = Auth::user();

        // 🔥 1. Ambil data aktivitas berdasarkan ID
        $dataAktivitas = LogAktivitas::where('user_id', $user->id)
            ->where('id', $id)
            ->get();

        // 🔥 2. Ambil tanggal dari aktivitas tersebut untuk sinkronisasi data makanan
        $tanggal = $dataAktivitas->first()?->tanggal;

        // 🔥 3. Ambil data makanan pada tanggal yang sama
        $makanan = DashboardHarian::with('resep') 
            ->where('user_id', $user->id)
            ->whereDate('tanggal', $tanggal)
            ->get();

        // 🔥 4. Hitung total nutrisi harian
        $totalKalori = $makanan->sum('kalori_masuk');
        $totalProtein = $makanan->sum('protein');
        $totalKarbo = $makanan->sum('karbo');
        $totalLemak = $makanan->sum('lemak');

        return response()->json([
            'aktivitas' => $dataAktivitas,
            'makanan' => $makanan,
            'total' => [
                'kalori' => $totalKalori,
                'protein' => $totalProtein,
                'karbo' => $totalKarbo,
                'lemak' => $totalLemak,
            ]
        ]);
    }

    public function exportPdf(Request $request)
    {
        $user = Auth::user();
        $filter = $request->filter ?? 'mingguan';
        $search = $request->search;

        if ($filter == 'harian') {
            $start = Carbon::today();
            $end = Carbon::today();
        } elseif ($filter == 'bulanan') {
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();
        } else {
            $start = Carbon::now()->startOfWeek(Carbon::MONDAY);
            $end = Carbon::now()->endOfWeek(Carbon::SUNDAY);
        }

        $query = LogAktivitas::where('user_id', $user->id)
            ->whereBetween('tanggal', [$start, $end]);

        if ($search) {
            $query->where('jenis', 'like', "%$search%");
        }

        $logs = $query->latest()->get();

        $makanan = DashboardHarian::with('resep')
            ->where('user_id', $user->id)
            ->whereBetween('tanggal', [$start, $end])
            ->get();

        $totalDurasi = $logs->sum('durasi');
        $totalJarak = $logs->sum('jarak');
        $totalKaloriTerbakar = $logs->sum('kalori');
        $totalKaloriMasuk = $makanan->sum('kalori_masuk');
        $totalProtein = $makanan->sum('protein');
        $totalKarbo = $makanan->sum('karbo');
        $totalLemak = $makanan->sum('lemak');

        $selisihKalori = $totalKaloriMasuk - $totalKaloriTerbakar;
        if ($selisihKalori > 0) {
            $statusKalori = 'Surplus';
        } elseif ($selisihKalori < 0) {
            $statusKalori = 'Defisit';
        } else {
            $statusKalori = 'Seimbang';
        }

        $rekomendasi = '';
        if ($statusKalori == 'Surplus') {
            $rekomendasi = 'Asupan kalori Anda melebihi pembakaran aktivitas. Disarankan untuk mengurangi porsi karbohidrat/lemak atau meningkatkan intensitas olahraga.';
        } elseif ($statusKalori == 'Defisit') {
            $rekomendasi = 'Pembakaran kalori Anda lebih besar dari asupan. Disarankan menambah asupan protein dan nutrisi agar tubuh tidak lemas.';
        } else {
            $rekomendasi = 'Keseimbangan kalori Anda sangat baik! Pertahankan pola makan dan aktivitas fisik yang sudah berjalan saat ini 👍';
        }

        $bmi = null;
        $statusBMI = 'Data Belum Lengkap';
        if ($user->client && $user->client->tinggi && $user->client->berat) {
            $tinggiMeter = $user->client->tinggi / 100;
            $bmi = round($user->client->berat / ($tinggiMeter * $tinggiMeter), 1);

            if ($bmi < 18.5) {
                $statusBMI = 'Kurus (Underweight)';
            } elseif ($bmi < 25) {
                $statusBMI = 'Normal (Ideal)';
            } elseif ($bmi < 30) {
                $statusBMI = 'Kelebihan Berat Badan (Overweight)';
            } else {
                $statusBMI = 'Obesitas';
            }
        }

        $pdf = Pdf::loadView('riwayat.pdf', compact(
            'logs', 
            'makanan', 
            'filter', 
            'start', 
            'end',
            'totalDurasi',
            'totalJarak',
            'totalKaloriTerbakar',
            'totalKaloriMasuk',
            'totalProtein',
            'totalKarbo',
            'totalLemak',
            'selisihKalori',
            'statusKalori',
            'rekomendasi',
            'bmi',
            'statusBMI'
        ));

        return $pdf->stream('laporan_aktivitas_sehatyuk.pdf');
    }
}