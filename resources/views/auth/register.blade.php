<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Register</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins', sans-serif;
}

body{
    background:#cfd8c6;
}

/* MAIN */
.main{
    margin-left:0;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}

/* CARD */
.card{
    width:480px;
    background:#f4f4f4;
    border-radius:25px;
    padding:30px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.25);
}

/* TITLE */
.title{
    text-align:center;
    font-size:38px;
    font-weight:600;
    letter-spacing:1px;
}

.sub{
    text-align:center;
    font-size:12px;
    color:#666;
    margin-bottom:15px;
}

/* GRID */
.row{
    display:flex;
    gap:12px;
}

/* INPUT */
.group{
    margin-top:10px;
    width:100%;
}

label{
    font-size:13px;
    margin-bottom:3px;
    display:block;
}

input{
    width:100%;
    padding:12px;
    border-radius:10px;
    border:1px solid #bbb;
    background:#efefef;
    outline:none;
    font-size:13px;
}

/* PASSWORD ICON */
.password-wrapper{
    position:relative;
}

.password-wrapper span{
    position:absolute;
    right:10px;
    top:50%;
    transform:translateY(-50%);
    cursor:pointer;
}

/* CHECKBOX */
.check{
    margin-top:10px;
    font-size:12px;
}

/* BUTTON */
.btn{
    width:100%;
    margin-top:15px;
    padding:12px;
    border-radius:12px;
    border:1px solid #999;
    background:#e2e2e2;
    font-weight:500;
    cursor:pointer;
    box-shadow: inset 0 2px 2px rgba(255,255,255,0.6),
                0 2px 3px rgba(0,0,0,0.2);
}

/* OR */
.or{
    text-align:center;
    margin:15px 0;
    font-size:12px;
    color:#888;
}

/* GOOGLE */
.google{
    display:flex;
    justify-content:center;
}
</style>
</head>

<body>

<div class="main">
<div class="card">

    <div class="title">MENDAFTAR</div>
    <div class="sub">SUDAH MEMILIKI AKUN? <a href="/login">SIGN IN</a></div>

    @if(session('error_email'))
    <div style="background:#ffdede;color:#a40000;padding:10px;border-radius:8px;margin-top:10px;font-size:13px;text-align:center;">
        {{ session('error_email') }}
    </div>
    @endif

    <form method="POST" action="/register">
    @csrf

    <div class="row">
        <div class="group">
            <label>Nama Depan</label>
            <input name="name_depan" value="{{ old('name_depan') }}" placeholder="Masukkan nama depan...">
        </div>

        <div class="group">
            <label>Nama Belakang</label>
            <input name="name_belakang" value="{{ old('name_belakang') }}" placeholder="Masukkan nama belakang...">
        </div>
    </div>

    <div class="row">
        <div class="group">
            <label>Umur</label>
            <input name="umur" value="{{ old('umur') }}" placeholder="Masukkan umur...">
        </div>

        <div class="group">
            <label>Tanggal Lahir</label>
            <!-- 🔥 FIX DI SINI -->
            <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}">
        </div>
    </div>

    <div class="group">
        <label>Email</label>
        <input name="email" value="{{ old('email') }}" placeholder="email@gmail.com">
    </div>

    <div class="group">
        <label>Password</label>
        <div class="password-wrapper">
            <input id="password" type="password" name="password" placeholder="Masukkan kata sandi Anda">

            <span onclick="togglePassword()">

                <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="black" viewBox="0 0 16 16">
                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8z"/>
                    <path d="M8 5a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/>
                </svg>

                <svg id="eyeClose" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="black" viewBox="0 0 16 16" style="display:none;">
                    <path d="M13.646 14.354l-12-12 .708-.708 12 12-.708.708z"/>
                    <path d="M16 8s-3-5.5-8-5.5a7.03 7.03 0 0 0-3.933 1.146L16 8z"/>
                    <path d="M0 8s3 5.5 8 5.5a7.03 7.03 0 0 0 4.032-1.02L0 8z"/>
                </svg>

            </span>
        </div>
    </div>

    <div class="check">
        <input type="checkbox"> Saya Setuju Dengan 
        <a href="#">Syarat dan Ketentuan</a> serta 
        <a href="#">Kebijakan Privasi</a>
    </div>

    <button class="btn">BUAT AKUN</button>

    <div class="or">OR</div>

    <div class="google">
        <img src="https://cdn-icons-png.flaticon.com/512/281/281764.png" width="35">
    </div>

    </form>

</div>
</div>

<script>
function togglePassword() {
    const password = document.getElementById("password");
    const eyeOpen = document.getElementById("eyeOpen");
    const eyeClose = document.getElementById("eyeClose");

    if (password.type === "password") {
        password.type = "text";
        eyeOpen.style.display = "none";
        eyeClose.style.display = "block";
    } else {
        password.type = "password";
        eyeOpen.style.display = "block";
        eyeClose.style.display = "none";
    }
}
</script>

</body>
</html>