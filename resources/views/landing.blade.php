<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>SehatYuk</title>

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

/* CONTAINER */
.container{
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}

/* CARD */
.card{
    width:900px;
    height:500px;
    background:#f4f4f4;
    border-radius:30px;
    box-shadow:0 15px 35px rgba(0,0,0,0.25);
    display:flex;
    overflow:hidden;
}

/* LEFT */
.left{
    flex:1;
    padding:50px;
    display:flex;
    flex-direction:column;
    justify-content:center;
}

.left h1{
    font-size:42px;
    margin-bottom:10px;
}

.left p{
    font-size:14px;
    color:#555;
    margin-bottom:25px;
}

/* BUTTON */
.btn{
    padding:12px 20px;
    border-radius:12px;
    border:none;
    background:#2f855a;
    color:white;
    cursor:pointer;
    margin-right:10px;
}

.btn-outline{
    background:#e2e2e2;
    color:black;
}

/* RIGHT */
.right{
    flex:1;
    background:#dfe5da;
    display:flex;
    justify-content:center;
    align-items:center;
}

.right img{
    width:70%;
}
</style>
</head>

<body>

<div class="container">

<div class="card">

    <!-- LEFT -->
    <div class="left">
        <h1>SehatYuk 💚</h1>
        <p>
            Platform digital untuk membantu kamu hidup lebih sehat, 
            mengatur gizi, memantau aktivitas, dan menjaga keseimbangan tubuh.
        </p>

        <div>
            <a href="/login">
                <button class="btn">Masuk</button>
            </a>

            <a href="/register">
                <button class="btn btn-outline">Daftar</button>
            </a>
        </div>
    </div>

    <!-- RIGHT -->
    <div class="right">
        <img src="https://cdn-icons-png.flaticon.com/512/1046/1046857.png">
    </div>

</div>

</div>

</body>
</html>