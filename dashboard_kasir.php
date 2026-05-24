<?php
session_start();
if ($_SESSION['role_id'] != 2) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="login.css">
</head>
<body>

<div class="phone">
<div class="screen">
<div class="container">

<h2>Kasir</h2>

<button>Daftar Pesanan</button>
<button>Update Status</button>
<button onclick="window.location.href='logout.php'">Logout</button>

</div>
</div>
</div>

</body>
</html>