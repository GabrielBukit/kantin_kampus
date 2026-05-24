<?php
session_start();
if ($_SESSION['role_id'] != 1) {
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

<h2>Admin Panel</h2>

<button>Kelola Menu</button>
<button>Lihat Laporan</button>
<button onclick="window.location.href='logout.php'">Logout</button>

</div>
</div>
</div>

</body>
</html>