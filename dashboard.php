<?php
session_start();

if (!isset($_SESSION['role_id'])) {
    header("Location: login.php");
    exit();
}

switch ($_SESSION['role_id']) {

    case 1:
        header("Location: admin_dashboard.php");
        break;

    case 2:
        header("Location: kasir_dashboard.php");
        break;

    case 3:
        header("Location: mahasiswa_dashboard.php");
        break;

    default:
        header("Location: login.php");
}

exit();
?>