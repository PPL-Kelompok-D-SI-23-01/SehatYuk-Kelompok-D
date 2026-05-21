<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edukasi - SehatYuk</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
    *{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
    body{ background:#cfd8c6; }
    .main{ margin-left:120px; padding:20px; }
    .header{ display:flex; justify-content:space-between; align-items:center; }
    .card{ margin-top:20px; background:#e6efe2; border-radius:15px; padding:0; box-shadow:0 5px 10px rgba(0,0,0,0.2); overflow:hidden; }
    .card-header{ background:#bcd3b0; padding:15px; font-weight:600; }
    
    .card-body{ 
        padding:20px; 
        position:relative; 
        min-height:350px; 
        display: flex; 
        flex-direction: column; 
    }
    
    /* FILTER */
    .filter{ display:flex; gap:10px; margin-bottom:15px; flex-wrap: wrap; align-items: center; }
    .filter button, .btn{ padding:6px 14px; border-radius:10px; border:1px solid #333; background:#fff; cursor:pointer; font-size:13px; transition: 0.3s; }
    .filter button.active{ background:#bcd3b0; border-color: #bcd3b0; }
    
    .section{ margin-top:15px; }
    .section h4{ margin-bottom:10px; font-size:14px; }
    
    .list a, .list .no-link{ display:block; margin-bottom:12px; color:black; text-decoration:none; transition: 0.2s; background: rgba(255,255,255,0.3); padding: 10px; border-radius: 10px; }
    .list a:hover{ transform: translateX(5px); background: rgba(255,255,255,0.6); }
    .list b{ font-weight:600; font-size: 14px; color: #2d3436; }
    .list small{ color: #666; font-size: 11px; display: block; text-transform: capitalize; margin-top: 2px; }
    
    .btn-small{ display: inline-block; margin-top:10px; padding:6px 12px; border-radius:8px; border:none; background:#bcd3b0; color: #1f3d1f; font-size:12px; cursor:pointer; text-decoration: none; font-weight: 500; transition: 0.3s; width: fit-content; }
    .btn-small:hover{ background: #7ed957; color: white; }
    .btn-small:disabled{ background: #a5a5a5; cursor: not-allowed; opacity: 0.7; }

    .count{ 
        position: static; 
        margin-top: auto; 
        padding-top: 30px;
        display: flex; 
        flex-direction: row; 
        gap: 10px; 
        z-index: 10;
    }
    
    .count-box{ 
        padding:10px 15px; 
        border-radius:10px; 
        background:#bcd3b0; 
        font-weight:500; 
        text-align:center; 
        min-width: 120px; 
        box-shadow: 0 3px 6px rgba(0,0,0,0.1); 
    }
    .count-box:last-child{ background:#eee; }

    .gi-table { width:100%; border-collapse:collapse; background: white; border-radius: 10px; overflow: hidden; margin-top: 10px; }
    .gi-table th { background: #f1f1f1; padding: 12px; text-align: left; font-size: 12px; text-transform: uppercase; }
    .gi-table td { padding: 12px; border-bottom: 1px solid #eee; font-size: 13px; }

    .food-card {
        background: white; 
        padding: 15px; 
        border-radius: 15px; 
        width: 200px; 
        box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        transition: 0.3s;
    }
    .food-card:hover { transform: translateY(-5px); }

    /* Style Live Search Input */
    .live-search-input { width:100%; padding:8px 12px; border-radius:10px; border:1px solid #ccc; margin-bottom:15px; outline: none; }
    .live-search-input:focus { border-color: #bcd3b0; box-shadow: 0 0 5px rgba(188, 211, 176, 0.5); }

    @media(max-width:768px){ 
        .main{ margin-left:0; } 
        .count{ margin-top:15px; } 
    }
</style>
</head>
<body>

@include('components.navbar')

<div class="main">
    <div class="header">
        <div>
            <h3>Hi, {{ Auth::user()->name }} 👋</h3>
            <small>{{ now()->translatedFormat('l, d F Y') }}</small>
        </div>
    </div>

    @if(session('error'))
    <div style="background:#ffe0e0; padding:12px; border-radius:10px; margin-top:15px; color:#b71c1c; border:1px solid #ffcdd2; font-size: 14px;">
        {{ session('error') }}
        <div style="margin-top:8px;">
            <a href="/edukasi" style="font-size:12px; background:#bcd3b0; padding:5px 10px; border-radius:6px; text-decoration:none; color:black; font-weight: 600; display: inline-block;">
                🔄 Coba Lagi
            </a>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-header">Pusat Edukasi & Panduan</div>
        <div class="card-body">
            
            <div class="filter">
                <button class="active" onclick="filterData('all', this)">Semua</button>
                <button onclick="filterData('artikel umum', this)">Artikel Umum</button>
                
                <a href="/edukasi" class="btn-small" style="margin-top: 0;">Reset</a>
            </div>

            <input type="text" id="liveSearch" class="live-search-input" placeholder="Cari artikel & video..." autocomplete="off">

            <div class="section" id="artikelSection">
                <h4>Artikel & Panduan 📖</h4>
                <div class="list" id="artikelList">
                    @forelse($articles as $a)
                    <a href="/edukasi/artikel/{{ $a->id }}" data-kategori="{{ trim(strtolower($a->kategori)) }}">
                        <b>{{ $a->judul }}</b>
                        <small>Kategori: {{ $a->kategori }}</small>
                    </a>
                    @empty
                    @endforelse
                </div>
                
                <button id="loadMoreArtikel" class="btn-small">Load More Artikel</button>

                <div id="emptySemua" style="display:none; padding:20px; text-align:center; color:#888;">
                    <div style="font-size:30px;">📚</div>
                    <div>Belum ada data edukasi tersedia</div>
                </div>
            </div>

            <hr id="dividerLine" style="margin: 20px 0; border: 0; border-top: 1px solid #ccc;">

            <div class="section" id="videoSection">
                <h4>Video Edukasi ▶</h4>
                <div class="list" id="videoList">
                    @forelse($videos as $v)
                        @php $link = $v->link ? (Str::startsWith($v->link, 'http') ? $v->link : 'https://' . $v->link) : null; @endphp
                        @if($link)
                            <a href="{{ $link }}" target="_blank" data-kategori="{{ trim(strtolower($v->kategori)) }}">
                                <b>{{ $v->judul }}</b>
                                <small>Kategori: {{ $v->kategori }}</small>
                            </a>
                        @else
                            <div class="no-link" style="opacity:0.7;" data-kategori="{{ trim(strtolower($v->kategori)) }}">
                                <b>{{ $v->judul }}</b>
                                <small>Kategori: {{ $v->kategori }}</small>
                                <div style="font-size:11px; color:#e74c3c;">⚠️ Link belum tersedia</div>
                            </div>
                        @endif
                    @empty
                    @endforelse
                </div>
                <button id="loadMoreVideo" class="btn-small">Load More Video</button>
            </div>

            <div class="count">
                <div class="count-box">{{ \App\Models\Artikel::where('tipe','artikel')->count() }} Artikel</div>
                <div class="count-box">{{ \App\Models\Artikel::where('tipe','video')->count() }} Video</div>
            </div>
        </div>
    </div>
</div>

<script>
// 🔥 SCRIPT LIVE SEARCH (FINAL SOLUTION: AJAX FETCH ON EMPTY)
let timeout = null;

document.getElementById('liveSearch').addEventListener('keyup', function(){
    clearTimeout(timeout);
    let keyword = this.value;

    // 🔥 SOLUSI FINAL: Jika keyword kosong, fetch ulang data kategori aktif tanpa reload
    if(keyword === ''){
        let kategoriAktif = document.querySelector('.filter button.active').innerText.toLowerCase();

        fetch(`/edukasi/live-search?keyword=&kategori=${kategoriAktif}`)
        .then(res => res.json())
        .then(data => {
            const artikelList = document.getElementById('artikelList');
            const videoList = document.getElementById('videoList');

            // 🔥 reset isi
            artikelList.innerHTML = '';
            videoList.innerHTML = '';

            // 🔥 render artikel
            if(data.articles.length > 0){
                data.articles.forEach(a => {
                    artikelList.innerHTML += `
                        <a href="/edukasi/artikel/${a.id}">
                            <b>${a.judul}</b>
                            <small>Kategori: ${a.kategori}</small>
                        </a>
                    `;
                });
            }

            // 🔥 render video
            if(data.videos.length > 0){
                data.videos.forEach(v => {
                    if(v.link){
                        let link = v.link.startsWith('http') ? v.link : 'https://' + v.link;
                        videoList.innerHTML += `
                            <a href="${link}" target="_blank">
                                <b>${v.judul}</b>
                                <small>Kategori: ${v.kategori}</small>
                            </a>
                        `;
                    } else {
                        videoList.innerHTML += `
                            <div class="no-link" style="opacity:0.7;">
                                <b>${v.judul}</b>
                                <small>Kategori: ${v.kategori}</small>
                                <div style="font-size:11px; color:#e74c3c;">⚠️ Link belum tersedia</div>
                            </div>
                        `;
                    }
                });
            }

            // 🔥 tampilkan tombol load more lagi
            document.getElementById('loadMoreArtikel').style.display = 'block';
            document.getElementById('loadMoreVideo').style.display = 'block';
        });
        return;
    }

    // 🔥 Reset list sebelum mulai pencarian baru (agar loading terlihat bersih)
    document.getElementById('artikelList').innerHTML = '';
    document.getElementById('videoList').innerHTML = '';

    timeout = setTimeout(() => {
        document.getElementById('loadMoreArtikel').style.display = 'none';
        document.getElementById('loadMoreVideo').style.display = 'none';

        let kategoriAktif = document.querySelector('.filter button.active').innerText.toLowerCase();

        fetch(`/edukasi/live-search?keyword=${keyword}&kategori=${kategoriAktif}`)
        .then(res => res.json())
        .then(data => {
            const artikelList = document.getElementById('artikelList');
            const videoList = document.getElementById('videoList');
            
            if(data.articles.length > 0){
                data.articles.forEach(a => {
                    artikelList.innerHTML += `
                        <a href="/edukasi/artikel/${a.id}">
                            <b>${a.judul}</b>
                            <small>Kategori: ${a.kategori}</small>
                        </a>
                    `;
                });
            } else {
                artikelList.innerHTML = '<p style="font-size:12px; color:#888;">Artikel tidak ditemukan.</p>';
            }

            if(data.videos.length > 0){
                data.videos.forEach(v => {
                    if(v.link){
                        let link = v.link.startsWith('http') ? v.link : 'https://' + v.link;
                        videoList.innerHTML += `
                            <a href="${link}" target="_blank">
                                <b>${v.judul}</b>
                                <small>Kategori: ${v.kategori}</small>
                            </a>
                        `;
                    } else {
                        videoList.innerHTML += `
                            <div class="no-link" style="opacity:0.7;">
                                <b>${v.judul}</b>
                                <small>Kategori: ${v.kategori}</small>
                                <div style="font-size:11px; color:#e74c3c;">⚠️ Link belum tersedia</div>
                            </div>
                        `;
                    }
                });
            } else {
                videoList.innerHTML = '<p style="font-size:12px; color:#888;">Video tidak ditemukan.</p>';
            }
        });
    }, 400);
});

function filterData(kategori, el = null){
    if(el) {
        document.querySelectorAll('.filter button').forEach(btn => btn.classList.remove('active'));
        el.classList.add('active');
    }

    const sections = ['artikelSection', 'videoSection', 'dividerLine'];
    const isGI = (kategori === 'Kamus Indeks Glikemik (GI)' || kategori === 'kamus indeks glikemik (gi)');
    const searchInput = document.getElementById('liveSearch');

    searchInput.style.display = isGI ? 'none' : 'block';
    
    sections.forEach(id => document.getElementById(id).style.display = isGI ? 'none' : 'block');
    document.getElementById('kamusSection').style.display = isGI ? 'block' : 'none';
    if(document.getElementById('rekomendasiSection')) document.getElementById('rekomendasiSection').style.display = isGI ? 'block' : 'none';

    if(!isGI){
        const filterKat = kategori.toLowerCase();
        document.querySelectorAll('#artikelList a, #videoList a, #videoList .no-link').forEach(item => {
            let dataKat = item.getAttribute('data-kategori')?.toLowerCase();
            item.style.display = (filterKat === 'all' || filterKat === 'semua' || dataKat === filterKat) ? 'block' : 'none';
        });
    }
}

// Logic Load More
document.getElementById('loadMoreArtikel')?.addEventListener('click', function(){
    let btn = this;
    btn.disabled = true;
    btn.innerHTML = "⏳ Loading...";
    let currentCount = document.querySelectorAll('#artikelList a').length;
    fetch(`/load-more?offset=${currentCount}&tipe=artikel&kategori=`)
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerText = "Load More Artikel";
        if(data.length === 0){ btn.innerText = "Habis"; btn.disabled = true; return; }
        data.forEach(a => {
            document.getElementById('artikelList').insertAdjacentHTML('beforeend', `<a href="/edukasi/artikel/${a.id}" data-kategori="${a.kategori.toLowerCase()}"><b>${a.judul}</b><small>Kategori: ${a.kategori}</small></a>`);
        });
    });
});

document.getElementById('loadMoreVideo')?.addEventListener('click', function(){
    let btn = this;
    btn.disabled = true;
    btn.innerHTML = "⏳ Loading...";
    let currentCount = document.querySelectorAll('#videoList > div, #videoList > a').length;
    fetch(`/load-more?offset=${currentCount}&tipe=video&kategori=`)
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerText = "Load More Video";
        if(data.length === 0){ btn.innerText = "Habis"; btn.disabled = true; return; }
        data.forEach(v => {
            let html = v.link ? `<a href="${v.link.startsWith('http') ? v.link : 'https://'+v.link}" target="_blank"><b>${v.judul}</b><small>Kategori: ${v.kategori}</small></a>` : `<div class="no-link" style="opacity:0.7;"><b>${v.judul}</b><small>Kategori: ${v.kategori}</small><div style="font-size:11px; color:#e74c3c;">⚠️ Link belum tersedia</div></div>`;
            document.getElementById('videoList').insertAdjacentHTML('beforeend', html);
        });
    });
});
</script>
</body>
</html>