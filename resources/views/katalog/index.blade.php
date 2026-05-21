<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Katalog Resep - SehatYuk</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        *{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
        body{background:#cfd8c6;}

        .main{margin-left:120px;padding:20px;}

        .header{
            display:flex;
            justify-content:space-between;
            align-items:center;
        }

        .section{
            margin-top:20px;
            background:#e6efe2;
            border-radius:15px;
            padding:15px;
            box-shadow:0 5px 10px rgba(0,0,0,0.2);
        }

        .stats{
            display:flex;
            gap:10px;
            margin-top:10px;
        }

        .stat{
            flex:1;
            background:white;
            border-radius:12px;
            padding:10px;
            display:flex;
            gap:10px;
            align-items:center;
        }

        .stat .icon{
            width:40px;height:40px;
            border-radius:10px;
            display:flex;
            align-items:center;
            justify-content:center;
        }

        .kalori{background:#ffb74d;}
        .karbo{background:#81c784;}
        .protein{background:#ffd54f;}
        .lemak{background:#64b5f6;}

        .search-bar{
            display:flex;
            gap:10px;
            margin-top:15px;
            align-items: center;
        }

        .search-bar input, .search-bar select{
            padding:8px;
            border-radius:8px;
            border:none;
            outline: none;
        }

        .search-bar input { flex: 2; }
        .search-bar select { flex: 1; cursor: pointer; }

        .table{
            margin-top:15px;
            background:white;
            border-radius:12px;
            overflow:hidden;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        th, td{
            padding:10px;
            font-size:12px;
            text-align:left;
        }

        th{background:#eee;}

        tr:not(:last-child){
            border-bottom:1px solid #ddd;
        }

        tbody tr { transition: all 0.3s ease; }

        .badge{
            padding:5px 10px;
            border-radius:10px;
            font-size:11px;
            color:white;
            display: inline-block;
            text-align: center;
            min-width: 80px;
        }

        .sarapan{background:#8bc34a;}
        .siang{background:#ff9800;}
        .malam{background:#3f51b5;}
        .snack{background:#ff7043;}

        .btn{
            padding:5px 10px;
            border:none;
            border-radius:8px;
            cursor:pointer;
            background:#e0e0e0;
            text-decoration: none;
            color: black;
            display: inline-block;
            font-size: 12px;
        }

        .rekomendasi{
            margin-top:20px;
            background:white;
            border-radius:15px;
            padding:20px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            border: 2px solid #7ed957;
        }

        .rekom-left small {
            color: #4a7c2c;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .rekom-left h2{
            font-size:28px;
            margin-top:5px;
            color: #333;
        }

        .rekom-left p {
            color: #666;
            margin-bottom: 10px;
        }

        .rekom-right img{
            width:150px;
            height:150px;
            object-fit: cover;
            border-radius: 15px;
        }

        .btn-green{
            background:#7ed957;
            padding:10px 20px;
            border:none;
            border-radius:10px;
            margin-top:10px;
            cursor:pointer;
            text-decoration: none;
            color: black;
            display: inline-block;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-green:hover {
            background: #6bc448;
            transform: scale(1.05);
        }

        .meal-check {
            cursor: pointer;
            width: 18px;
            height: 18px;
        }
    </style>
</head>

<body>

@include('components.navbar')

<div class="main">

    <div class="header">
        <h2>Katalog Resep</h2>
    </div>

    <div class="section">
        <p>Pilih makanan untuk menghitung total nutrisi harianmu</p>

        <div class="stats">
            <div class="stat">
                <div class="icon kalori">🔥</div>
                <div>
                    <small>Total kalori</small>
                    <h4 id="totalKalori">0 kcal</h4>
                </div>
            </div>

            <div class="stat">
                <div class="icon karbo">🥗</div>
                <div>
                    <small>Total Karbo</small>
                    <h4 id="totalKarbo">0 gr</h4>
                </div>
            </div>

            <div class="stat">
                <div class="icon protein">🍗</div>
                <div>
                    <small>Total Protein</small>
                    <h4 id="totalProtein">0 gr</h4>
                </div>
            </div>

            <div class="stat">
                <div class="icon lemak">💧</div>
                <div>
                    <small>Total Lemak</small>
                    <h4 id="totalLemak">0 gr</h4>
                </div>
            </div>
        </div>

        <div class="search-bar">
            <div style="display:flex; gap:10px; flex:1;">
                <input type="text" id="searchInput" placeholder="Cari resep sehat...">

                <select id="filterKategori">
                    <option value="Semua">Semua</option>
                    <option value="Sarapan">Sarapan</option>
                    <option value="Makan Siang">Makan Siang</option>
                    <option value="Makan Malam">Makan Malam</option>
                    <option value="Makanan Ringan">Makanan Ringan</option>
                </select>
            </div>

            <button onclick="openFav()" style="background:#ff7675;color:white;border:none;padding:8px 14px;border-radius:10px;cursor:pointer;font-weight:600;">
                ❤️ Favorite
            </button>
        </div>

        <div class="table">
            <table>
                <thead>
                    <tr>
                        <th>Pilih</th>
                        <th>Kategori</th>
                        <th>Menu</th>
                        <th>Kalori</th>
                        <th>Karbo</th>
                        <th>Protein</th>
                        <th>Lemak</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="mealTableBody">
                    @php
                        $meals = $reseps ?? [];
                    @endphp

                    @foreach($meals as $m)
                    <tr>
                        <td>
                            <input type="checkbox" class="meal-check"
                                data-kalori="{{ $m->kalori }}"
                                data-karbo="{{ $m->karbohidrat }}"
                                data-protein="{{ $m->protein }}"
                                data-lemak="{{ $m->lemak }}">
                        </td>
                        <td>
                            @php
                                $kat = strtolower($m->kategori);
                                $map = [
                                    'sarapan' => 'sarapan',
                                    'makan siang' => 'siang',
                                    'makan malam' => 'malam',
                                    'makanan ringan' => 'snack',
                                    'snack' => 'snack'
                                ];
                                $class = $map[$kat] ?? 'snack';
                            @endphp

                            <span class="badge {{ $class }}">
                                {{ $m->kategori ?? 'Lainnya' }}
                            </span>
                        </td>
                        <td>{{ $m->nama_makanan }}</td>
                        <td>{{ $m->kalori }} kcal</td>
                        <td>{{ $m->karbohidrat }} gr</td>
                        <td>{{ $m->protein }} gr</td>
                        <td>{{ $m->lemak }} gr</td>
                        <td><a href="/resep/{{ $m->id }}" class="btn">Lihat Resep</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="rekomendasi">
        <div class="rekom-left">
            <small>✨ Rekomendasi Untuk Anda</small>
            <h2>{{ $rekom->nama_makanan ?? 'Mencari resep...' }}</h2>
            <p>{{ $rekom->kalori ?? 0 }} kcal • Kategori: {{ $rekom->kategori ?? '-' }}</p>
            
            @if($rekom)
            <a href="/resep/{{ $rekom->id }}" class="btn-green">Lihat Resep</a>
            @endif
        </div>
        <div class="rekom-right">
            @if($rekom && $rekom->image)
                <img src="{{ asset('storage/'.$rekom->image) }}" alt="Rekomendasi">
            @else
                <img src="https://placehold.co/150x150?text=SehatYuk" alt="Healthy Food">
            @endif
        </div>
    </div>

</div>

<div id="favModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999;">
    <div style="background:white; width:400px; max-height:80vh; overflow:auto; margin:80px auto; padding:20px; border-radius:15px; position:relative;">
        <h3 style="margin-bottom:15px;">❤️ Resep Favorit</h3>

        @forelse($favorites ?? [] as $f)
            <div style="padding:10px; border-bottom:1px solid #eee; margin-bottom:10px;">
                <b style="font-size:14px;">{{ $f->resep->nama_makanan ?? '-' }}</b><br>
                <small style="color:#777;">{{ $f->resep->kategori ?? '-' }}</small><br>

                <a href="/resep/{{ $f->resep->id ?? '#' }}" 
                   style="display:inline-block;margin-top:8px;background:#7ed957;padding:5px 12px;border-radius:8px;text-decoration:none;color:black;font-size:12px;font-weight:500;">
                   Lihat Resep
                </a>
            </div>
        @empty
            <p style="text-align:center; padding:20px; color:#999;">Belum ada resep favorit</p>
        @endforelse

        <button onclick="closeFav()" style="width:100%;margin-top:10px;padding:10px;background:#ddd;border:none;border-radius:8px;cursor:pointer;font-weight:600;">
            Tutup
        </button>
    </div>
</div>

<script>
// ✅ GUNAKAN NAMED ROUTE UNTUK URL PENCARIAN (DINAMIS)
// Pastikan di web.php sudah ada ->name('search.resep') pada route terkait
const searchUrl = "{{ route('search.resep') }}";

function openFav(){
    document.getElementById('favModal').style.display = 'block';
}

function closeFav(){
    document.getElementById('favModal').style.display = 'none';
}

function updateTotal(){
    let totalKalori = 0, totalKarbo = 0, totalProtein = 0, totalLemak = 0;

    document.querySelectorAll('.meal-check:checked').forEach(cb => {
        totalKalori += parseFloat(cb.dataset.kalori) || 0;
        totalKarbo += parseFloat(cb.dataset.karbo) || 0;
        totalProtein += parseFloat(cb.dataset.protein) || 0;
        totalLemak += parseFloat(cb.dataset.lemak) || 0;
    });

    document.getElementById('totalKalori').innerText = totalKalori.toLocaleString('id-ID') + ' kcal';
    document.getElementById('totalKarbo').innerText = totalKarbo + ' gr';
    document.getElementById('totalProtein').innerText = totalProtein + ' gr';
    document.getElementById('totalLemak').innerText = totalLemak + ' gr';
}

// ✨ UPDATE JAVASCRIPT SEARCH (DINAMIS & ROBUST)
function searchResep(){
    let search = document.getElementById('searchInput').value;
    let kategori = document.getElementById('filterKategori').value;

    // Loading State
    document.getElementById('mealTableBody').innerHTML = `
        <tr>
            <td colspan="8" style="text-align:center;padding:20px;">
                ⏳ Memuat data...
            </td>
        </tr>
    `;

    // ✅ FETCH MENGGUNAKAN NAMED ROUTE
    fetch(`${searchUrl}?search=${encodeURIComponent(search)}&kategori=${encodeURIComponent(kategori)}`)
    .then(res => res.json())
    .then(response => {
        let tableBody = document.getElementById('mealTableBody');
        tableBody.innerHTML = '';

        // Handle error dari backend
        if(response.status === 'error'){
            tableBody.innerHTML = `
                <tr>
                    <td colspan="8" style="text-align:center; padding:20px; color:red;">
                        ❌ ${response.message}
                        <br><br>
                        <button onclick="searchResep()" style="padding:6px 12px;border:none;border-radius:6px;background:#7ed957;cursor:pointer;">
                            Coba Lagi
                        </button>
                    </td>
                </tr>
            `;
            return;
        }

        let data = response.data;

        // Jika data kosong
        if(data.length === 0){
            tableBody.innerHTML = `
                <tr>
                    <td colspan="8" style="text-align:center; padding:20px;">
                        ❌ Maaf, resep dengan bahan "<b>${search || 'yang dicari'}</b>" belum tersedia.
                        <br><br>
                        🔍 Coba kata kunci lain atau kategori berbeda.
                    </td>
                </tr>
            `;
            return;
        }

        // Render Data
        data.forEach(m => {
            let kat = (m.kategori || '').toLowerCase();
            let badgeClass = 'snack';
            if(kat === 'sarapan') badgeClass = 'sarapan';
            else if(kat === 'makan siang') badgeClass = 'siang';
            else if(kat === 'makan malam') badgeClass = 'malam';
            else if(kat === 'makanan ringan' || kat === 'snack') badgeClass = 'snack';

            tableBody.innerHTML += `
                <tr>
                    <td>
                        <input type="checkbox" class="meal-check"
                            data-kalori="${m.kalori}"
                            data-karbo="${m.karbohidrat}"
                            data-protein="${m.protein}"
                            data-lemak="${m.lemak}">
                    </td>
                    <td><span class="badge ${badgeClass}">${m.kategori}</span></td>
                    <td>${m.nama_makanan}</td>
                    <td>${m.kalori} kcal</td>
                    <td>${m.karbohidrat} gr</td>
                    <td>${m.protein} gr</td>
                    <td>${m.lemak} gr</td>
                    <td><a href="/resep/${m.id}" class="btn">Lihat</a></td>
                </tr>
            `;
        });

        // Re-bind listener
        document.querySelectorAll('.meal-check').forEach(cb => {
            cb.addEventListener('change', updateTotal);
        });
    })
    .catch(error => {
        // Handle Error Network
        let tableBody = document.getElementById('mealTableBody');
        tableBody.innerHTML = `
            <tr>
                <td colspan="8" style="text-align:center; padding:20px; color:red;">
                    ❌ Gagal memuat data. Periksa koneksi internet Anda.
                    <br><br>
                    <button onclick="searchResep()" style="padding:6px 12px;border:none;border-radius:6px;background:#7ed957;cursor:pointer;">
                        Coba Lagi
                    </button>
                </td>
            </tr>
        `;
    });
}

// Listeners
document.getElementById('searchInput').addEventListener('keyup', searchResep);
document.getElementById('filterKategori').addEventListener('change', searchResep);

document.querySelectorAll('.meal-check').forEach(cb => {
    cb.addEventListener('change', updateTotal);
});
</script>

</body>
</html>