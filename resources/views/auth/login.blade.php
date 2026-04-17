<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Login</title>

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
    margin-left:0; /* 🔥 diubah biar center penuh */
    height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
}

/* CARD */
.card{
    width:420px;
    padding:35px;
    background:#f4f4f4;
    border-radius:25px;
    box-shadow:0 10px 25px rgba(0,0,0,0.25);
    text-align:center;
}

.title{
    font-size:38px;
    font-weight:600;
    letter-spacing:1px;
}

.sub{
    font-size:12px;
    margin-top:5px;
    color:#666;
}

.input-group{
    margin-top:15px;
    text-align:left;
}

.input-group label{
    font-size:13px;
}

.input{
    margin-top:5px;
    width:100%;
    padding:12px;
    border-radius:10px;
    border:1px solid #bbb;
    background:#efefef;
    font-size:13px;
}

.check{
    margin-top:10px;
    font-size:12px;
}

.btn{
    margin-top:15px;
    width:100%;
    padding:12px;
    border-radius:12px;
    border:1px solid #999;
    background:#e2e2e2;
    font-weight:500;
    cursor:pointer;
    box-shadow: inset 0 2px 2px rgba(255,255,255,0.6),
                0 2px 3px rgba(0,0,0,0.2);
}

.or{
    margin:15px 0;
    font-size:12px;
    color:#888;
}

.error-box{
    background:#ffdede;
    color:#a40000;
    padding:10px;
    border-radius:8px;
    margin-top:10px;
    font-size:13px;
}
</style>
</head>

<body>

<div class="main">
<div class="card">

    <div class="title">MASUK</div>
    <div class="sub">TIDAK PUNYA AKUN? <a href="/register">Daftar</a></div>

    @if(session('error_email'))
        <div class="error-box">
            {{ session('error_email') }}
        </div>
    @endif

    @if(session('error_password'))
        <div class="error-box">
            {{ session('error_password') }}
        </div>
    @endif

    <form method="POST" action="/login">
        @csrf

        <div class="input-group">
            <label>Email</label>
            <input class="input" type="email" name="email" placeholder="email@gmail.com">
        </div>

        <div class="input-group">
            <label>Password</label>
            <input class="input" type="password" name="password" placeholder="Masukkan kata sandi Anda">
        </div>

        <div class="check">
            <input type="checkbox"> Remember me
        </div>

        <button class="btn">LOGIN</button>
    </form>

    <div class="or">OR</div>

    <img src="https://cdn-icons-png.flaticon.com/512/281/281764.png" width="35">

</div>
</div>

</body>
</html>