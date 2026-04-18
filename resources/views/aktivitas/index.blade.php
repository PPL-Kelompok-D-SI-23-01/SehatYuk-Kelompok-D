<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Aktivitas</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    @php
        $target = $target ?? 1000;
        $persen = 0;
        if(isset($kaloriHariIni) && $target > 0){
            $persen = min(100, ($kaloriHariIni / $target) * 100);
        }
        $persen = round($persen, 2);
        $bgCircle = "conic-gradient(#7ed957 {$persen}%, #1f3d1f 0%)";
        $kaloriHariIni = $kaloriHariIni ?? 0;
        $targetInt = (int) $target;
    @endphp

    <style>
        *{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
        body{background:#cfd8c6;}
        .main{margin-left:120px;padding:20px;}
        .wrapper{background:#e6efe2;border-radius:20px;padding:20px;box-shadow:0 5px 15px rgba(0,0,0,0.2);}
        .header{margin-bottom:15px;}
        .header h3{font-weight:600;}
        .top{display:flex;gap:20px;align-items:flex-start;}
        .card-container-left{display:flex;flex-direction:column;gap:20px;flex:1;}
        .card{background:#2f4f2f;border-radius:15px;padding:20px;color:white;box-shadow:0 5px 10px rgba(0,0,0,0.2);}
        .card-log{flex:1.5;}
        .circle-wrap{display:flex;justify-content:center;margin-top:10px;}
        .circle{width:150px;height:150px;border-radius:50%;display:flex;align-items:center;justify-content:center;position:relative;}
        .circle::before{content:"";position:absolute;width:110px;height:110px;border-radius:50%;background:#1f3d1f;}
        .circle span{position:relative;z-index:2;font-size:18px;text-align:center;}
        .form-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:10px;}
        .form-grid input,.form-grid select{padding:10px;border-radius:8px;border:none;width:100%;}
        .btn{margin-top:10px;background:#7ed957;border:none;padding:10px;border-radius:10px;cursor:pointer;font-weight:600;}
        .btn:hover{opacity:0.9;}
        .table{margin-top:20px;background:#f4f4f4;border-radius:15px;overflow:hidden;}
        table{width:100%;border-collapse:collapse;}
        th{background:#eee;padding:10px;font-size:12px;text-align:left;}
        td{padding:10px;font-size:13px;border-bottom:1px solid #ddd;}
        .btn-detail{background:#e9d5c9;padding:5px 10px;border-radius:8px;font-size:12px;cursor:pointer;}
        .modal{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.6);z-index:999;}
        .modal-content{background:white;width:500px;margin:80px auto;padding:20px;border-radius:15px;position:relative;}
        .circle-bg{background:{{$bgCircle}};}

        .error-input {
            border: 2px solid red !important;
            background: #ffecec !important;
        }

    </style>
</head>
<body>

@include('components.navbar')

<div class="main">
    <div class="wrapper">
        <div class="header">
            <h3>Aktivitas</h3>
            <small>Pantau dan catat aktivitas harian Anda</small>
        </div>

        @if(session('success'))
            <div style="background:#d4edda;color:#155724;padding:10px;border-radius:10px;margin-bottom:15px;">
                ✅ {{ session('success') }}
            </div>
        @endif

        <div class="top">
            <div class="card-container-left">
                <div class="card">
                    <h4>Target Harian</h4>
                    <div class="circle-wrap">
                        <div class="circle circle-bg">
                            <span>
                                {{ $kaloriHariIni }}<br>
                                kcal
                            </span>
                        </div>
                    </div>
                    <p style="text-align:center;margin-top:10px;">
                        Target {{ $target }} kcal
                    </p>
                    <form method="POST" action="/target/update" style="margin-top:10px;">
                        @csrf
                        <input type="number" name="target_kalori" value="{{ old('target_kalori', $target) }}" 
                            min="1" max="50000" placeholder="1000" required
                            class="@error('target_kalori') error-input @enderror"
                            style="padding:8px;border-radius:8px;border:none;width:100%;margin-bottom:5px;color:black;">
                        <button class="btn" style="width:100%;">Set Target Harian</button>
                    </form>
                </div>
            </div>

            <div class="card card-log">
                <h4>Log Aktivitas</h4>
                <form id="formAktivitas" method="POST" action="/aktivitas">
                    @csrf
                    <div class="form-grid">
                        <div>
                            <label>Jenis Aktivitas</label>
                            <select name="jenis" style="color:black;">
                                <option value="Lari">Lari</option>
                                <option value="Joging">Joging</option>
                                <option value="Bersepeda">Bersepeda</option>
                                <option value="Renang">Renang</option>
                                <option value="Senam">Senam</option>
                            </select>
                        </div>
                        <div>
                            <label>Kalori (opsional)</label>
                            <input type="number" name="kalori" placeholder="Kosongkan untuk otomatis" style="color:black;">
                        </div>
                        <div>
                            <label>Durasi (menit)</label>
                            <input type="number" name="durasi" placeholder="30" min="1" required style="color:black;">
                        </div>
                        <div>
                            <label>Jarak (km)</label>
                            <input type="number" step="0.1" name="jarak" placeholder="4.5" style="color:black;">
                        </div>
                        <div style="grid-column:span 2;">
                            <label>Tanggal</label>
                            <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" style="color:black;">
                        </div>
                    </div>
                    <button class="btn" style="width:100%;margin-top:20px;">Simpan Aktivitas</button>
                </form>
            </div>
        </div>

        <div class="table">
            <table>
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Aktivitas</th>
                        <th>Durasi</th>
                        <th>Kalori</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $a)
                    <tr>
                        <td>{{ $a->tanggal }}</td>
                        <td>{{ $a->jenis }}</td>
                        <td>{{ $a->durasi }} menit</td>
                        <td>{{ $a->kalori }} kcal</td>
                        <td>
                            <span class="btn-detail"
                                data-id="{{ $a->id }}"
                                data-jenis="{{ $a->jenis }}"
                                data-tanggal="{{ $a->tanggal }}"
                                data-durasi="{{ $a->durasi }}"
                                data-kalori="{{ $a->kalori }}"
                                data-jarak="{{ $a->jarak ?? 0 }}"
                                onclick="openModalFromData(this)">
                                Detail
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center; padding:20px; color:#888;">
                            Belum ada aktivitas olahraga minggu ini. Yuk, mulai aktivitas hari ini!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modalDetail" class="modal">
    <div class="modal-content">
        <button onclick="closeModal()" style="position:absolute;right:15px;top:15px;border:none;background:none;font-size:20px;cursor:pointer;">✖</button>
        <h3 id="mJenis" style="margin-bottom:5px;"></h3>
        <p id="mTanggal" style="color:#666;margin-bottom:15px;"></p>
        <hr style="margin-bottom:15px;border:0;border-top:1px solid #eee;">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            <p>Durasi: <strong id="mDurasi"></strong></p>
            <p>Kalori: <strong id="mKalori"></strong></p>
            <p>Jarak: <strong id="mJarak"></strong></p>
            <p>Intensitas: <strong id="mIntensitas"></strong></p>
        </div>
        <p style="margin-top:10px;color:#2f4f2f;">Kontribusi Target Harian: <strong id="mTarget"></strong></p>
        <form id="deleteForm" method="POST">
            @csrf
            @method('DELETE')
            <button onclick="return confirm('Yakin mau hapus aktivitas ini?')" style="background:#dc3545;color:white;border:none;padding:12px;border-radius:10px;margin-top:20px;width:100%;cursor:pointer;font-weight:600;">
                Hapus Aktivitas
            </button>
        </form>
    </div>
</div>

<script>
    var targetKalori = parseInt("{{ $targetInt }}");

    document.querySelector('input[name="target_kalori"]').addEventListener('input', function(){
        let val = parseInt(this.value);
        if(val > 50000){
            alert("Maksimal kalori harian adalah 50.000 kcal!");
            this.value = 50000;
        }
    });

    function hitungEstimasi() {
        var durasi = document.querySelector('input[name="durasi"]').value;
        var jenis = document.querySelector('select[name="jenis"]').value;
        var inputKalori = document.querySelector('input[name="kalori"]');
        var berat = 60; 
        var metMap = { "Lari": 9.8, "Joging": 7, "Bersepeda": 6, "Renang": 8, "Senam": 4 };
        var met = metMap[jenis] || 5;
        var durasiJam = durasi / 60;
        var estimasi = Math.round(met * berat * durasiJam);
        if(!inputKalori.value){
            inputKalori.placeholder = "Estimasi: " + (durasi ? estimasi : "Kosongkan untuk otomatis") + " kcal";
        }
    }

    document.querySelector('input[name="durasi"]').addEventListener('input', hitungEstimasi);
    document.querySelector('select[name="jenis"]').addEventListener('change', hitungEstimasi);

    function openModalFromData(el){
        var id      = el.getAttribute('data-id');
        var jenis   = el.getAttribute('data-jenis');
        var tanggal = el.getAttribute('data-tanggal');
        var durasi  = el.getAttribute('data-durasi');
        var kalori  = el.getAttribute('data-kalori');
        var jarak   = el.getAttribute('data-jarak');
        openModal(id, jenis, tanggal, durasi, kalori, jarak);
    }

    function openModal(id, jenis, tanggal, durasi, kalori, jarak){
        document.getElementById('modalDetail').style.display = 'block';
        document.getElementById('mJenis').innerText   = "Detail Aktivitas: " + jenis;
        document.getElementById('mTanggal').innerText = tanggal;
        document.getElementById('mDurasi').innerText  = durasi + " menit";
        document.getElementById('mKalori').innerText  = kalori + " kcal";
        document.getElementById('mJarak').innerText   = jarak + " km";
        var intensitas = (kalori > 400) ? "Tinggi" : (kalori > 200 ? "Sedang" : "Rendah");
        document.getElementById('mIntensitas').innerText = intensitas;
        var persen = ((kalori / targetKalori) * 100).toFixed(1);
        document.getElementById('mTarget').innerText = persen + "%";
        document.getElementById('deleteForm').action = "/aktivitas/" + id;
    }

    function closeModal(){ document.getElementById('modalDetail').style.display = 'none'; }
    window.onclick = function(event){ if(event.target == document.getElementById('modalDetail')) closeModal(); }
</script>

</body>
</html>