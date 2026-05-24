<?php
session_start();
require_once "database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$data = $conn->query("
SELECT 
    k.id_keranjang,
    k.jumlah,
    m.nama_menu,
    m.harga,
    m.foto_url
FROM keranjang k
JOIN menu m ON k.menu_id = m.id_menu
WHERE k.user_id = $user_id
");

$total = 0;
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Keranjang</title>

<style>

/* ===== BODY ===== */
body{
margin:0;height:100vh;
display:flex;justify-content:center;align-items:center;
background:linear-gradient(135deg,#e3f2fd,#f5f7fa);
font-family:'Segoe UI';
}

/* ===== PHONE ===== */
.phone{
width:340px;height:710px;
background:#000;border-radius:55px;
padding:14px;
box-shadow:0 25px 60px rgba(0,0,0,0.3);
}

/* ===== SCREEN ===== */
.screen{
height:100%;
background:#f2f2f2;
border-radius:45px;
display:flex;
flex-direction:column;
overflow:hidden;
}

/* ===== TOPBAR ===== */
.topbar{
padding:15px;
background:white;
font-weight:600;
border-bottom:1px solid #eee;
}

/* ===== CONTENT ===== */
.content{
flex:1;
overflow-y:auto;
padding:10px;
}
.content::-webkit-scrollbar{display:none;}

/* ===== CART ITEM ===== */
.cart-item{
display:flex;
align-items:center;
background:white;
padding:10px;
margin-bottom:12px;
border-radius:15px;
box-shadow:0 4px 12px rgba(0,0,0,0.08);
transition:0.25s;
}

.cart-item:hover{
transform:translateY(-4px);
box-shadow:0 8px 20px rgba(0,0,0,0.15);
}

/* ===== IMAGE ===== */
.cart-item img{
width:65px;
height:65px;
border-radius:12px;
object-fit:cover;
}

/* ===== INFO ===== */
.info{
flex:1;
margin-left:10px;
}

.menu-name{
font-size:14px;
font-weight:500;
}

.menu-price{
font-size:13px;
color:#1796FF;
margin-top:3px;
}

/* ===== QTY CONTROL ===== */
.qty{
display:flex;
align-items:center;
gap:6px;
}

.qty button{
width:28px;
height:28px;
border:none;
border-radius:8px;
background:#1796FF;
color:white;
font-size:16px;
cursor:pointer;
transition:0.2s;
}

.qty button:hover{
background:#0d7ae0;
transform:scale(1.1);
}

.qty span{
min-width:20px;
text-align:center;
font-weight:600;
}

/* ===== DELETE BUTTON ===== */
.delete{
margin-left:8px;
background:#ff4d4d;
border:none;
color:white;
padding:6px 8px;
border-radius:8px;
cursor:pointer;
transition:0.2s;
}

.delete:hover{
background:#e60000;
transform:scale(1.1);
}

/* ===== TOTAL BOX ===== */
.total{
padding:15px;
background:white;
border-top:1px solid #eee;
display:flex;
justify-content:space-between;
font-weight:600;
}

/* ===== NAVBAR ===== */
.bottom-nav{
display:flex;
justify-content:space-around;
background:white;
padding:10px 0;
border-top:1px solid #eee;
}

.nav-item{
font-size:11px;
cursor:pointer;
transition:0.2s;
}

.nav-item:hover{
color:#1796FF;
transform:scale(1.1);
}

.active{
color:#1796FF;
font-weight:bold;
}

</style>
</head>

<body>

<div class="phone">
<div class="screen">

<!-- TOPBAR -->
<div class="topbar">🛒 Keranjang Saya</div>

<!-- CONTENT -->
<div class="content">

<?php while($row = $data->fetch_assoc()):
$subtotal = $row['harga'] * $row['jumlah'];
$total += $subtotal;
?>

<div class="cart-item">
    <img src="<?= $row['foto_url'] ?>">

    <div class="info">
        <div class="menu-name"><?= $row['nama_menu'] ?></div>
        <div class="menu-price">Rp <?= number_format($row['harga']) ?></div>
    </div>

    <div class="qty">
        <button onclick="updateQty(<?= $row['id_keranjang'] ?>,-1)">−</button>
        <span><?= $row['jumlah'] ?></span>
        <button onclick="updateQty(<?= $row['id_keranjang'] ?>,1)">+</button>
    </div>

    <button class="delete" onclick="hapus(<?= $row['id_keranjang'] ?>)">✕</button>
</div>

<?php endwhile; ?>

</div>

<!-- TOTAL -->
<div class="total">
    <span>Total</span>
    <span>Rp <?= number_format($total) ?></span>
</div>

<!-- NAVBAR -->
<div class="bottom-nav">
    <div class="nav-item" onclick="location='dashboard.php'">Home</div>
    <div class="nav-item active">Keranjang</div>
    <div class="nav-item">Pesanan</div>
    <div class="nav-item">Profil</div>
</div>

</div>
</div>

<script>
function updateQty(id, change){
fetch('update_qty.php',{
method:'POST',
headers:{'Content-Type':'application/x-www-form-urlencoded'},
body:'id='+id+'&change='+change
}).then(()=>location.reload());
}

function hapus(id){
fetch('hapus_keranjang.php',{
method:'POST',
headers:{'Content-Type':'application/x-www-form-urlencoded'},
body:'id='+id
}).then(()=>location.reload());
}
</script>

</body>
</html>