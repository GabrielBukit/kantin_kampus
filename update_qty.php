<?php
require_once "database.php";

$id = $_POST['id'];
$change = $_POST['change'];

$conn->query("
    UPDATE keranjang 
    SET jumlah = GREATEST(jumlah + $change, 1)
    WHERE id_keranjang = $id
");