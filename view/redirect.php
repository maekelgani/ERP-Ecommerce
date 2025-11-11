//Redirect Url: Google API login
<?php
require '../config/function.php';
require '../config/config.php';
require __DIR__ . '/../vendor/autoload.php';

$client = new Google\Client;
$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirecturi(GOOGLE_REDIRECT_URI);

if (!isset($_GET["code"])) {
    exit("Login failed: no code returned");
}

$token = $client->fetchAccessTokenWithAuthCode($_GET["code"]);

if (isset($token['error'])) {
    exit('Gagal mengambil access token: ' . htmlspecialchars($token['error']));
}

$client->setAccessToken($token['access_token']);

$oauth = new Google\Service\Oauth2($client);
$userinfo = $oauth->userinfo->get();

//menambahkan data user register ke db
$email = $userinfo->email;
$name = $userinfo->name;

// Cek apakah user sudah ada di database
$cek = mysqli_query($conn, "SELECT * FROM pengguna_user WHERE email='$email'");
if (mysqli_num_rows($cek) == 0) {

     // Hitung total user Google biar bisa generate ID unik
    $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM pengguna_user WHERE id_pengguna LIKE 'google%'");
    $row = mysqli_fetch_assoc($result);
    $totalGoogleUsers = $row['total'] + 1;

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

     // Password dummy (karena OAuth)
    $fakePassword = md5('google_oauth');
    
     // Insert ke database
    mysqli_query($conn, "INSERT INTO pengguna_user (id_pengguna, nama_pengguna, no_telp, email, password_pengguna) VALUES ('$newID', '$name', '', '$email', '$fakePassword')"); 
}

// Set session untuk user Google
$_SESSION['loggedin'] = true;
$_SESSION['email'] = $userinfo->email;
$_SESSION['name'] = $userinfo->name;

header('Location: index.php');
exit;
