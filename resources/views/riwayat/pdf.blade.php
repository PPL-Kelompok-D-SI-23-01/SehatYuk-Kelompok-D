<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">

<style>
    body {
        font-family: DejaVu Sans, sans-serif;
        color: #2c3e50;
        font-size: 12px;
    }

    /* 🔥 ALIGNMENT CLASSES */
    .text-left { text-align: left; }
    .text-center { text-align: center; }
    .text-right { text-align: right; }

    /* 🔥 HEADER */
    .header {
        border-bottom: 2px solid #4CAF50;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    .header table {
        width: 100%;
        border: none;
    }

    .header td { border: none; }

    .title {
        font-size: 20px;
        font-weight: bold;
    }

    .subtitle {
        font-size: 11px;
        color: #555;
    }

    /* 🔥 CARD */
    .card {
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        padding: 12px;
        margin-bottom: 15px;
    }

    /* 🔥 SECTION TITLE */
    .section-title {
        font-weight: bold;
        margin-bottom: 8px;
        color: #2e7d32;
        text-transform: uppercase;
        font-size: 11px;
    }

    /* 🔥 SUMMARY BOX */
    .summary {
        width: 100%;
        border-collapse: separate;
        border-spacing: 5px;
    }

    .summary td {
        background: #f5f9f5;
        padding: 10px;
        border-radius: 8px;
        text-align: center;
        border: 1px solid #e8f5e9;
    }

    .big {
        font-size: 16px;
        font-weight: bold;
        display: block;
        margin-top: 4px;
    }

    /* 🔥 TABLE DATA */
    table.data {
        width: 100%;
        border-collapse: collapse;
        margin-top: 5px;
    }

    table.data th {
        background: #f1f1f1;
        padding: 8px;
        font-size: 10px;
        text-transform: uppercase;
        border-bottom: 2px solid #ddd;
    }

    table.data td {
        padding: 8px;
        font-size: 11px;
        border-bottom: 1px solid #eee;
    }

    /* 🔥 TOTAL ROW */
    .total {
        background: #f9f9f9;
        font-weight: bold;
    }

    .total td {
        border-top: 2px solid #eee;
    }

    /* 🔥 STATUS */
    .surplus { color: green; font-weight: bold; }
    .defisit { color: red; font-weight: bold; }
    .normal { color: blue; font-weight: bold; }

    /* 🔥 FOOTER */
    .footer {
        text-align: center;
        margin-top: 30px;
        font-size: 10px;
        color: #888;
    }
</style>
</head>

<body>

<div class="header">
    <table>
        <tr>
            <td width="70">
                @if(file_exists(public_path('logo.png')))
                    <img src="{{ public_path('logo.png') }}" width="60">
                @endif
            </td>
            <td>
                <div class="title">Laporan Aktivitas & Konsumsi</div>
                <div class="subtitle">
                    Periode {{ ucfirst($filter) }} :
                    {{ $start->format('d M Y') }} - {{ $end->format('d M Y') }} <br>
                    Dicetak: {{ now()->translatedFormat('d M Y H:i') }}
                </div>
            </td>
        </tr>
    </table>
</div>

<div class="card">
    <div class="section-title">Ringkasan Energi</div>

    <table class="summary">
        <tr>
            <td width="33%">
                Kalori Masuk<br>
                <span class="big">{{ number_format($totalKaloriMasuk) }}</span>
            </td>
            <td width="33%">
                Kalori Terbakar<br>
                <span class="big">{{ number_format($totalKaloriTerbakar) }}</span>
            </td>
            <td width="33%">
                Selisih<br>
                <span class="big {{ $selisihKalori > 0 ? 'surplus' : ($selisihKalori < 0 ? 'defisit' : 'normal') }}">
                    {{ $selisihKalori > 0 ? '+' : '' }}{{ number_format($selisihKalori) }}
                </span>
            </td>
        </tr>
    </table>

    <div style="margin-top:10px; text-align: center;">
        Status Energi:
        <b class="{{ $selisihKalori > 0 ? 'surplus' : ($selisihKalori < 0 ? 'defisit' : 'normal') }}">
            {{ $statusKalori }} 
            @if($statusKalori == 'Surplus') &uarr; @elseif($statusKalori == 'Defisit') &darr; @endif
        </b>
    </div>
</div>

<div class="card">
    <div class="section-title">Aktivitas Fisik</div>

    <table class="data">
        <thead>
            <tr>
                <th class="text-center">Tanggal</th>
                <th class="text-left">Aktivitas</th>
                <th class="text-center">Durasi</th>
                <th class="text-right">Kalori</th>
                <th class="text-right">Jarak</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
            <tr>
                <td class="text-center">{{ \Carbon\Carbon::parse($log->tanggal)->format('d/m/Y') }}</td>
                <td class="text-left">{{ $log->jenis }}</td>
                <td class="text-center">{{ $log->durasi }} min</td>
                <td class="text-right">{{ number_format($log->kalori) }}</td>
                <td class="text-right">{{ number_format($log->jarak, 1) }} km</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total">
                <td colspan="2" class="text-left">RINGKASAN AKTIVITAS</td>
                <td class="text-center">{{ $totalDurasi }} min</td>
                <td class="text-right">{{ number_format($totalKaloriTerbakar) }} kcal</td>
                <td class="text-right">{{ number_format($totalJarak, 1) }} km</td>
            </tr>
        </tfoot>
    </table>
</div>

<div class="card">
    <div class="section-title">Konsumsi Makanan</div>

    <table class="data">
        <thead>
            <tr>
                <th class="text-center">Tanggal</th>
                <th class="text-left">Menu</th>
                <th class="text-right">Kalori</th>
                <th class="text-right">Pro (g)</th>
                <th class="text-right">Kar (g)</th>
                <th class="text-right">Lem (g)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($makanan as $item)
            <tr>
                <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                <td class="text-left">{{ $item->resep->nama_makanan ?? 'Menu' }}</td>
                <td class="text-right">{{ number_format($item->kalori_masuk) }}</td>
                <td class="text-right">{{ $item->protein }}</td>
                <td class="text-right">{{ $item->karbo }}</td>
                <td class="text-right">{{ $item->lemak }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total">
                <td colspan="2" class="text-left">TOTAL NUTRISI</td>
                <td class="text-right">{{ number_format($totalKaloriMasuk) }} kcal</td>
                <td class="text-right">{{ number_format($totalProtein, 1) }}</td>
                <td class="text-right">{{ number_format($totalKarbo, 1) }}</td>
                <td class="text-right">{{ number_format($totalLemak, 1) }}</td>
            </tr>
        </tfoot>
    </table>
</div>

<div class="card">
    <div class="section-title">Analisis & Rekomendasi</div>
    <div style="font-style: italic; color: #555; line-height: 1.6;">
        "{{ $rekomendasi }}"
    </div>
</div>

@if($bmi)
<div class="card">
    <div class="section-title">Komposisi Tubuh (BMI)</div>
    <table width="100%">
        <tr>
            <td width="50%">Nilai Index: <b>{{ $bmi }}</b></td>
            <td>Status: <b class="bmi-badge">{{ $statusBMI }}</b></td>
        </tr>
    </table>
</div>
@endif

<div class="footer">
    Laporan ini dihasilkan secara otomatis oleh sistem <b>SehatYuk App</b>.<br>
    Tetap semangat menjaga pola hidup sehat!
</div>

</body>
</html>