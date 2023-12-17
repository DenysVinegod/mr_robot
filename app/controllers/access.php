<?php 
echo "controllers/access";

session_start();

if (isset($_GET['account_action'])) {
    if ($_GET['account_action'] == 'logout'){
        session_destroy();
        header('Location: /app/views/sign_in.php');
        exit();
    }
}

if (isset($_SESSION['account'])) {

} else {
    $_SESSION['message']['warning'] = "Необхідна аутентифікація користувача!";
    $_SESSION['come_back_url'] = $_SERVER['REQUEST_URI'];
    header("Location: /app/views/log_in.php");
}
?>