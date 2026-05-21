@php use Illuminate\Support\Str; @endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>{{ $artikel->judul ?? 'Detail Artikel' }} - SehatYuk</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}

body{ background:#cfd8c6; }

/* 🔥 SAMA PERSIS DENGAN INDEX */
.main{ margin-left:120px; padding:20px; }

/* 🔥 CARD SAMA */
.card{
    margin-top:20px;
    background:#e6efe2;
    border-radius:15px;
    padding:0;
    box-shadow:0 5px 10px rgba(0,0,0,0.2);
    overflow:hidden;
}

.card-header{
    background:#bcd3b0;
    padding:15px;
    font-weight:600;
}

.card-body{
    padding:20px;
}

/* 🔥 DETAIL STYLE */
.title{
    font-size:20px;
    font-weight:600;
    margin-bottom:10px;
}

.meta{
    font-size:12px;
    color:#666;
    margin-bottom:15px;
}

.divider{
    margin:15px 0;
    border-top:1px solid #ccc;
}

.content{
    font-size:14px;
    line-height:1.8;
    color:#333;
}

/* 🔥 STYLE REKOMENDASI */
.rekomendasi-link {
    display: block;
    color: #2c5e2c;
    text-decoration: none;
    font-size: 14px;
    padding: 5px 0;
    transition: 0.2s;
}

.rekomendasi-link:hover {
    color: #7ed957;
    padding-left: 5px;
}

/* 🔥 BUTTON */
.back{
    display:inline-block;
    margin-top:20px;
    padding:8px 14px;
    border-radius:10px;
    border:none;
    background:#bcd3b0;
    font-size:12px;
    text-decoration:none;
    color:#1f3d1f;
    font-weight: 500;
    transition: 0.3s;
}

.back:hover{
    background: #7ed957;
    color: white;
}

/* 🔥 RESPONSIVE SAMA */
@media(max-width:768px){
    .main{ margin-left:0; }
}
</style>
</head>

<body>

@include('components.navbar')

<div class="main">

    <div class="card">

        <div class="card-header">
            Detail Artikel
        </div>

        <div class="card-body">
            
            {{-- 🔥 STEP 3: VALIDASI JAGA-JAGA --}}
            @if(!$artikel)
                <div style="
                    background: #ffe0e0; 
                    color: #b71c1c; 
                    padding: 15px; 
                    border-radius: 10px; 
                    border: 1px solid #ffcdd2;
                    margin-bottom: 20px;
                    font-size: 14px;
                ">
                    ❌ Maaf, data artikel tidak tersedia atau telah dihapus.
                </div>
            @else

                <div class="title">
                    {{ $artikel->judul }}
                </div>

                {{-- ✅ TAMBAHAN: TAMPILAN GAMBAR EDUKASI --}}
                @if($artikel->gambar_edukasi)
                    <div style="margin:15px 0;">
                        <img src="{{ asset('gambar_artikel/' . $artikel->gambar_edukasi) }}" 
                             style="width:100%; max-height:400px; object-fit:cover; border-radius:10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                    </div>
                @endif

                <div class="meta">
                    Kategori: {{ $artikel->kategori }} 
                    @if($artikel->kondisi) | Kondisi: <b>{{ $artikel->kondisi }}</b> @endif
                </div>

                <div class="divider"></div>

                {{-- 🔥 ISI ARTIKEL --}}
                <div class="content">
                    @if($artikel->isi)
                        {!! nl2br(e($artikel->isi)) !!}
                    @else
                        <i style="color: #999;">Konten belum tersedia.</i>
                    @endif
                </div>

                {{-- ✅ STEP 4: WARNING MEDIS (HANYA UNTUK PANDUAN OLAHRAGA) --}}
                @if(strtolower(trim($artikel->kategori)) == 'panduan olahraga')
                <div style="
                    background:#ffe0e0;
                    padding:12px;
                    border-radius:10px;
                    margin-top:15px;
                    color:#b71c1c;
                    font-size: 13px;
                    border-left: 5px solid #e74c3c;
                ">
                    ⚠️ <b>Peringatan Aktivitas Fisik</b><br>
                    Hentikan olahraga segera jika Anda mengalami:
                    <ul style="margin-top:5px; padding-left:15px;">
                        <li>Pusing atau pandangan kabur</li>
                        <li>Nyeri dada yang tajam</li>
                        <li>Sesak napas berlebihan</li>
                    </ul>
                </div>
                @endif

                {{-- ✅ STEP 5: PANDUAN LAIN (FIXED ARTIKEL VS VIDEO) --}}
                @if(isset($rekomendasi) && $rekomendasi->count())
                <div style="margin-top:25px; padding-top: 15px; border-top: 1px dashed #ccc;">
                    <h4 style="margin-bottom: 10px; font-size: 16px; color: #1f3d1f;">Panduan Lain</h4>

                    @foreach($rekomendasi as $r)
                    <div style="margin-bottom:8px;">
                        @if($r->tipe == 'video')
                            {{-- 🔥 JIKA VIDEO → LANGSUNG KE LINK (YouTube/Lainnya) --}}
                            <a href="{{ Str::startsWith($r->link, 'http') ? $r->link : 'https://' . $r->link }}" 
                               target="_blank" 
                               class="rekomendasi-link">
                                ▶ {{ $r->judul }} <small>(Video)</small>
                            </a>
                        @else
                            {{-- 🔥 JIKA ARTIKEL → KE DETAIL --}}
                            <a href="/edukasi/artikel/{{ $r->id }}" class="rekomendasi-link">
                                📖 {{ $r->judul }}
                            </a>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif

            @endif

            <a href="/edukasi" class="back">← Kembali ke Edukasi</a>

        </div>

    </div>

</div>

</body>
</html>