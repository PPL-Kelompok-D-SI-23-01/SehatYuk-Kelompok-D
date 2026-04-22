<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $resep->nama_makanan }} - SehatYuk</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        *{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
        body{background:#cfd8c6;}

        .main{margin-left:120px;padding:20px;}

        .wrapper{
            background:#e6efe2;
            border-radius:25px;
            overflow:hidden;
            box-shadow:0 8px 20px rgba(0,0,0,0.2);
        }

        /* HEADER */
        .header{
            background:#bcd3b0;
            padding:15px 20px;
            border-bottom:2px solid #8aa67f;
        }

        .header h3{
            font-weight:600;
        }

        /* TOP */
        .top{
            display:flex;
            gap:25px;
            padding:20px;
            align-items:center;
        }

        /* IMAGE */
        .image img{
            width:200px;
            height:200px;
            border-radius:50%;
            object-fit:cover;
            box-shadow:0 5px 10px rgba(0,0,0,0.3);
        }

        /* INFO */
        .info{
            flex:1;
        }

        .title{
            font-size:30px;
            font-weight:700;
        }

        .desc{
            font-size:13px;
            color:#444;
            margin:5px 0 10px;
        }

        /* ACTION */
        .actions{
            display:flex;
            gap:10px;
            margin-bottom:15px;
            align-items: center;
        }

        .actions button {
            border:1px solid #ccc;
            background:white;
            padding:8px 16px;
            border-radius:12px;
            cursor:pointer;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
            color: #333;
        }

        .actions button:hover {
            background: #f0f0f0;
        }

        .btn-tambah {
            background: #4caf50 !important;
            color: white !important;
            border: none !important;
            font-weight: 500;
        }

        .btn-tambah:hover {
            background: #45a049 !important;
        }


        /* NUTRISI - UPDATED TO 2 COLUMNS */
        .nutrisi{
            display:grid;
            grid-template-columns:repeat(2,1fr);
            gap:10px;
        }

        .nutri-box{
            display:flex;
            gap:10px;
            align-items:center;
            background:white;
            padding:10px;
            border-radius:12px;
            box-shadow:0 2px 5px rgba(0,0,0,0.1);
        }

        .icon{
            width:35px;height:35px;
            border-radius:10px;
            display:flex;
            align-items:center;
            justify-content:center;
            color:white;
            font-size:14px;
        }

        .kalori{background:#ff6b6b;}
        .protein{background:#ffd166;}
        .lemak{background:#4ecdc4;}
        .karbo{background:#6bcf9f;}

        /* BODY */
        .body{
            display:flex;
            gap:20px;
            padding:20px;
            border-top:1px solid #bcd3b0;
        }

        /* BOX */
        .box{
            flex:1;
            background:#f8f8f8;
            border-radius:15px;
            padding:15px;
            min-height:300px;
        }

        .box h4{
            margin-bottom:10px;
            color: #4a7c2c;
        }

        pre {
            white-space: pre-wrap;
            word-wrap: break-word;
            font-size: 13px;
            line-height: 1.6;
            color: #333;
            font-family: inherit;
        }

        /* BACK */
        .back{
            display:inline-block;
            margin:20px 0 20px 20px;
            padding:8px 12px;
            background:#ddd;
            border-radius:10px;
            text-decoration:none;
            color:black;
            font-size: 13px;
        }

        #notif {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #4caf50;
            color: white;
            padding: 14px 28px;
            border-radius: 12px;
            display: none;
            box-shadow: 0 8px 20px rgba(0,0,0,0.25);
            z-index: 9999;
            font-weight: 500;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.4s ease;
        }

        @media(max-width:768px){
            .main{margin-left:0;}
            .top{flex-direction:column; text-align:center;}
            .actions{justify-content: center; flex-wrap: wrap;}
            .body{flex-direction:column;}
            .nutrisi{grid-template-columns: repeat(2, 1fr);}
        }
    </style>
</head>

<body>

@include('components.navbar')

{{-- 🧩 STEP 2 — ERROR POPUP + REDIRECT --}}
@if(session('error'))
<script>
    alert("{{ session('error') }}");
    window.location.href = "/katalog";
</script>
@endif

<div class="main">
    <div class="wrapper">
        <div class="header">
            <h3>Resep Makanan Sehat</h3>
        </div>

        <div class="top">
            <div class="image">
                <img src="{{ $resep->image ? asset('storage/'.$resep->image) : 'https://placehold.co/200x200?text=Food' }}">
            </div>

            <div class="info">
                <div class="title">{{ $resep->nama_makanan }}</div>
                <div class="desc">{{ $resep->deskripsi ?? '-' }}</div>

                {{-- 🧩 DATA TAMBAHAN --}}
                <div style="font-size:13px;margin-bottom:15px; background: rgba(255,255,255,0.4); padding: 10px; border-radius: 10px;">
                    ⏱️ Waktu Masak: <b>{{ $resep->waktu ?? '-' }} menit</b><br>
                    🔥 Tingkat Kesulitan: <b>{{ $resep->kesulitan ?? '-' }}</b><br>
                    🍽️ Porsi: <b>{{ $resep->porsi ?? '-' }} orang</b>
                </div>

                <div class="actions">
                    @php
                        $isFavorite = \App\Models\FavoriteResep::where('user_id', Auth::id())
                                        ->where('meal_id', $resep->id)
                                        ->exists();
                    @endphp

                    <button onclick="shareResep()">🔗 Bagikan</button>

                    <form action="/konsumsi/{{ $resep->id }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn-tambah">
                            🍽️ Tambah ke Ringkasan Nutrisi
                        </button>
                    </form>
                </div>

                {{-- 🧩 NUTRISI (CLEAN VERSION) --}}
                <div class="nutrisi">
                    <div class="nutri-box">
                        <div class="icon kalori">🔥</div>
                        <div><small>Kalori</small><br><b>{{ $resep->kalori }} kcal</b></div>
                    </div>
                    <div class="nutri-box">
                        <div class="icon protein">🍗</div>
                        <div><small>Protein</small><br><b>{{ $resep->protein }} gr</b></div>
                    </div>
                    <div class="nutri-box">
                        <div class="icon lemak">💧</div>
                        <div><small>Lemak</small><br><b>{{ $resep->lemak }} gr</b></div>
                    </div>
                    <div class="nutri-box">
                        <div class="icon karbo">🥗</div>
                        <div><small>Karbo</small><br><b>{{ $resep->karbohidrat }} gr</b></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="body">
            <div class="box">
                <h4>Bahan Utama</h4>
                <pre>{{ $resep->bahan ?? '-' }}</pre>
            </div>
            <div class="box">
                <h4>Cara Membuat</h4>
                <pre>{{ $resep->langkah ?? '-' }}</pre>
            </div>
        </div>

        <div style="padding-bottom: 20px;">
            <a href="/katalog" class="back">← Kembali ke Katalog</a>
            
            {{-- 🧩 RETRY BUTTON --}}
            <button onclick="location.reload()" 
            style="margin-left:10px;padding:8px 12px;border:none;border-radius:10px;background:#7ed957;cursor:pointer; font-size: 13px; font-weight: 500;">
                Muat Ulang
            </button>
        </div>
    </div>
</div>

<div id="notif"></div>

@if(session('success'))
<script>
    document.addEventListener("DOMContentLoaded", function(){
        showNotif("{{ session('success') }}");
    });
</script>
@endif

<script>
window.addEventListener("error", function () {
    alert("Gagal memuat detail resep. Silakan coba lagi.");
});

function showNotif(text, bgColor = '#4caf50'){
    let notif = document.getElementById('notif');
    notif.innerText = text;
    notif.style.backgroundColor = bgColor;
    notif.style.display = 'block';

    setTimeout(()=>{
        notif.style.opacity = '1';
        notif.style.transform = 'translateY(0)';
    }, 10);

    if (window.notifTimeout) clearTimeout(window.notifTimeout);
    
    window.notifTimeout = setTimeout(()=>{
        notif.style.opacity = '0';
        notif.style.transform = 'translateY(20px)';

        setTimeout(()=>{
            notif.style.display = 'none';
        }, 400);
    }, 2800);
}

function shareResep(){
    navigator.clipboard.writeText(window.location.href);
    showNotif('Link berhasil disalin ke clipboard 🔗');
}
</script>

</body>
</html>