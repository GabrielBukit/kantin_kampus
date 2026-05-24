<?php
session_start();
require_once "database.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // VALIDASI
    if (empty($username) || empty($password)) {
        $error = "Username dan password wajib diisi!";
    } else {

        // CEK USER
        $stmt = $conn->prepare("SELECT id_user, username, password_hash, role_id FROM users WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {

            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password_hash'])) {

                // SET SESSION
                $_SESSION['user_id'] = $user['id_user'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role_id'] = $user['role_id'];

                // ARAHIN SESUAI ROLE
                if ($user['role_id'] == 1) {
                    header("Location: dashboard_admin.php");
                } elseif ($user['role_id'] == 2) {
                    header("Location: dashboard_kasir.php");
                } else {
                    header("Location: dashboard_mahasiswa.php");
                }
                exit();

            } else {
                $error = "Password salah!";
            }

        } else {
            $error = "User tidak ditemukan!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
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
    background: #f9fbfd;
    border-radius: 45px;
    overflow: hidden;
}

/* HEADER */
.header {
    width: 100%;
    height: 220px;
    background: url('https://figma-alpha-api.s3.us-west-2.amazonaws.com/images/2df3af74-fbf9-42b1-a345-37764a0aaefb');
    background-size: cover;
    background-position: center;
}

/* CARD */
.card {
    background: white;
    margin: -60px 20px 20px 20px;
    padding: 25px 20px 20px 20px;
    border-radius: 20px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    text-align: center;
    position: relative;
}

/* AVATAR */
.avatar {
    width: 85px;
    height: 85px;
    background: #fff;
    border-radius: 50%;
    position: absolute;
    top: -42px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    justify-content: center;
    align-items: center;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    border: 4px solid #f9fbfd;
}

.avatar img {
    width: 45px;
    opacity: 0.7;
}

/* TITLE */
h2 {
    margin-top: 50px;
    margin-bottom: 20px;
}

/* INPUT */
input {
    width: 100%;
    padding: 13px 16px;
    margin: 8px 0;
    border-radius: 15px;
    border: 1px solid #ddd;
    font-size: 14px;
    box-sizing: border-box;
}

/* BUTTON */
button {
    width: 100%;
    padding: 14px;
    background: #1796FF;
    border: none;
    border-radius: 12px;
    color: white;
    font-size: 16px;
    cursor: pointer;
    margin-top: 10px;
}

button:hover {
    background: #0f7ae5;
}

/* ERROR */
.error {
    background: red;
    color: white;
    padding: 10px;
    margin-bottom: 10px;
    border-radius: 8px;
    font-size: 12px;
}

/* LINK */
.link {
    margin-top: 15px;
    font-size: 13px;
}

.link a {
    color: #1796FF;
    text-decoration: none;
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

<h2>Login</h2>

<?php if (!empty($error)) : ?>
    <div class="error"><?= $error ?></div>
<?php endif; ?>

<form method="POST">

<input type="text" name="username" placeholder="Username / NIM" required>

<input type="password" name="password" placeholder="Password" required>

<button type="submit">Login</button>

</form>

<div class="link">
Belum punya akun? <a href="register.php">Daftar</a>
</div>

</div>

</div>
</div>

</body>
</html>