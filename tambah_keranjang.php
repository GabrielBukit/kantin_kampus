<?php
session_start();
require_once "database.php";

if (!isset($_SESSION['user_id'])) exit();

$user_id = $_SESSION['user_id'];
$menu_id = $_POST['menu_id'] ?? 0;
$jumlah  = $_POST['qty'] ?? 1;

$jumlah = max(1, (int)$jumlah);

// cek sudah ada
$cek = $conn->prepare("
    SELECT id_keranjang, jumlah 
    FROM keranjang 
    WHERE user_id = ? AND menu_id = ?
");
$cek->bind_param("ii", $user_id, $menu_id);
$cek->execute();
$res = $cek->get_result();

if ($res->num_rows > 0) {
    $row = $res->fetch_assoc();
    $newJumlah = $row['jumlah'] + $jumlah;

    $update = $conn->prepare("
        UPDATE keranjang 
        SET jumlah = ? 
        WHERE id_keranjang = ?
    ");
    $update->bind_param("ii", $newJumlah, $row['id_keranjang']);
    $update->execute();
} else {
    $insert = $conn->prepare("
        INSERT INTO keranjang (user_id, menu_id, jumlah)
        VALUES (?, ?, ?)
    ");
    $insert->bind_param("iii", $user_id, $menu_id, $jumlah);
    $insert->execute();
}

echo "ok";