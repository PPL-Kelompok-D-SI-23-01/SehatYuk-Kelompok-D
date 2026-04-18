<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profile - SehatYuk</title>

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

        .profile-card {
            background: #f4f4f4;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        .profile-header {
            background: #a8c39e;
            padding: 12px;
            border-radius: 12px;
            font-weight: 600;
        }

        .user-info {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 15px;
        }

        .user-left {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .avatar {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            background: #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 20px;
            color: #555;
        }

        .btn-save {
            background: #4caf50;
            color: white;
            padding: 8px 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: 0.3s;
        }

        .btn-save:hover {
            background: #45a049;
        }

        .form-grid {
            margin-top: 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .input-group {
            display: flex;
            flex-direction: column;
        }

        .input-group label {
            font-size: 12px;
            margin-bottom: 5px;
            color: #666;
        }

        .input-group input, select {
            padding: 10px;
            border-radius: 8px;
            border: 1px solid transparent;
            background: #e9e9e9;
            transition: all 0.3s ease;
            outline: none;
        }

        .input-group input:focus, select:focus {
            background: #fff;
            border-color: #a8c39e;
        }

        /* HIGHLIGHT ERROR */
        .error-input {
            border: 2px solid red !important;
            background: #ffecec !important;
        }

        .toast-success {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #4caf50;
            color: white;
            padding: 12px 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            font-size: 14px;
            opacity: 0;
            transform: translateY(30px);
            animation: fadeIn 0.4s forwards;
            transition: all 0.5s ease;
            z-index: 999;
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .toast-hide {
            opacity: 0 !important;
            transform: translateY(30px) !important;
        }

        @media (max-width: 768px) {
            .main { margin-left: 0; }
            .form-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>

<body>

@include('components.navbar')

<div class="main">
    <div class="profile-card">

        <div class="profile-header">
            Profil Kesehatan
        </div>

        {{-- ✨ C. NOTIFIKASI ERROR GLOBAL --}}
        @if ($errors->any())
        <div style="background:#ffecec;padding:12px;border-radius:10px;margin-top:15px;margin-bottom:15px;border: 1px solid #ffcccc;">
            <strong style="color:red;">Terjadi kesalahan:</strong>
            <ul style="margin-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li style="color:red;font-size:13px;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="/profile/update">
            @csrf

            <div class="user-info">
                <div class="user-left">
                    <div class="avatar">{{ substr($user->name, 0, 1) }}</div>
                    <div>
                        <strong>{{ $user->name }}</strong><br>
                        <small>{{ $user->email }}</small>
                    </div>
                </div>

                <button type="submit" class="btn-save">Save Changes</button>
            </div>

            <div class="form-grid">

                {{-- NAMA --}}
                <div class="input-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="@error('name') error-input @enderror">
                    @error('name')
                        <small style="color:red">{{ $message }}</small>
                    @enderror
                </div>

                {{-- UMUR --}}
                <div class="input-group">
                    <label>Umur</label>
                    <input type="number" name="umur" value="{{ old('umur', $user->client->umur ?? '') }}" class="@error('umur') error-input @enderror">
                    @error('umur')
                        <small style="color:red">{{ $message }}</small>
                    @enderror
                </div>

                {{-- GENDER --}}
                <div class="input-group">
                    <label>Gender</label>
                    <select name="gender" class="@error('gender') error-input @enderror">
                        <option value="">Pilih Gender</option>
                        <option value="Pria" {{ old('gender', optional($user->client)->gender) == 'Pria' ? 'selected' : '' }}>Pria</option>
                        <option value="Wanita" {{ old('gender', optional($user->client)->gender) == 'Wanita' ? 'selected' : '' }}>Wanita</option>
                    </select>
                    @error('gender')
                        <small style="color:red">{{ $message }}</small>
                    @enderror
                </div>

                {{-- NEGARA --}}
                <div class="input-group">
                    <label>Negara</label>
                    <input type="text" name="negara" value="{{ old('negara', $user->client->negara ?? '') }}" class="@error('negara') error-input @enderror">
                    @error('negara')
                        <small style="color:red">{{ $message }}</small>
                    @enderror
                </div>

                {{-- NO HP --}}
                <div class="input-group">
                    <label>Nomor HP</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp', $user->client->no_hp ?? '') }}" class="@error('no_hp') error-input @enderror">
                    @error('no_hp')
                        <small style="color:red">{{ $message }}</small>
                    @enderror
                </div>

                {{-- TANGGAL LAHIR --}}
                <div class="input-group">
                    <label>Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" 
                           value="{{ old('tanggal_lahir', optional($user->client)->tanggal_lahir ? \Carbon\Carbon::parse($user->client->tanggal_lahir)->format('Y-m-d') : '') }}"
                           class="@error('tanggal_lahir') error-input @enderror">
                    @error('tanggal_lahir')
                        <small style="color:red">{{ $message }}</small>
                    @enderror
                </div>

                {{-- BERAT --}}
                <div class="input-group">
                    <label>Berat Badan (kg)</label>
                    <input type="number" name="berat" value="{{ old('berat', $user->client->berat ?? '') }}" class="@error('berat') error-input @enderror">
                    @error('berat')
                        <small style="color:red">{{ $message }}</small>
                    @enderror
                </div>

                {{-- TINGGI --}}
                <div class="input-group">
                    <label>Tinggi Badan (cm)</label>
                    <input type="number" name="tinggi" value="{{ old('tinggi', $user->client->tinggi ?? '') }}" class="@error('tinggi') error-input @enderror">
                    @error('tinggi')
                        <small style="color:red">{{ $message }}</small>
                    @enderror
                </div>

            </div>

        </form>

    </div>
</div>

@if(session('success'))
<div id="toast-success" class="toast-success">
    ✔ {{ session('success') }}
</div>
@endif

<script>
document.addEventListener("DOMContentLoaded", function () {
    // 1. Toast Success Logic
    const toast = document.getElementById('toast-success');
    if(toast){
        setTimeout(() => {
            toast.classList.add('toast-hide');
        }, 5000); 
    }

    // 2. ✨ A. AUTO FOCUS ERROR
    const firstError = document.querySelector('.error-input');
    if (firstError) {
        firstError.focus();
        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});
</script>

</body>
</html>