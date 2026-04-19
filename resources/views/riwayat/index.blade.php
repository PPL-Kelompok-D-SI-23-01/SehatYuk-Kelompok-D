<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat & Laporan | SehatYuk</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #cfd8c6;
            margin: 0;
            color: #2e3a2f;
        }

        .container {
            margin-left: 120px;
            padding: 30px;
        }

        /* CARD MODERN */
        .card {
            background: #f4f7f2;
            border-radius: 18px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.04);
        }

        /* HEADER */
        .header {
            font-size: 20px;
            font-weight: 600;
            color: #2e3a2f;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* FILTER & SEARCH BOX */
        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            flex-wrap: wrap;
            gap: 15px;
        }

        form.filter-form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        /* FORM ELEMENTS */
        input[type="text"], select {
            padding: 10px 15px;
            border-radius: 12px;
            border: 1px solid #d0d7cc;
            background: #ffffff;
            outline: none;
            font-size: 13px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus,
        select:focus {
            border-color: #7ed957;
            box-shadow: 0 0 0 3px rgba(126, 217, 87, 0.1);
        }

        /* BUTTONS */
        .btn {
            padding: 9px 18px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-green {
            background: #7ed957;
            color: white;
        }

        .btn-green:hover {
            background: #6bc94c;
            transform: translateY(-2px);
        }

        .btn-gray {
            background: #e2e5e0;
            color: #333;
        }

        .btn-gray:hover {
            background: #d6dbd2;
        }

        /* TABLE MODERN */
        .table-container {
            overflow-x: auto;
            margin-top: 15px;
            border-radius: 14px;
            background: white;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }

        .table thead {
            background: #e8f0e4;
        }

        .table th {
            padding: 15px;
            font-size: 13px;
            text-align: left;
            color: #4a5a4c;
            font-weight: 600;
        }

        .table td {
            padding: 15px;
            font-size: 13px;
            border-top: 1px solid #f1f1f1;
        }

        .table tr:hover {
            background: #f7faf6;
        }

        /* CHART CONTAINER */
        .chart-card {
            background: white;
            border-radius: 18px;
            padding: 20px;
        }

        .chart-container {
            position: relative;
            height: 280px;
            width: 100%;
        }

        /* MODAL SYSTEM */
        .modal-overlay {
            display:none; 
            position:fixed; 
            top:0; 
            left:0; 
            width:100%; 
            height:100%; 
            background:rgba(0,0,0,0.5); 
            backdrop-filter: blur(4px);
            justify-content:center; 
            align-items:center; 
            z-index:9999;
        }

        .modal-content {
            width:60%; 
            background:white; 
            border-radius:20px; 
            padding:30px; 
            max-height:85%; 
            overflow:auto;
            box-shadow: 0 20px 50px rgba(0,0,0,0.2);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-header h3 { margin: 0; color: #2e3a2f; }

        .close-modal {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #999;
        }

        /* DETAIL CONTENT STYLING */
        #detailContent h4 {
            margin: 20px 0 10px 0;
            border-bottom: 2px solid #f1f1f1;
            padding-bottom: 8px;
            color: #4a7c2c;
            font-size: 15px;
        }

        .detail-item {
            padding: 10px 0;
            font-size: 14px;
            border-bottom: 1px dashed #eee;
            display: flex;
            justify-content: space-between;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .container {
                margin-left: 0;
                padding: 15px;
            }
            .modal-content { width: 90%; }
            .action-bar { flex-direction: column; align-items: stretch; }
            form.filter-form { flex-direction: column; }
        }
    </style>
</head>

<body>

@include('components.navbar')

<div class="container">

    <div class="card">
        <div class="header">📊 Riwayat & Laporan Aktivitas</div>

        <div class="action-bar">
            <form method="GET" action="/riwayat" class="filter-form">
                <input 
                    type="text" 
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari data riwayat..."
                >

                <select name="filter" onchange="this.form.submit()">
                    <option value="harian" {{ request('filter')=='harian'?'selected':'' }}>Harian</option>
                    <option value="mingguan" {{ request('filter')=='mingguan' || !request('filter') ?'selected':'' }}>Mingguan</option>
                    <option value="bulanan" {{ request('filter')=='bulanan'?'selected':'' }}>Bulanan</option>
                </select>
                <button type="submit" class="btn btn-gray">Filter</button>
            </form>

            <button class="btn btn-green" onclick="openPdf()">
                + Cetak PDF Laporan
            </button>
        </div>

        <div class="chart-card card" style="margin-top:20px;">
            <h4 style="margin: 0 0 15px 0; font-size: 14px; color: #7f8c8d; text-transform: uppercase; letter-spacing: 1px;">Statistik Aktivitas</h4>
            <div class="chart-container">
                <canvas id="chart"></canvas>
            </div>
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Aktivitas</th>
                        <th>Durasi</th>
                        <th>Kalori</th>
                        <th>Jarak</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td>{{ $log->tanggal }}</td>
                        <td><strong>{{ $log->jenis }}</strong></td>
                        <td>{{ $log->durasi }} menit</td>
                        <td style="color: #e67e22; font-weight: 500;">{{ $log->kalori }} kcal</td>
                        <td>{{ $log->jarak }} km</td>
                        <td>
                            <button class="btn btn-gray" style="padding: 5px 12px; font-size: 12px;" onclick="showDetail('{{ $log->id }}')">
                                Lihat Detail
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 20px;">
            {{ $logs->links() }}
        </div>
    </div>

</div>

<div id="pdfModal" class="modal-overlay">
    <div style="width:85%; height:90%; background:white; border-radius:20px; overflow:hidden; display:flex; flex-direction:column;">
        <div style="padding:15px 25px; background:#bcd3b0; display:flex; justify-content:space-between; align-items:center; border-bottom: 1px solid #cbd5c0;">
            <b style="font-size:16px;">Preview Laporan PDF</b>
            <button onclick="closePdf()" class="close-modal">&times;</button>
        </div>
        <iframe id="pdfFrame" style="flex:1; border:none; width:100%;"></iframe>
        <div style="padding:15px; background:#f9f9f9; text-align:right;">
            <button class="btn btn-gray" onclick="closePdf()">Tutup Preview</button>
        </div>
    </div>
</div>

<div id="detailModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Rincian Aktivitas Harian</h3>
            <button onclick="closeDetail()" class="close-modal">&times;</button>
        </div>

        <div id="detailContent"></div>

        <div style="margin-top:25px; text-align:right;">
            <button onclick="closeDetail()" class="btn btn-gray">Tutup Rincian</button>
        </div>
    </div>
</div>

<script>
    const kaloriMasukPerHari = JSON.parse('{!! json_encode($dataKaloriMasuk ?? []) !!}');
    const labelsData = JSON.parse('{!! json_encode($labels ?? []) !!}');
    const dataBMI = JSON.parse('{!! json_encode($dataBMI ?? []) !!}');
    const dataKalori = JSON.parse('{!! json_encode($dataKalori ?? []) !!}');
    const dataNutrisi = JSON.parse('{!! json_encode($dataNutrisi ?? []) !!}');
    const targetKalori = parseInt('{{ $targetKaloriMasuk ?? 2000 }}');

    function openPdf() {
        const filter = "{{ request('filter', 'mingguan') }}";
        const search = "{{ request('search', '') }}";
        const url = `/riwayat/pdf?filter=${filter}&search=${search}`;
        document.getElementById('pdfFrame').src = url;
        document.getElementById('pdfModal').style.display = 'flex';
    }

    function closePdf() {
        document.getElementById('pdfModal').style.display = 'none';
        document.getElementById('pdfFrame').src = '';
    }

    // 🔥 FUNCTION JS UPDATED: DARI TANGGAL JADI ID
    function showDetail(id) {
        document.getElementById('detailContent').innerHTML = '<p style="text-align:center; padding:20px;">Mengambil data...</p>';
        document.getElementById('detailModal').style.display = 'flex';

        // 🔥 FETCH UPDATED: DARI TANGGAL JADI ID
        fetch(`/riwayat/detail/${id}`)
        .then(res => res.json())
        .then(data => {
            let html = '';

            // SEKSI RINGKASAN NUTRISI
            html += `<h4>📊 Ringkasan Konsumsi</h4>
            <div class="detail-item"><span>🔥 Kalori</span> <b>${data.total.kalori.toLocaleString()} / ${targetKalori.toLocaleString()} kcal</b></div>
            <div class="detail-item"><span>🥩 Protein</span> <b>${data.total.protein} gr</b></div>
            <div class="detail-item"><span>🍞 Karbohidrat</span> <b>${data.total.karbo} gr</b></div>
            <div class="detail-item"><span>🥑 Lemak</span> <b>${data.total.lemak} gr</b></div>`;

            // SEKSI DAFTAR MAKANAN
            html += `<h4>🍽️ Daftar Makanan</h4>`;
            if(data.makanan.length === 0){
                html += `<p style="font-size:12px; color:#888; font-style:italic;">Belum ada makanan yang dicatat.</p>`;
            } else {
                data.makanan.forEach(item => {
                    html += `
                    <div style="background:#f6faf4; padding:12px 14px; border-radius:12px; margin-bottom:10px; border:1px solid #e0e7dc;">
                        <div style="font-weight:600; margin-bottom:5px;">
                            • ${item.resep?.nama_makanan ?? 'Tanpa Nama'}
                        </div>
                        <div style="font-size:12px; color:#555; display:flex; gap:12px; flex-wrap:wrap;">
                            <span style="background:#e8f5e3; padding:4px 8px; border-radius:8px;">🔥 ${item.kalori_masuk} kcal</span>
                            <span style="background:#e8f5e3; padding:4px 8px; border-radius:8px;">🥩 ${item.protein} g</span>
                            <span style="background:#e8f5e3; padding:4px 8px; border-radius:8px;">🍞 ${item.karbo} g</span>
                            <span style="background:#e8f5e3; padding:4px 8px; border-radius:8px;">🥑 ${item.lemak} g</span>
                        </div>
                    </div>`;
                });
            }

            // SEKSI AKTIVITAS FISIK
            html += `<h4>🏃 Aktivitas Fisik</h4>`;
            if(data.aktivitas.length === 0){
                html += `<p style="font-size:12px; color:#888; font-style:italic;">Tidak ada aktivitas fisik yang dicatat.</p>`;
            } else {
                data.aktivitas.forEach(act => {
                    html += `
                    <div style="background:#f6faf4; padding:12px 14px; border-radius:12px; margin-bottom:10px; border:1px solid #e0e7dc; font-size:13px; color:#333;">
                        • <b>${act.jenis}</b> 
                        - ${act.durasi} menit 
                        | ${act.jarak ?? 0} km 
                        | <b style="color:#ff6b6b;">${act.kalori} kcal</b>
                    </div>`;
                });
            }

            document.getElementById('detailContent').innerHTML = html;
        })
        .catch(err => {
            document.getElementById('detailContent').innerHTML = '<p style="color:red; text-align:center;">Gagal memuat detail data.</p>';
        });
    }

    function closeDetail() {
        document.getElementById('detailModal').style.display = 'none';
    }

    const ctx = document.getElementById('chart');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labelsData,
            datasets: [
                { label: 'BMI', data: dataBMI, backgroundColor: '#ff6b6b', borderRadius: 6 },
                { label: 'Kalori Terbakar', data: dataKalori, backgroundColor: '#feca57', borderRadius: 6 },
                { label: 'Asupan Nutrisi (%)', data: dataNutrisi, backgroundColor: '#1dd1a1', borderRadius: 6 }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { font: { family: 'Poppins', size: 12 } } },
                tooltip: {
                    padding: 12,
                    backgroundColor: 'rgba(46, 58, 47, 0.9)',
                    titleFont: { family: 'Poppins', size: 13 },
                    bodyFont: { family: 'Poppins', size: 12 },
                    callbacks: {
                        label: function(context) {
                            if (context.dataset.label === 'Asupan Nutrisi (%)') {
                                const val = kaloriMasukPerHari[context.dataIndex] ?? 0;
                                return `Nutrisi: ${val.toLocaleString()} / ${targetKalori.toLocaleString()} kcal`;
                            }
                            return `${context.dataset.label}: ${context.raw}`;
                        }
                    }
                }
            },
            scales: { 
                y: { beginAtZero: true, grid: { color: '#f0f0f0' } },
                x: { grid: { display: false } }
            }
        }
    });
</script>

</body>
</html>