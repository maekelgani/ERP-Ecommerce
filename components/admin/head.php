<?php
// pada head.php ini, jika $pageTitle tidak didefinisikan nilai nya, maka gunakan default "Dashboard" sebagai judul halaman
if (!isset($pageTitle)) {
    $pageTitle = "Dashboard";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Admin Panel</title>
    <link rel="icon" href="../../assets/img/logo-nano-transparant.png" type="image/x-icon">
    <link rel="stylesheet" href="/src/output.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
</head>