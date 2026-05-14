<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - SehatYuk</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        *{ margin:0; padding:0; box-sizing:border-box; font-family:'Poppins',sans-serif; }
        body{ background:#cfd8c6; }
        .main{ margin-left:120px; padding:30px; }
        .header{ display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
        .header h2{ font-weight:600; }
        .header small{ color:#666; }
        .panel{ background:#eef5ea; border-radius:20px; padding:25px; box-shadow:0 8px 20px rgba(0,0,0,0.08); }
        .top-bar{ display:flex; flex-wrap:wrap; justify-content:space-between; gap:15px; margin-bottom:20px; }
        .search{ padding:10px 14px; border-radius:10px; border:1px solid #ddd; outline:none; width:220px; }
        .search:focus{ border-color:#7ed957; }
        .btn{ padding:8px 14px; border:none; border-radius:10px; cursor:pointer; font-size:13px; }
        .green{ background:#7ed957; color:#fff; }
        .gray{ background:#e0e0e0; }
        .tabs{ display:flex; gap:10px; background:#e4ebdf; padding:5px; border-radius:12px; }
        .tab{ padding:8px 14px; border-radius:10px; cursor:pointer; font-size:13px; }
        .tab.active{ background:#7ed957; color:white; }
        .table{ background:white; border-radius:15px; overflow:hidden; }
        table{ width:100%; border-collapse:collapse; }
        th{ background:#f5f5f5; padding:12px; font-size:12px; text-align: left; }
        td{ padding:12px; font-size:13px; border-bottom:1px solid #eee; }
        .action{ display:flex; gap:8px; align-items: center; }
        .edit{ background:#555; color:white; padding:6px 10px; border-radius:8px; border:none; cursor:pointer; display: inline-flex; align-items: center; }
        .delete{ background:#f44336; color:white; padding:6px 10px; border-radius:8px; border:none; cursor:pointer; }
        .section{display:none;}
        .section.active{display:block;}
        .modal{ display:none; position:fixed; top:0;left:0; width:100%;height:100%; background:rgba(0,0,0,0.4); z-index:999; }
        .modal-content{ background:white; width:450px; margin:30px auto; padding:25px; border-radius:20px; max-height: 90vh; overflow-y: auto; }
        .modal-content input, .modal-content select, .modal-content textarea{ width:100%; padding:10px; margin-top:10px; border-radius:10px; border:1px solid #ddd; }
        .preview{ margin-top:10px; text-align:center; }
        .preview img{ width:100px; height:100px; border-radius:12px; }
        input[readonly] { background-color: #f0f0f0; cursor: not-allowed; color: #777; font-weight: bold; }
        #userDetail p { margin-bottom: 10px; font-size: 14px; border-bottom: 1px solid #f9f9f9; padding-bottom: 5px; }

        /* CSS BARU — CUSTOM PAGINATION */
        .custom-pagination{
            display:flex;
            align-items:center;
            gap:8px;
            margin-top:20px;
            flex-wrap:wrap;
        }

        .custom-pagination a,
        .custom-pagination span{
            padding:8px 14px;
            border-radius:10px;
            font-size:13px;
            text-decoration:none;
            transition:0.2s;
        }

        .custom-pagination a{
            background:white;
            color:#333;
            border:1px solid #ddd;
        }

        .custom-pagination a:hover{
            background:#7ed957;
            color:white;
            border-color:#7ed957;
        }

        .custom-pagination .active{
            background:#7ed957;
            color:white;
            font-weight:600;
        }

        .custom-pagination .disabled{
            background:#eee;
            color:#999;
            cursor:not-allowed;
        }
    </style>
</head>
<body>

@include('components.navbar')

<div class="main">
    <div class="header">
        <div>
            <h2>👤 Admin Panel</h2>
            <small>{{ now()->translatedFormat('l, d F Y') }}</small>
        </div>
    </div>

    {{-- NOTIFIKASI --}}
    @if(session('success'))
        <div style="background:#d4edda; color:#155724; padding:12px; border-radius:10px; margin-bottom:15px; border:1px solid #c3e6cb; font-size:14px;">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background:#f8d7da; color:#721c24; padding:12px; border-radius:10px; margin-bottom:15px; border:1px solid #f5c6cb; font-size:14px;">
            ❌ {{ session('error') }}
        </div>
    @endif

    <div class="panel">

        @php
            $articles = $artikels ?? [];
            $reseps = $reseps ?? [];
            $users = $users ?? [];
        @endphp

        <div class="top-bar">
            <div>
                <form method="GET" action="/admin" style="display:flex; gap:10px;" id="filterForm">
                    <input type="hidden" name="tab" id="tabInput" value="{{ request('tab', 'pengguna') }}">
                    
                    <input 
                        type="text" 
                        id="searchInput"
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="@if(request('tab') == 'artikel') Cari judul, kategori, tipe... @elseif(request('tab') == 'resep') Cari resep, kategori, GI... @else Cari user, email... @endif" 
                        class="search"
                    >

                    @if(request('tab', 'pengguna') == 'pengguna')
                    <input 
                        type="number" 
                        id="umurInput"
                        name="umur" 
                        value="{{ request('umur') }}" 
                        placeholder="Filter umur" 
                        class="search angka-only" 
                        style="width: 120px;"
                    >
                    @endif
                    
                    <a href="/admin" class="btn gray" style="text-decoration: none; display: inline-flex; align-items: center;">Reset</a>
                </form>
            </div>

            <div class="tabs">
                <div class="tab active" data-tab="pengguna" onclick="showTab(event,'pengguna')">Pengguna</div>
                <div class="tab" data-tab="artikel" onclick="showTab(event,'artikel')">Artikel</div>
                <div class="tab" data-tab="resep" onclick="showTab(event,'resep')">Resep</div>
            </div>

            <div>
                <button class="btn green" onclick="handleAdd()">+ Add Data</button>
            </div>
        </div>

        {{-- TAB ARTIKEL --}}
        <div id="artikel" class="section">
            <div class="table">
                <table>
                    <thead><tr><th>Judul</th><th>Tipe</th><th>Kategori</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @foreach($articles as $a)
                        <tr>
                            <td>{{ $a->judul }}</td>
                            <td>{{ ucfirst($a->tipe) }}</td>
                            <td>{{ $a->kategori }}</td>
                            <td class="action">
                                <button class="edit" onclick="prepareEditArtikel(this)"
                                    data-id="{{ $a->id }}" 
                                    data-judul="{{ $a->judul }}"
                                    data-tipe="{{ $a->tipe }}" 
                                    data-konten="{{ $a->isi }}"
                                    data-kategori="{{ $a->kategori }}" 
                                    data-link="{{ $a->link }}"
                                    data-gambar="{{ $a->gambar_edukasi }}">✏</button>

                                <form action="/admin/artikel/{{ $a->id }}" method="POST" style="display: inline;">
                                    @csrf @method('DELETE')
                                    <button onclick="return confirm('Yakin hapus?')" class="delete">🗑</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION ARTIKEL --}}
            @if($articles->hasPages())
            <div class="custom-pagination">
                @if($articles->onFirstPage())
                    <span class="disabled">‹ Previous</span>
                @else
                    <a href="{{ $articles->previousPageUrl() }}&tab=artikel">‹ Previous</a>
                @endif

                @for($i = 1; $i <= $articles->lastPage(); $i++)
                    @if($i == $articles->currentPage())
                        <span class="active">{{ $i }}</span>
                    @else
                        <a href="{{ $articles->url($i) }}&tab=artikel">{{ $i }}</a>
                    @endif
                @endfor

                @if($articles->hasMorePages())
                    <a href="{{ $articles->nextPageUrl() }}&tab=artikel">Next ›</a>
                @else
                    <span class="disabled">Next ›</span>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

{{-- MODAL DETAIL USER --}}
<div id="modalUser" class="modal">
    <div class="modal-content">
        <h3 style="border-bottom: 2px solid #7ed957; padding-bottom: 10px; margin-bottom: 15px;">Detail Pengguna</h3>
        <div id="userDetail"></div>
        <div style="margin-top:20px; text-align: right;">
            <button class="btn gray" onclick="closeUser()">Tutup</button>
        </div>
    </div>
</div>

{{-- MODAL RESEP --}}
<div id="modal" class="modal">
    <div class="modal-content">
        <h3 id="modalTitle">Tambah Resep</h3>
        <form id="formResep" method="POST" action="/admin/resep" enctype="multipart/form-data">
            @csrf
            <div id="methodContainerResep"></div>
            <input name="nama_makanan" id="res_nama" placeholder="Nama Makanan" required>
            <select name="kategori" id="res_kategori" required>
                <option value="">-- Pilih Kategori --</option>
                <option value="Sarapan">Sarapan</option>
                <option value="Makan Siang">Makan Siang</option>
                <option value="Makan Malam">Makan Malam</option>
                <option value="Makanan Ringan">Makanan Ringan</option>
                <option value="Lainnya">Lainnya</option>
            </select>
            
            <select name="gi" id="res_gi">
                <option value="">-- Pilih Indeks Glikemik (GI) --</option>
                <option value="rendah">Rendah</option>
                <option value="sedang">Sedang</option>
                <option value="tinggi">Tinggi</option>
            </select>

            <input 
                type="number"
                step="0.1"
                min="0"
                inputmode="decimal"
                name="protein"
                id="res_protein"
                class="angka-only"
                placeholder="Protein (gr)"
                required
                oninput="hitungKalori()"
            >
            <input 
                type="number"
                step="0.1"
                min="0"
                inputmode="decimal"
                name="karbohidrat"
                id="res_karbohidrat"
                class="angka-only"
                placeholder="Karbohidrat (gr)"
                required
                oninput="hitungKalori()"
            >
            <input 
                type="number"
                step="0.1"
                min="0"
                inputmode="decimal"
                name="lemak"
                id="res_lemak"
                class="angka-only"
                placeholder="Lemak (gr)"
                required
                oninput="hitungKalori()"
            >
            
            <input name="kalori" id="res_kalori" placeholder="Total Kalori (Auto)" readonly>
            
            <input type="number" name="waktu" id="res_waktu" class="angka-only" placeholder="Waktu Masak (menit)">
            <select name="kesulitan" id="res_kesulitan">
                <option value="">-- Tingkat Kesulitan --</option>
                <option value="Mudah">Mudah</option>
                <option value="Sedang">Sedang</option>
                <option value="Sulit">Sulit</option>
            </select>
            <input type="number" name="porsi" id="res_porsi" class="angka-only" placeholder="Jumlah Porsi">
            <input type="date" name="tanggal" id="res_tanggal" required>
            <input type="file" name="image" onchange="previewImage(event)">
            <div class="preview"><img id="previewImg" src="" style="display:none;"></div>
            <textarea name="deskripsi" id="res_deskripsi" placeholder="Deskripsi makanan..."></textarea>
            <textarea name="bahan" id="res_bahan" placeholder="Bahan (pisahkan per baris)..."></textarea>
            <textarea name="langkah" id="res_langkah" placeholder="Langkah memasak (pisahkan per baris)..."></textarea>
            <div style="margin-top:15px; display:flex; gap:10px;">
                <button type="submit" class="btn green">Simpan</button>
                <button type="button" class="btn gray" onclick="closeModal()">Batal</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL ARTIKEL --}}
<div id="modalArtikel" class="modal">
    <div class="modal-content">
        <h3 id="modalArtikelTitle">Tambah Artikel / Video</h3>
        <form method="POST" id="formArtikelUtama" action="/admin/artikel" enctype="multipart/form-data">
            @csrf
            <div id="methodContainerArtikel"></div>
            
            <input type="hidden" name="id" id="artikelId">
            <input name="judul" id="inputJudul" placeholder="Judul" required>
            <select name="tipe" id="selectTipe">
                <option value="artikel">Artikel</option>
                <option value="video">Video</option>
            </select>
            <select name="kategori" id="selectKategori" required>
                <option value="">-- Pilih Kategori --</option>
                <option value="artikel umum">Artikel Umum</option>
                <option value="panduan olahraga">Panduan Olahraga</option>
            </select>
            <div id="formArtikel">
                <textarea name="isi" id="inputKonten" placeholder="Isi artikel..."></textarea>
            </div>
            <div id="formVideo" style="display:none;">
                <input name="link" id="inputLink" placeholder="Link Video / Konten">
            </div>

            <div style="margin-top:10px;">
                <label style="font-size: 13px; color: #333; font-weight: 500;">Gambar Artikel</label>
                <input type="file" name="gambar_edukasi" id="gambarInput" onchange="previewGambarArtikel(event)">

                <div class="preview" style="margin-top:10px;">
                    <img id="previewGambarArtikel" style="display:none; width:100%; max-height:150px; object-fit:cover; border-radius:10px; border: 1px solid #ddd;">
                </div>
            </div>

            <div style="margin-top:15px; display:flex; gap:10px;">
                <button type="submit" class="btn green">Simpan</button>
                <button type="button" class="btn gray" onclick="closeModalArtikel()">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
    function showUser(id){
        document.getElementById('modalUser').style.display = 'block';
        document.getElementById('userDetail').innerHTML = '<p style="text-align:center;">Memuat data...</p>';
        fetch('/admin/user/' + id)
        .then(res => res.json())
        .then(data => {
            let html = `
                <p><b>Nama:</b> ${data.name}</p>
                <p><b>Email:</b> ${data.email}</p>
                <p><b>Umur:</b> ${data.client?.umur ?? '-'} tahun</p>
                <p><b>Jenis Kelamin:</b> ${data.client?.gender ?? '-'}</p>
                <p><b>Berat Badan:</b> ${data.client?.berat ?? '-'} kg</p>
                <p><b>Tinggi Badan:</b> ${data.client?.tinggi ?? '-'} cm</p>
                <p><b>Dibuat Pada:</b> ${new Date(data.created_at).toLocaleDateString('id-ID')}</p>
            `;
            document.getElementById('userDetail').innerHTML = html;
        })
        .catch(() => {
            document.getElementById('userDetail').innerHTML = '<p style="color:red;">Gagal memuat data</p>';
        });
    }

    function closeUser(){
        document.getElementById('modalUser').style.display = 'none';
    }

    function setActiveTab(tab){
        document.querySelectorAll('.section').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.tab').forEach(el => el.classList.remove('active'));
        const section = document.getElementById(tab);
        if(section){section.classList.add('active');}
        document.querySelectorAll('.tab').forEach(el=>{ 
            if(el.dataset.tab === tab){el.classList.add('active');} 
        });
        document.getElementById('tabInput').value = tab;
    }

    function showTab(e, tab){
        const url = new URL(window.location);
        url.searchParams.set('tab', tab);
        url.searchParams.delete('search');
        url.searchParams.delete('umur');
        // Reset page parameters when switching tabs to avoid confusion
        url.searchParams.delete('users_page');
        url.searchParams.delete('artikel_page');
        url.searchParams.delete('resep_page');
        window.location.replace(url.href);
    }

    function hitungKalori() {
        let karbo = parseFloat(document.getElementById('res_karbohidrat').value) || 0;
        let protein = parseFloat(document.getElementById('res_protein').value) || 0;
        let lemak = parseFloat(document.getElementById('res_lemak').value) || 0;
        let total = (karbo * 4) + (protein * 4) + (lemak * 9);
        document.getElementById('res_kalori').value = Math.round(total);
    }

    function openModal(){
        document.getElementById('modal').style.display='block';
        document.getElementById('formResep').reset();
        document.getElementById('formResep').action = '/admin/resep';
        document.getElementById('modalTitle').innerText = 'Tambah Resep';
        document.getElementById('methodContainerResep').innerHTML = '';
        document.getElementById('previewImg').style.display = 'none';
    }

    function editResep(btn){
        openModal();
        document.getElementById('modalTitle').innerText = 'Edit Resep';
        document.getElementById('res_nama').value = btn.dataset.nama;
        document.getElementById('res_kategori').value = btn.dataset.kategori || '';
        document.getElementById('res_gi').value = btn.dataset.gi || '';
        document.getElementById('res_kalori').value = btn.dataset.kalori;
        document.getElementById('res_protein').value = btn.dataset.protein;
        document.getElementById('res_karbohidrat').value = btn.dataset.karbohidrat;
        document.getElementById('res_lemak').value = btn.dataset.lemak;
        document.getElementById('res_tanggal').value = btn.dataset.tanggal;
        document.getElementById('res_deskripsi').value = btn.dataset.deskripsi || '';
        document.getElementById('res_bahan').value = btn.dataset.bahan || '';
        document.getElementById('res_langkah').value = btn.dataset.langkah || '';
        document.getElementById('res_waktu').value = btn.dataset.waktu || '';
        document.getElementById('res_kesulitan').value = btn.dataset.kesulitan || '';
        document.getElementById('res_porsi').value = btn.dataset.porsi || '';
        document.getElementById('formResep').action = '/admin/resep/' + btn.dataset.id;
        document.getElementById('methodContainerResep').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    }

    function closeModal(){document.getElementById('modal').style.display='none';}
    function closeModalArtikel(){document.getElementById('modalArtikel').style.display='none';}

    function openModalArtikel(){
        document.getElementById('modalArtikel').style.display='block';
        const form = document.getElementById('formArtikelUtama');
        form.reset();
        form.action = '/admin/artikel';
        document.getElementById('methodContainerArtikel').innerHTML = '';
        document.getElementById('modalArtikelTitle').innerText = 'Tambah Artikel';
        document.getElementById('selectTipe').disabled = false;
        document.getElementById('previewGambarArtikel').style.display = 'none';
        toggleForm('artikel');
    }

    function prepareEditArtikel(btn){
        const id = btn.dataset.id;
        document.getElementById('modalArtikel').style.display='block';
        document.getElementById('modalArtikelTitle').innerText = 'Edit Artikel';
        document.getElementById('inputJudul').value = btn.dataset.judul;
        document.getElementById('selectTipe').value = btn.dataset.tipe;
        document.getElementById('selectKategori').value = btn.dataset.kategori;
        toggleForm(btn.dataset.tipe);
        if(btn.dataset.tipe === 'artikel'){
            document.getElementById('inputKonten').value = btn.dataset.konten;
        } else {
            document.getElementById('inputLink').value = btn.dataset.link;
        }
        const form = document.getElementById('formArtikelUtama');
        form.action = '/admin/artikel/' + id;
        document.getElementById('methodContainerArtikel').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        let imgPreview = document.getElementById('previewGambarArtikel');
        if(btn.dataset.gambar){
            imgPreview.src = '/gambar_artikel/' + btn.dataset.gambar;
            imgPreview.style.display = 'block';
        }
    }

    function toggleForm(val){
        document.getElementById('formArtikel').style.display = (val === 'artikel') ? 'block' : 'none';
        document.getElementById('formVideo').style.display = (val === 'video') ? 'block' : 'none';
    }

    document.getElementById('selectTipe').addEventListener('change', function(){
        toggleForm(this.value);
    });

    function previewImage(event){
        let img = document.getElementById('previewImg');
        if(event.target.files.length > 0){
            img.src = URL.createObjectURL(event.target.files[0]);
            img.style.display = 'block';
        }
    }

    function previewGambarArtikel(event){
        let img = document.getElementById('previewGambarArtikel');
        if(event.target.files.length > 0){
            img.src = URL.createObjectURL(event.target.files[0]);
            img.style.display = 'block';
        }
    }

    function handleAdd(){
        let active = document.querySelector('.tab.active').dataset.tab;
        if(active === 'resep') openModal();
        else if(active === 'artikel') openModalArtikel();
        else alert('Pindah ke tab Artikel atau Resep untuk menambah data.');
    }

    document.addEventListener("DOMContentLoaded", function(){
        const currentTabParam = "{{ request('tab', 'pengguna') }}";
        setActiveTab(currentTabParam);

        const filterForm = document.getElementById('filterForm');
        const searchInput = document.getElementById('searchInput');
        const umurInput = document.getElementById('umurInput');

        let searchTimeout;
        if (searchInput) {
            searchInput.addEventListener('keyup', () => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    filterForm.submit();
                }, 700);
            });
        }

        if (umurInput) {
            umurInput.addEventListener('input', () => {
                filterForm.submit();
            });
        }

        document.querySelectorAll('.angka-only').forEach(input => {
            input.addEventListener('keydown', function(e) {
                if (['e', 'E', '+', '-'].includes(e.key)) {
                    e.preventDefault();
                }
            });
            input.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9.]/g, '');
            });
        });
    });
</script>
</body>
</html>