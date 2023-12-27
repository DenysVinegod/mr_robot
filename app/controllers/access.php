<?php
if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}

if (isset($_GET['account_action'])) {
    if ($_GET['account_action'] == 'logout'){
        session_destroy();
        header('Location: /app/views/log_in.php');
        exit();
    }
}

if (isset($_SESSION['account'])) {

} else {
    if ($_SESSION['service_block']['authentication_process'] != 'started') {
        $_SESSION['message']['warning'] = "Необхідна аутентифікація користувача!";
        $_SESSION['come_back_url'] = $_SERVER['REQUEST_URI'];
        header("Location: /app/views/log_in.php");
    }
}
?>
