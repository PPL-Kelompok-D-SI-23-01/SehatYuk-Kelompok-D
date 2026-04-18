<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kalkulator Gizi</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #cfd8c6;
        }

        .main {
            margin-left: 120px;
            padding: 20px;
        }

        /* CARD */
        .card {
            background: #f4f4f4;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        /* HEADER */
        .header {
            background: #a8c39e;
            padding: 15px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 18px;
        }

        /* GRID */
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 20px;
        }

        /* PROFILE BOX */
        .profile-box {
            background: white;
            border-radius: 15px;
            padding: 20px;
        }

        .profile-box h3 {
            margin-bottom: 10px;
        }

        .profile-item {
            font-size: 14px;
            margin: 5px 0;
        }

        /* RESULT */
        .result-box {
            background: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
        }

        .result-box h2 {
            margin-bottom: 10px;
        }

        /* MACRO GRID */
        .macro {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-top: 15px;
        }

        .macro div {
            background: #e9e9e9;
            padding: 10px;
            border-radius: 10px;
        }

        /* FORM STYLES */
        .form-group {
            margin-top: 15px;
            text-align: left;
        }

        .form-group label {
            font-size: 13px;
            font-weight: 600;
            display: block;
            margin-bottom: 5px;
        }

        .form-select {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-family: 'Poppins', sans-serif;
            background-color: #fff;
        }

        /* BUTTON */
        .btn {
            display: inline-block;
            text-decoration: none;
            margin-top: 20px;
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 10px;
            background: #4caf50;
            color: white;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
        }

        .btn:hover {
            opacity: 0.9;
        }

        /* ALERT */
        .alert {
            background: #ffdddd;
            padding: 12px;
            border-radius: 10px;
            margin-top: 15px;
            color: #d32f2f;
            border: 1px solid #f44336;
            font-size: 14px;
        }

        .success {
            background: #d4edda;
            padding: 12px;
            border-radius: 10px;
            margin-top: 15px;
            color: #155724;
            border: 1px solid #c3e6cb;
            font-size: 14px;
        }

        @media(max-width:768px) {
            .grid {
                grid-template-columns: 1fr;
            }
            .main {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>

    @include('components.navbar')

    <div class="main">
        <div class="card">

            <div class="header">
                Kalkulator Kebutuhan Gizi Harian
            </div>

            {{-- 🔥 ERROR HANDLING --}}
            @if(session('error'))
            <div class="alert">
                {{ session('error') }}
            </div>
            @endif

            @if(session('success'))
            <div class="success">
                {{ session('success') }}
            </div>
            @endif

            <div class="grid">

                {{-- 🔹 DATA PROFILE & FORM HITUNG --}}
                <div class="profile-box">
                    <h3>Data Profil</h3>

                    {{-- 🔥 FIX DATA PROFILE (PASTIKAN INI) --}}
                    <div class="profile-item">Umur: {{ optional($user->client)->umur ?? '-' }}</div>
                    <div class="profile-item">Berat: {{ optional($user->client)->berat ?? '-' }} kg</div>
                    <div class="profile-item">Tinggi: {{ optional($user->client)->tinggi ?? '-' }} cm</div>
                    <div class="profile-item">Gender: {{ ucfirst(optional($user->client)->gender ?? '-') }}</div>

                    <hr style="margin-top:15px; border: 0; border-top: 1px solid #eee;">

                    <form action="/hitung-gizi" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label>Tingkat Aktivitas</label>
                            <select name="aktivitas" class="form-select">
                                <option value="ringan" {{ (optional($user->client)->aktivitas == 'ringan') ? 'selected' : '' }}>Ringan</option>
                                <option value="sedang" {{ (optional($user->client)->aktivitas == 'sedang' || !optional($user->client)->aktivitas) ? 'selected' : '' }}>Sedang</option>
                                <option value="berat" {{ (optional($user->client)->aktivitas == 'berat') ? 'selected' : '' }}>Berat</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Tujuan (Goal)</label>
                            <select name="goal" class="form-select">
                                <option value="tetap" {{ (optional($user->client)->goal == 'tetap' || !optional($user->client)->goal) ? 'selected' : '' }}>Tetap</option>
                                <option value="turun_berat" {{ (optional($user->client)->goal == 'turun_berat') ? 'selected' : '' }}>Turun Berat</option>
                                <option value="naik_berat" {{ (optional($user->client)->goal == 'naik_berat') ? 'selected' : '' }}>Naik Berat</option>
                            </select>
                        </div>

                        <button type="submit" class="btn">Hitung Sekarang</button>
                    </form>
                </div>

                {{-- 🔹 HASIL PERHITUNGAN --}}
                <div class="result-box">
                    <h2>Hasil Perhitungan</h2>

                    @if(isset($user->client->kalori_harian) && $user->client->kalori_harian > 0)

                        <h1>{{ $user->client->kalori_harian }} kcal</h1>
                        
                        <p style="font-size: 12px; color: #666; margin-bottom: 5px;">
                            Status: {{ str_replace('_', ' ', ucfirst($user->client->goal ?? 'tetap')) }} 
                            ({{ ucfirst($user->client->aktivitas ?? 'sedang') }})
                        </p>

                        @if(!empty($user->client->rekomendasi))
                            <p style="color:#2e7d32; font-weight:600; font-size: 14px; margin-top: 10px;">
                                Rekomendasi: {{ $user->client->rekomendasi }}
                            </p>
                        @else
                            <p style="color:red; font-size: 14px; margin-top: 10px;">
                                Rekomendasi belum tersedia
                            </p>
                        @endif

                        <div class="macro">
                            <div>
                                <strong>Protein</strong><br>
                                {{ $user->client->protein_harian ?? 0 }} g
                            </div>

                            <div>
                                <strong>Karbo</strong><br>
                                {{ $user->client->karbo_harian ?? 0 }} g
                            </div>

                            <div>
                                <strong>Lemak</strong><br>
                                {{ $user->client->lemak_harian ?? 0 }} g
                            </div>
                        </div>

                    @else
                        {{-- 🔥 TOMBOL ALTERNATIVE PROCESS --}}
                        <div style="margin-top: 20px;">
                            <img src="https://cdn-icons-png.flaticon.com/512/1633/1633633.png" width="50" style="opacity: 0.2; margin-bottom: 10px;">
                            <p style="color: #888; margin-bottom: 15px;">Belum ada data.<br>Silakan lengkapi profil dan klik hitung.</p>
                            
                            <a href="/profile" class="btn" style="background:#ff9800;">
                                Lengkapi Profil
                            </a>
                        </div>
                    @endif

                </div>

            </div>

        </div>
    </div>

</body>
</html>