<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LogAktivitas;
use App\Models\Client;
use Carbon\Carbon;

class ActivityController extends Controller
{
    /**
     * Menampilkan daftar aktivitas dengan filter dan perhitungan target.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $today = date('Y-m-d');

        // 🔥 AMBIL TARGET DARI LOG AKTIVITAS HARI INI
        $todayActivity = LogAktivitas::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->latest()
            ->first();

        // ➕ TAMBAH FLAG DEFAULT (STEP 1)
        // Mengecek apakah hari ini belum ada record aktivitas atau record yang ada tidak memiliki target_mingguan
        $pakaiDefault = !$todayActivity || !$todayActivity->target_mingguan;

        $target = $todayActivity->target_kalori ?? 1000;
        $targetMingguan = $todayActivity->target_mingguan ?? 150;

        // 🔥 QUERY LIST (MENGECUALIKAN 'Set Target' AGAR TIDAK MUNCUL DI TABEL)
        $query = LogAktivitas::where('user_id', $user->id)
            ->where('jenis', '!=', 'Set Target');

        if ($request->filled('search')) {
            $query->where('jenis', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        // ✅ LOGIKA FILTER WAKTU
        if ($request->filter == 'mingguan') {
            $start = Carbon::now()->startOfWeek()->format('Y-m-d');
            $end = Carbon::now()->endOfWeek()->format('Y-m-d');
            $query->whereBetween('tanggal', [$start, $end]);
        } elseif ($request->filter == 'bulanan') {
            $query->whereMonth('tanggal', Carbon::now()->month)
                  ->whereYear('tanggal', Carbon::now()->year);
        } elseif ($request->filter == 'tahunan') {
            $query->whereYear('tanggal', Carbon::now()->year);
        }

        $activities = $query->latest()->paginate(5);

        // 🔥 HITUNG TOTAL KALORI TERBAKAR HARI INI
        $kaloriHariIni = LogAktivitas::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->where('jenis', '!=', 'Set Target')
            ->sum('kalori');

        // ✅ PERHITUNGAN DURASI MINGGUAN
        $startOfWeek = Carbon::now()->startOfWeek()->format('Y-m-d');
        $endOfWeek = Carbon::now()->endOfWeek()->format('Y-m-d');

        $durasiMingguan = LogAktivitas::where('user_id', $user->id)
            ->whereBetween('tanggal', [$startOfWeek, $endOfWeek])
            ->where('jenis', '!=', 'Set Target')
            ->sum('durasi');

        // 🔥 RETURN VIEW DENGAN TAMBAHAN FLAG pakaiDefault
        return view('aktivitas.index', compact(
            'activities',
            'kaloriHariIni',
            'target',
            'targetMingguan',
            'durasiMingguan',
            'pakaiDefault' // ⬅️ TAMBAHAN STEP 1
        ));
    }

    /**
     * Menyimpan aktivitas baru dengan perhitungan MET otomatis.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'jenis' => 'required',
            'durasi' => 'required|numeric|min:1',
            'tanggal' => 'required|date'
        ]);

        // 🔥 AMBIL DATA CLIENT (UNTUK BERAT BADAN)
        $client = Client::where('user_id', $user->id)->first();

        if (!$client || !$client->berat) {
            return redirect('/profile')->with('error', 'Lengkapi berat badan di profil terlebih dahulu untuk menghitung kalori!');
        }

        // 🔥 TABEL MET VALUE (Metabolic Equivalent of Task)
        $metValues = [
            'Lari'      => 9.8,
            'Joging'    => 7.0,
            'Bersepeda' => 6.0,
            'Renang'    => 8.0,
            'Senam'     => 4.0
        ];

        $met = $metValues[$request->jenis] ?? 5.0;
        $durasiJam = $request->durasi / 60;

        // 🔥 HITUNG KALORI (MET * Berat * Jam)
        if ($request->filled('kalori')) {
            $kalori = $request->kalori;
        } else {
            $kalori = $met * $client->berat * $durasiJam;
        }

        // Ambil snapshot target terakhir
        $lastActivity = LogAktivitas::where('user_id', $user->id)->latest()->first();
        $targetTerakhir = $lastActivity->target_kalori ?? 1000;
        $targetMingguTerakhir = $lastActivity->target_mingguan ?? 150;

        // 🔥 SIMPAN DATA KE LOG AKTIVITAS
        LogAktivitas::create([
            'user_id' => $user->id,
            'jenis' => $request->jenis,
            'durasi' => $request->durasi,
            'kalori' => round($kalori),
            'jarak' => $request->jarak ?? 0,
            'tanggal' => date('Y-m-d', strtotime($request->tanggal)),
            'target_kalori' => $targetTerakhir,
            'target_mingguan' => $targetMingguTerakhir
        ]);

        return back()->with('success', 'Aktivitas berhasil ditambahkan');
    }

    /**
     * Update target kalori harian.
     */
    public function updateTarget(Request $request)
    {
        $request->validate([
            'target_kalori' => 'required|numeric|min:1|max:50000'
        ]);

        $user = Auth::user();
        $today = date('Y-m-d');

        $activity = LogAktivitas::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->latest()
            ->first();

        if ($activity) {
            $activity->update(['target_kalori' => $request->target_kalori]);
        } else {
            LogAktivitas::create([
                'user_id' => $user->id,
                'jenis' => 'Set Target',
                'durasi' => 0,
                'kalori' => 0,
                'jarak' => 0,
                'tanggal' => $today,
                'target_kalori' => $request->target_kalori,
                'target_mingguan' => 150
            ]);
        }

        return back()->with('success', 'Target harian berhasil diubah');
    }

    /**
     * Menghapus aktivitas.
     */
    public function destroy($id)
    {
        $data = LogAktivitas::findOrFail($id);

        if ($data->user_id != Auth::id()) {
            abort(403);
        }

        $data->delete();

        return redirect('/aktivitas')->with('success', 'Aktivitas berhasil dihapus');
    }
}