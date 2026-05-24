<?php
session_start();
require_once "database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ambil nama user
$queryUser = $conn->prepare("
    SELECT m.nama 
    FROM mahasiswa m
    WHERE m.user_id = ?
");
$queryUser->bind_param("i", $user_id);
$queryUser->execute();
$user = $queryUser->get_result()->fetch_assoc();

// 🔍 SEARCH
$search = $_GET['search'] ?? '';

$stmt = $conn->prepare("
    SELECT * FROM menu 
    WHERE is_active = 1 
    AND nama_menu LIKE ?
");
$like = "%$search%";
$stmt->bind_param("s", $like);
$stmt->execute();
$menu = $stmt->get_result();

// FLASH SALE (ambil 6 aja)
$flash = $conn->query("SELECT * FROM menu WHERE is_active = 1 LIMIT 6");
?>
<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>

/* ===== BODY ===== */
body {
    margin: 0;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: linear-gradient(135deg, #e3f2fd, #f5f7fa);
    font-family: 'Segoe UI', sans-serif;
}

/* ===== PHONE ===== */
.phone {
    width: 340px;
    height: 710px;
    background: #000;
    border-radius: 55px;
    padding: 14px;
    box-shadow: 0 25px 60px rgba(0,0,0,0.3);
}

/* ===== SCREEN ===== */
.screen {
    width: 100%;
    height: 100%;
    background: #f2f2f2;
    border-radius: 45px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* ===== TOPBAR ===== */
.topbar {
    padding: 15px;
    display: flex;
    justify-content: space-between;
    background: white;
}

.topbar img {
    width: 34px;
    border-radius: 50%;
    cursor: pointer;
}

/* ===== CONTENT ===== */
.content {
    flex: 1;
    overflow-y: auto;
    padding-bottom: 80px;
    scrollbar-width: none;
}
.content::-webkit-scrollbar {
    display: none;
}

/* ===== SEARCH ===== */
.search-box {
    margin: 12px;
}

.search-box input {
    width: 100%;
    box-sizing: border-box;
    padding: 13px 16px;
    border-radius: 15px;
    border: none;
    outline: none;
    background: white;
    font-size: 13px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    transition: 0.25s;
}

.search-box input:focus {
    box-shadow: 0 5px 18px rgba(23,150,255,0.25);
    transform: scale(1.01);
}

.search-box input::placeholder {
    color: #999;
}

/* ===== BANNER ===== */
.banner {
    margin: 10px;
    padding: 15px;
    border-radius: 15px;
    color: white;
    background: linear-gradient(135deg, #000, #444);
}

/* ===== TITLE ===== */
.title {
    margin: 15px 10px 5px;
    font-weight: bold;
}

/* ===== HORIZONTAL SCROLL ===== */
.menu-container {
    display: flex;
    overflow-x: auto;
    gap: 10px;
    padding: 10px;
    scrollbar-width: none;
}
.menu-container::-webkit-scrollbar {
    display: none;
}

/* ===== CARD ===== */
.menu-card {
    background: white;
    border-radius: 15px;
    padding: 8px;
    min-width: 140px;
    transition: 0.25s;
    box-shadow: 0 4px 10px rgba(0,0,0,0.08);
}

.menu-card:hover {
    transform: translateY(-5px) scale(1.05);
}

.menu-card img {
    width: 100%;
    height: 90px;
    border-radius: 10px;
    object-fit: cover;
}

.menu-price {
    color: #1796FF;
    font-weight: bold;
}

/* ===== GRID ===== */
.menu-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
    padding: 10px;
}

/* ===== MODAL ===== */
/* ===== MODAL BACKDROP ===== */
.modal {
    position: fixed;
    top:0; left:0;
    width:100%;
    height:100%;
    background: rgba(0,0,0,0.45);
    display:none;
    justify-content:center;
    align-items:center;
    z-index:999;
}

/* ===== MODAL BOX ===== */
.modal-box {
    background: white;
    padding: 25px;
    border-radius: 20px;
    width: 260px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    animation: pop 0.25s ease;
}

/* ANIMASI MASUK */
@keyframes pop {
    from {
        transform: scale(0.8);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

/* TITLE */
.modal-box h4 {
    margin: 0 0 15px;
}

/* INPUT QTY */
.qty-box {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    margin: 15px 0;
}

.qty-btn {
    width: 30px;
    height: 30px;
    border: none;
    border-radius: 50%;
    background: #1796FF;
    color: white;
    font-size: 18px;
    cursor: pointer;
    transition: 0.2s;
}

.qty-btn:hover {
    background: #0d7be0;
    transform: scale(1.1);
}

.qty-input {
    width: 50px;
    text-align: center;
    font-size: 16px;
    border: none;
    background: #f2f2f2;
    border-radius: 8px;
    padding: 5px;
}

/* BUTTON */
.modal-actions {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.btn {
    flex:1;
    padding: 10px;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    transition: 0.2s;
}

.btn-add {
    background: #1796FF;
    color: white;
}

.btn-add:hover {
    background: #0d7be0;
    transform: scale(1.05);
}

.btn-cancel {
    background: #eee;
}

.btn-cancel:hover {
    background: #ddd;
}

/* ===== NAVBAR ===== */
/* ===== NAVBAR ===== */
.bottom-nav {
    display: flex;
    justify-content: space-around;
    background: white;
    padding: 10px 0;
    border-top: 1px solid #eee;
}

/* ITEM */
.nav-item {
    font-size: 11px;
    text-align: center;
    cursor: pointer;
    transition: 0.25s;
    padding: 5px 10px;
    border-radius: 10px;
}

/* HOVER */
.nav-item:hover {
    background: #f2f8ff;
    color: #1796FF;
    transform: translateY(-2px);
}

/* ACTIVE */
.active {
    color: #1796FF;
    font-weight: bold;
    background: #eaf4ff;
}

</style>
</head>

<body>

<div class="phone">
<div class="screen">

<!-- TOP -->
<div class="topbar">
    <div>Halo, <?= htmlspecialchars($user['nama']) ?> 👋</div>
    <img src="https://cdn-icons-png.flaticon.com/512/847/847969.png"
         onclick="location='profil.php'">
</div>

<div class="content">

<!-- BANNER -->
<div class="banner">
    <h3>Pesan Online</h3>
    Hindari Antrian!
</div>

<!-- SEARCH -->
<form method="GET" class="search-box">
    <input 
        type="text" 
        name="search" 
        placeholder="Cari menu favoritmu..."
        value="<?= htmlspecialchars($search) ?>"
    >
</form>

<!-- FLASH -->
<div class="title">Flash Sale</div>
<div class="menu-container">
<?php while($f = $flash->fetch_assoc()) : ?>
<div class="menu-card" onclick="openModal(<?= $f['id_menu'] ?>)">
    <img src="<?= $f['foto_url'] ?>">
    <div><?= $f['nama_menu'] ?></div>
    <div class="menu-price">Rp <?= number_format($f['harga']) ?></div>
</div>
<?php endwhile; ?>
</div>

<!-- GRID -->
<div class="title">Semua Menu</div>
<div class="menu-grid">
<?php while($m = $menu->fetch_assoc()) : ?>
<div class="menu-card" onclick="openModal(<?= $m['id_menu'] ?>)">
    <img src="<?= $m['foto_url'] ?>">
    <div><?= $m['nama_menu'] ?></div>
    <div class="menu-price">Rp <?= number_format($m['harga']) ?></div>
</div>
<?php endwhile; ?>
</div>

</div>

<!-- NAV -->
<div class="bottom-nav">
    <div class="nav-item active">Home</div>
    <div class="nav-item" onclick="location='keranjang.php'">Keranjang</div>
    <div class="nav-item" onclick="location='pesanan.php'">Pesanan</div>
    <div class="nav-item" onclick="location='profil.php'">Profil</div>
</div>

</div>
</div>

<!-- MODAL -->
<div class="modal" id="modal">
  <div class="modal-box">
    <h4>Jumlah Pesanan</h4>

    <div class="qty-box">
        <button class="qty-btn" onclick="minus()">-</button>
        <input type="number" id="qty" class="qty-input" value="1" min="1">
        <button class="qty-btn" onclick="plus()">+</button>
    </div>

    <div class="modal-actions">
        <button class="btn btn-add" onclick="addToCart()">Tambah</button>
        <button class="btn btn-cancel" onclick="closeModal()">Batal</button>
    </div>
  </div>
</div>

<script>
let selectedMenu = 0;

function openModal(id){
    selectedMenu = id;
    document.getElementById('modal').style.display='flex';
}

function closeModal(){
    document.getElementById('modal').style.display='none';
}

function plus(){
    let q = document.getElementById('qty');
    q.value = parseInt(q.value) + 1;
}

function minus(){
    let q = document.getElementById('qty');
    if(q.value > 1){
        q.value = parseInt(q.value) - 1;
    }
}

function addToCart(){
    let qty = document.getElementById('qty').value;

    fetch('tambah_keranjang.php',{
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:`menu_id=${selectedMenu}&qty=${qty}`
    })
    .then(()=> {
        alert('Berhasil ditambahkan!');
        closeModal();
    });
}
</script>

</body>
</html>