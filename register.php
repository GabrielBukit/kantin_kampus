<?php
require_once "database.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']); // NIM
    $nama     = trim($_POST['nama']);
    $fakultas = trim($_POST['fakultas']);
    $no_hp    = trim($_POST['no_hp']);
    $password = $_POST['password'];

    // ================= VALIDASI =================
    if (empty($username) || empty($nama) || empty($fakultas) || empty($no_hp) || empty($password)) {
        $error = "Semua field harus diisi!";
    } elseif (!is_numeric($no_hp)) {
        $error = "No HP harus berupa angka!";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter!";
    } else {

        // CEK USERNAME
        $cek = $conn->prepare("SELECT id_user FROM users WHERE username=?");
        $cek->bind_param("s", $username);
        $cek->execute();
        $cek->store_result();

        if ($cek->num_rows > 0) {
            $error = "NIM sudah terdaftar!";
        } else {

            // HASH PASSWORD
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // ROLE MAHASISWA (DIKUNCI)
            $role_id = 3;

            // TRANSAKSI
            $conn->begin_transaction();

            try {

                // INSERT USERS
                $stmt = $conn->prepare("INSERT INTO users (username, password_hash, role_id) VALUES (?, ?, ?)");
                $stmt->bind_param("ssi", $username, $password_hash, $role_id);
                $stmt->execute();

                $user_id = $stmt->insert_id;

                // INSERT MAHASISWA
                $stmt2 = $conn->prepare("INSERT INTO mahasiswa (user_id, nim, nama, fakultas, no_hp) VALUES (?, ?, ?, ?, ?)");
                $stmt2->bind_param("issss", $user_id, $username, $nama, $fakultas, $no_hp);
                $stmt2->execute();

                // COMMIT
                $conn->commit();

                echo "<script>alert('Registrasi Berhasil!'); window.location='login.php';</script>";
                exit();

            } catch (Exception $e) {

                // ROLLBACK jika gagal
                $conn->rollback();
                $error = "Registrasi gagal! Silakan coba lagi.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Register</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
/* ===== BACKGROUND ===== */
body {
    margin: 0;
    padding: 60px 0;
    display: flex;
    justify-content: center;
    background: linear-gradient(135deg, #e3f2fd, #f5f7fa);
    font-family: Arial, sans-serif;
}

/* PHONE */
.phone {
    width: 335px;
    height: 700px;
    background: #000;
    border-radius: 55px;
    padding: 14px;
    box-shadow: 0 30px 60px rgba(0,0,0,0.35);
    position: relative;
}

.phone::before {
    content: '';
    width: 150px;
    height: 28px;
    background: #000;
    position: absolute;
    top: 8px;
    left: 50%;
    transform: translateX(-50%);
    border-radius: 20px;
}

/* SCREEN */
.screen {
    width: 100%;
    height: 100%;
    border-radius: 45px;
    overflow: hidden;
    position: relative;
    background: #f9fbfd;
}

/* HEADER IMAGE */
.header {
    width: 100%;
    height: 200px;
    background: url('https://images.unsplash.com/photo-1555396273-367ea4eb4db5');
    background-size: cover;
    background-position: center;
}

/* CARD */
.card {
    position: absolute;
    top: 150px;
    left: 0;
    right: 0;
    margin: 0 15px;
    background: white;
    border-radius: 25px;
    padding: 70px 20px 25px 20px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

/* AVATAR */
.avatar {
    width: 85px;
    height: 85px;
    background: white;
    border-radius: 50%;
    position: absolute;
    top: -40px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
}

.avatar img {
    width: 45px;
    opacity: 0.6;
}

/* TITLE */
h2 {
    text-align: center;
    margin-bottom: 20px;
}

/* INPUT */
input {
    width: 100%;
    padding: 13px 16px;
    margin: 8px 0;
    border-radius: 15px;
    border: 1px solid #ddd;
    box-sizing: border-box;
}

/* BUTTON */
button {
    width: 100%;
    padding: 14px;
    background: #1796FF;
    border: none;
    border-radius: 15px;
    color: white;
    font-size: 16px;
    cursor: pointer;
    margin-top: 10px;
}

/* LINK */
.link {
    text-align: center;
    margin-top: 15px;
    font-size: 13px;
}

/* ERROR */
.error {
    background: #ff4d4f;
    color: white;
    padding: 10px;
    margin-bottom: 12px;
    border-radius: 8px;
    text-align: center;
    font-size: 12px;
}
</style>

</head>

<body>

<div class="phone">
<div class="screen">

<div class="header"></div>

<div class="card">

    <div class="avatar">
        <img src="https://cdn-icons-png.flaticon.com/512/847/847969.png">
    </div>

    <h2>Register</h2>

    <?php if (!empty($error)) : ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="NIM" required>
        <input type="text" name="nama" placeholder="Nama Lengkap" required>
        <input type="text" name="fakultas" placeholder="Fakultas" required>
        <input type="text" name="no_hp" placeholder="No HP" required>
        <input type="password" name="password" placeholder="Password" required>

        <button type="submit">Daftar</button>
    </form>

    <div class="link">
        Sudah punya akun? <a href="login.php">Login</a>
    </div>

</div>

</div>
</div>

</body>
</html>