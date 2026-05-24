<?php
require_once "database.php";

$id = $_POST['id'];
$qty = $_POST['qty'];

$conn->query("UPDATE keranjang SET qty=$qty WHERE id_keranjang=$id");

header("Location: keranjang.php");