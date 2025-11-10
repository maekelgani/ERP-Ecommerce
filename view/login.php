<?php
session_start();
require_once __DIR__ . '../../config/config.php';
require_once __DIR__ . '../../config/function.php';
require __DIR__ . "/../vendor/autoload.php";

//Google API login
$client = new Google\Client;

$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirecturi(GOOGLE_REDIRECT_URI);

$client->addScope("email");
$client->addScope("profile");

$url = $client->createAuthUrl();

//Create
if (isset($_POST['signup'])) {
    $nama = $_POST['name']; 
    $email = $_POST['email'];
    $no_telp = $_POST['no_telp'];
    $password = md5($_POST['password']); // Hash MD5

    //UIDGENERARTOR
    $query_id = mysqli_query($conn, "SELECT MAX(id_pengguna) AS maxID FROM pengguna_user");
    $data = mysqli_fetch_assoc($query_id);
    $lastID = $data['maxID'] ?? null;

    if ($lastID && preg_match('/USR(\d+)/', $lastID, $matches)) {
        $num = (int)$matches[1] + 1;
    } else {
        $num = 1;
    }
    $newID = "USR" . str_pad($num, 3, "0", STR_PAD_LEFT); //GENERATE UNIQUE ID

    $query = "INSERT INTO pengguna_user (id_pengguna, nama_pengguna, email, no_telp, password_pengguna) VALUES ('$newID', '$nama', '$email', '$no_telp',  '$password')";
    
    if (mysqli_query($conn, $query)) {
        echo "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Pendaftaran akun Berhasil!',
                    text: 'Silakan login untuk melanjutkan.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = 'login.php';
                });
            });
        </script>";
        exit(); 
    }

}

//Read
$error = "";
if (isset($_POST['masuk'])) {
    $login = mysqli_real_escape_string($conn, $_POST['login']);
    $password = md5($_POST['password']);

    // Deteksi input login: email atau nomor telepon
    if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
        // Kalau login pakai email
        $query = "SELECT * FROM pengguna_user WHERE email = '$login' AND password_pengguna = '$password'";
    } else {
        // Kalau login pakai no_telp
        $query = "SELECT * FROM pengguna_user WHERE no_telp = '$login' AND password_pengguna = '$password'";
    }

    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user_data = mysqli_fetch_assoc($result);

        // Set session
        $_SESSION['log'] = true;
        $_SESSION['login'] = $login; // bisa email / no_telp
        $_SESSION['id_pengguna'] = $user_data['id_pengguna'];
        $_SESSION['nama_pengguna'] = $user_data['nama_pengguna'];

        header('Location: index.php');
        exit();
    } else {
        $error = "Login gagal. Periksa kembali data Anda.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nano Komputer</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" href="../assets/img/Nanocomp.png">
    <link rel="stylesheet" href="../src/portal-login.css">
</head>

<body>
    <div class="container" id="container">
        <div class="form-container sign-up">
            <form method="POST" action="">
                <h1>Daftar Sekarang</h1>
                <div class="social-icons">
                    <a href="<?= $url ?>" class="icon"><i class="fa-brands fa-google"></i>Google</a>
                </div>
                <span>atau</span>
                <input type="text" name="name" placeholder="Nama" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="tel" name="no_telp" placeholder="Nomor Telepon" maxlength="15" required>
                <div class="password-wrapper">
                    <input type="password" name="password" id="signupPassword" placeholder="Password" required>
                    <i class="fa-solid fa-eye-slash" id="toggleSignupPassword"></i>
                </div>
                <button type="submit" name="signup">Daftar</button>
                <p class="terms">
                    Dengan mendaftar, saya menyetujui <br>
                    <a href="#">Syarat & Ketentuan</a>
                    serta
                    <a href="#">Kebijakan Privasi</a>.
                </p>
            </form>
        </div>

        <div class="form-container sign-in">
            <form method="POST" action="">
                <h1>Masuk</h1>
                <div class="social-icons">
                    <a href="<?= $url ?>" class="icon"><i class="fa-brands fa-google"></i>Google</a>
                </div>
                <span>atau</span>
                <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
                <input type="text" name="login" placeholder="Nomor Telepon atau Email" required>
                <div class="password-wrapper">
                    <input type="password" name="password" id="signinPassword" placeholder="Password" required>
                    <i class="fa-solid fa-eye-slash" id="toggleSigninPassword"></i>
                </div>
                <div class="options-row">
                    <label class="remember-me">
                        <input type="checkbox" name="remember"> Ingat saya
                    </label>
                    <a href="#" class="forgot-btn">Lupa Password?</a>
                </div>
                <button type="submit" name="masuk">Masuk</button>
                <p class="help-text">Butuh bantuan? <a href="#">Hubungi Nano Komputer</a></p>
            </form>
        </div>

        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <img src="../assets/img/logo_nano.png" alt="Nano Komputer Logo" class="logo-img">
                    <p>Daftarkan akun Anda <br> Nikmati berbagai promo menarik dari Nano Komputer.</p>
                    <button class="hidden" id="login">Masuk</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <img src="../assets/img/logo_nano.png" alt="Nano Komputer Logo" class="logo-img">
                    <p>Masuk sekarang dan nikmati <br>berbagai penawaran menarik di Nano Komputer.</p>
                    <button class="hidden" id="register">Daftar Sekarang</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
</body>
</html>
