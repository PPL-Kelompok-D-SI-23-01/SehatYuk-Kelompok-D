<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SehatYuk</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #e9efdf;
        }

        .main {
            margin-left: 120px;
            padding: 25px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .header h2 {
            font-weight: 600;
            color: #333;
        }

        .cards {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .card {
            background: #ffffff;
            padding: 20px;
            border-radius: 20px;
            flex: 1;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            position: relative;
            overflow: hidden;
            border: 1px solid #e0e0e0;
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .icon-box {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            border: 1px solid #eee;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .kalori-icon {
            background: #fff9f0;
            color: #ff9800;
        }

        .nutrisi-icon {
            background: #f0fff4;
            color: #2ecc71;
        }

        .card-value {
            font-size: 32px;
            font-weight: 700;
            color: #222;
        }

        .wave {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 50px;
        }

        .bmi-card {
            display: flex;
            gap: 15px;
            margin-top: 10px;
        }

        .bmi-left {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .bmi-box {
            padding: 10px 15px;
            border-radius: 12px;
            width: 130px;
            font-size: 13px;
            color: #444;
        }

        .height { background: #ffe0b2; }
        .weight { background: #d1e3ff; }

        .bmi-right {
            flex: 1;
            background: #2d2d2d;
            border-radius: 15px;
            padding: 15px;
            color: white;
        }

        .bmi-status-label {
            background: #4caf50;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11px;
            float: right;
        }

        .bmi-bar {
            margin-top: 15px;
            height: 8px;
            border-radius: 10px;
            background: linear-gradient(to right, #5bc0de, #5cb85c, #f0ad4e, #d9534f);
            position: relative;
        }

        .bmi-dot {
            position: absolute;
            top: -4px;
            width: 12px;
            height: 12px;
            background: #fff;
            border-radius: 50%;
            border: 2px solid #000;
        }

        .flex-container {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }

        .graph-section {
            flex: 2;
            background: white;
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        .target-section {
            flex: 1;
            background: white;
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        .target-card {
            border: 2.5px solid #6c8cd5;
            border-radius: 15px;
            padding: 15px;
            background: #f8fbff;
        }

        .btn-catat {
            width: 100%;
            padding: 10px;
            background: #6c8cd5;
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            margin-top: 12px;
            cursor: pointer;
        }

        .footer-rekomendasi {
            margin-top: 20px;
            background: white;
            padding: 15px 25px;
            border-radius: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .food-item {
            background: #fff9c4;
            padding: 8px 15px;
            border-radius: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: 500;
        }

        input[type=range] {
            width: 100%;
            cursor: pointer;
            margin: 10px 0;
        }

        .input-harian {
            width: 100%;
            padding: 8px;
            border-radius: 8px;
            border: 1px solid #ccc;
            margin-top: 5px;
            font-size: 13px;
        }

        .bg-hijau {
            background: #e8f5e9;            
        }

        .bg-kuning {
            background: #fff9c4;
        }

    </style>
</head>

<body>

@include('components.navbar')

@php
    $client = $user->client ?? null;
    $posisiBMI = (int) min(100, max(0, ($bmiSekarang / 40) * 100));
@endphp

<div class="main">

    @if(session('error'))
    <div style="background:#f8d7da;color:#721c24;padding:10px;border-radius:10px;margin-bottom:15px;border:1px solid #f5c6cb;">
        {{ session('error') }}
    </div>
    @endif

    @if(session('success'))
        <div style="background:#d4edda; color:#155724; padding:10px; border-radius:10px; margin-bottom:15px; border:1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    <div class="header">
        <div>
            <h2>Hi, {{ $user->name }} 👋</h2>
            <p style="font-size:12px; color:#666;">{{ now()->translatedFormat('l, F Y') }}</p>
        </div>
    </div>

    <div class="cards">

        <div class="card">
            <div class="card-header">
                <div class="icon-box kalori-icon">🔥</div>
                <h4 style="font-size:14px; color:#555;">Kalori Terbakar</h4>
            </div>
            <div class="card-value">
                {{ number_format($kaloriTerbakar) }}
                <span style="font-size:14px; font-weight:400;">kcal</span>
            </div>
            <svg class="wave" viewBox="0 0 500 100" preserveAspectRatio="none">
                <path d="M0,60 C150,100 350,20 500,60 L500,100 L0,100 Z" fill="rgba(255,165,0,0.2)"></path>
            </svg>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="icon-box nutrisi-icon">🥗</div>
                <h4 style="font-size:14px; color:#555;">Ringkasan Nutrisi</h4>
            </div>
            
            @if(!$isProfileLengkap)
                <div style="color:#e74c3c; font-size:12px; font-weight:600; margin-top: 5px;">
                    ⚠ Lengkapi profil (BB, TB, Umur, Gender)
                </div>
                <small style="color:#999; font-size: 10px; line-height: 1.2; display: block; margin-top: 5px;">
                    Isi profil terlebih dahulu untuk menghitung kebutuhan kalori harian Anda secara akurat.
                </small>
            @else
                <div class="card-value"
                     @style([
                        'color: #e74c3c' => $nutrisiPersen >= 100,
                        'color: #f39c12' => $nutrisiPersen >= 80 && $nutrisiPersen < 100,
                        'color: #2ecc71' => $nutrisiPersen < 80,
                     ])>
                    {{ $nutrisiPersen }}%
                </div>

                <div style="font-size:12px; color:#555; margin-top:2px; font-weight:600;">
                    {{ number_format($totalKaloriMakro) }} / {{ number_format($targetKaloriMasuk) }} kcal
                </div>

                <div style="font-size:10px; color:#aaa; margin-top: 5px;">
                    Karbo: {{ round($persenKarbo) }}% | 
                    Protein: {{ round($persenProtein) }}% | 
                    Lemak: {{ round($persenLemak) }}%
                </div>
            @endif

            <svg class="wave" viewBox="0 0 500 100" preserveAspectRatio="none">
                <path d="M0,70 C120,90 250,10 380,50 C430,70 470,40 500,50 L500,100 L0,100 Z" fill="rgba(46,204,113,0.2)"></path>
            </svg>
        </div>

        <div class="card">
            <h4 style="font-size:14px; color:#555; margin-bottom:10px;">BMI Calculator</h4>
            <div class="bmi-card">
                <div class="bmi-left">
                    <div class="bmi-box height">
                        Height <br>
                        <strong>{{ $client->tinggi ?? 0 }} cm</strong>
                    </div>
                    <div class="bmi-box weight">
                        Weight <br>
                        <strong>{{ $client->berat ?? 0 }} kg</strong>
                    </div>
                </div>
                <div class="bmi-right">
                    <p style="font-size:11px;">BMI (Body Mass Index)</p>
                    <div style="margin-top:5px;">
                        <span style="font-size:20px; font-weight:700;">{{ number_format($bmiSekarang, 1) }}</span>
                        <span class="bmi-status-label">{{ $statusBMI }}</span>
                    </div>
                    <div class="bmi-bar">
                        <div class="bmi-dot"
                            @style([
                                "left: {$posisiBMI}%",
                            ])>
                        </div>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-size:9px; margin-top:5px; color:#aaa;">
                        <span>15</span>
                        <span>18.5</span>
                        <span>25</span>
                        <span>30</span>
                        <span>40</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="flex-container">

        <div class="graph-section">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
                <h4 style="color:#444;">Grafik aktivitas</h4>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="font-size:11px; color:#888;">Filter:</span>
                    <form method="GET">
                        <select name="filter" onchange="this.form.submit()"
                            style="border:1px solid #ddd; border-radius:8px; padding:2px 10px; font-size:12px; cursor: pointer;">
                            <option value="harian" {{ $filter == 'harian' ? 'selected' : '' }}>Harian</option>
                            <option value="mingguan" {{ $filter == 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                            <option value="bulanan" {{ $filter == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                        </select>
                    </form>
                </div>
            </div>
            <canvas id="activityChart" height="150"></canvas>
        </div>

        <div class="target-section">
            <h4 style="color:#444; margin-bottom:10px;">Target Berat Badan</h4>
            <p style="font-size:11px; color:#888; margin-bottom:15px;">{{ now()->format('M Y') }}</p>

            <div class="target-card">
                <form action="/update-target-berat" method="POST">
                    @csrf
                    <div style="display:flex; justify-content:space-between; font-size:11px; color:#555;">
                        <span>Awal</span>
                        <span>Target</span>
                    </div>

                    <input
                        type="range"
                        name="target_berat"
                        min="40"
                        max="120"
                        step="0.1"
                        value="{{ $client->target_berat ?? 0 }}"
                        oninput="document.getElementById('valTarget').innerText = this.value + 'kg'"
                    >

                    <div style="display:flex; justify-content:space-between; font-weight:600; font-size:13px;">
                        <span>{{ $client->berat ?? 0 }}kg</span>
                        <span id="valTarget" style="color:#6c8cd5;">{{ $client->target_berat ?? 0 }}kg</span>
                    </div>

                    <div style="margin-top:10px;">
                        <label style="font-size:12px; color:#666;">Berat Hari Ini</label>
                        <input
                            type="number"
                            name="berat_harian"
                            step="0.1"
                            value="{{ $client->berat ?? 0 }}"
                            placeholder="Masukkan berat..."
                            class="input-harian"
                        >
                    </div>

                    <button type="submit" class="btn-catat">Catat</button>
                </form>
            </div>

            <div style="margin-top:20px;">
                <h4 style="font-size:14px; color:#444;">Riwayat</h4>
                <canvas id="historyChart" height="100"></canvas>
            </div>

        </div>

    </div>

    <div class="footer-rekomendasi">
        <div>
            <h4 style="font-size:14px; color:#444;">Rekomendasi Makan &amp; Resep</h4>
            <p style="font-size:12px; color:#888;">Makanan Favorit</p>
        </div>

        <div style="display:flex; gap:15px;">
            @forelse($rekomendasi as $item)
                <a href="/resep/{{ $item->id }}" style="text-decoration:none; color:inherit;">
                    <div class="food-item {{ $loop->even ? 'bg-hijau' : 'bg-kuning' }}">
                        <img src="{{ $item->image ? asset('storage/'.$item->image) : 'https://placehold.co/50x50?text=Food' }}" 
                             style="width:40px;height:40px;border-radius:10px;object-fit:cover;">
                        <div>
                            <strong>{{ $item->nama_makanan }}</strong>
                            <br>
                            <small>{{ $item->kalori }} kcal</small>
                        </div>
                        <span style="font-weight:bold; margin-left:10px;">+</span>
                    </div>
                </a>
            @empty
                <p style="font-size:12px; color:#888;">Belum ada rekomendasi</p>
            @endforelse
        </div>

        <div style="display:flex; flex-direction:column; gap:5px;">
            <a href="/aktivitas" 
               style="background:#2ecc71; color:white; border:none; border-radius:8px; padding:5px 15px; font-size:11px; text-decoration:none; text-align:center;">
                Log Aktivitas
            </a>
            <button onclick="openFav()" 
                style="background:#ff7675; color:white; border:none; border-radius:8px; padding:5px 15px; font-size:11px; cursor:pointer;">
                ❤️ Resep Favorit
            </button>
        </div>
    </div>

</div>

<div id="favModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999;">
    <div style="background:white; width:400px; max-height:80vh; overflow:auto; margin:80px auto; padding:20px; border-radius:15px; position:relative;">
        <h3 style="margin-bottom:15px;">❤️ Resep Favorit</h3>
        @forelse($favorites as $f)
            <div style="padding:10px; border-bottom:1px solid #eee; margin-bottom:10px;">
                <b style="font-size:14px;">{{ $f->resep->nama_makanan ?? '-' }}</b><br>
                <small style="color:#777;">{{ $f->resep->kategori ?? '-' }}</small><br>
                <a href="/resep/{{ $f->resep->id ?? '' }}" 
                   style="display:inline-block;margin-top:8px;background:#7ed957;padding:5px 12px;border-radius:8px;text-decoration:none;color:black;font-size:12px;font-weight:500;">
                    Lihat Resep
                </a>
            </div>
        @empty
            <p style="text-align:center; padding:20px; color:#999;">Belum ada resep favorit</p>
        @endforelse
        <button onclick="closeFav()" 
            style="width:100%;margin-top:10px;padding:10px;background:#ddd;border:none;border-radius:8px;cursor:pointer;font-weight:600;">
            Tutup
        </button>
    </div>
</div>

<script>
    const labelAktivitas = JSON.parse('{!! json_encode($labelAktivitas ?? []) !!}');
    const dataBMI = JSON.parse('{!! json_encode($dataBMI ?? []) !!}');
    const dataKalori = JSON.parse('{!! json_encode($dataKalori ?? []) !!}');
    const dataNutrisi = JSON.parse('{!! json_encode($dataNutrisi ?? []) !!}');
    const kaloriMasukPerHari = JSON.parse('{!! json_encode($dataKaloriMasuk ?? []) !!}');
    const targetKalori = Number('{!! $targetKaloriMasuk ?? 2000 !!}');
    const tanggal = JSON.parse('{!! json_encode($tanggal ?? []) !!}');
    const riwayat = JSON.parse('{!! json_encode($riwayat ?? []) !!}');
</script>

<script>
    const ctxActivity = document.getElementById('activityChart').getContext('2d');

    new Chart(ctxActivity, {
        type: 'bar',
        data: {
            labels: labelAktivitas,
            datasets: [
                {
                    label: 'BMI',
                    data: dataBMI,
                    backgroundColor: '#ff7675',
                    borderRadius: 5
                },
                {
                    label: 'Kalori',
                    data: dataKalori,
                    backgroundColor: '#fdcb6e',
                    borderRadius: 5
                },
                {
                    label: 'Nutrisi',
                    data: dataNutrisi,
                    backgroundColor: '#55efc4',
                    borderRadius: 5
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 12, font: { size: 10 } }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            if (context.dataset.label === 'Nutrisi') {
                                const index = context.dataIndex;
                                const kalori = kaloriMasukPerHari[index] ?? 0;

                                return 'Nutrisi: ' 
                                    + kalori.toLocaleString() 
                                    + ' / ' 
                                    + targetKalori.toLocaleString() 
                                    + ' kcal';
                            }
                            return context.dataset.label + ': ' + context.raw;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: function(value) { return value + '%'; } }
                }
            }
        }
    });

    const ctxHistory = document.getElementById('historyChart').getContext('2d');
    new Chart(ctxHistory, {
        type: 'line',
        data: {
            labels: tanggal,
            datasets: [{
                data: riwayat,
                borderColor: '#6c8cd5',
                backgroundColor: 'rgba(108, 140, 213, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 0
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { x: { display: true }, y: { display: false } }
        }
    });

    function openFav(){ document.getElementById('favModal').style.display = 'block'; }
    function closeFav(){ document.getElementById('favModal').style.display = 'none'; }
</script>

</body>
</html>