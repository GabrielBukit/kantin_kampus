<?php
require_once "database.php";

$id = $_POST['id'];

$conn->query("DELETE FROM keranjang WHERE id_keranjang = $id");