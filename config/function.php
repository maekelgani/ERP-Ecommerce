<?php
    //cek Login
    function cekLogin() {
        if (!isset($_SESSION['log'])) {
            header('Location: Login.php');
            exit;
        }
    }
    
    $conn = new mysqli("localhost", "root", "", "nanocomp_db");

    if($conn->connect_error){
        echo "ini error: ", $conn->connect_error;
    }


 // Get current user info
    function getCurrentUser() {
        global $conn;
        if (isset($_SESSION['email'])) {
            // Google OAuth users
            return [
                'nama' => $_SESSION['name'],
                'email' => $_SESSION['email'],
                'login_type' => 'google'
            ];
        } elseif (isset($_SESSION['login'])) {
            // regular users
            $login = $_SESSION['login'];
            $login = mysqli_real_escape_string($conn, $login);

            // login cek pakai email atau nomor telepon
            if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
                $query = mysqli_query($conn, "SELECT id_pengguna, nama_pengguna, email, no_telp 
                                            FROM pengguna_user 
                                            WHERE email = '$login'");
            } else {
                $query = mysqli_query($conn, "SELECT id_pengguna, nama_pengguna, email, no_telp 
                                            FROM pengguna_user 
                                            WHERE no_telp = '$login'");
            }

            if ($query && mysqli_num_rows($query) > 0) {
                $user = mysqli_fetch_assoc($query);
                $user['login_type'] = 'regular';
                return $user;
            }
        }

        return null;
    }
?>
