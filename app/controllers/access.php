<?php
if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}

if (isset($_GET['account_action'])) {
    if ($_GET['account_action'] == 'logout'){
        session_destroy();
        header('Location: /app/views/log_in.php');
        exit();
    }
}

$message['auth']['need_auth'] = 'Необхідна аутентифікація користувача!';
$message['auth']['failed'] = 'Проблеми із входом у систему!';

if (isset($_SESSION['account'])) {
    if (isset($_SESSION['account']['status'])) {
        switch ($_SESSION['account']['status']) {
            case 'authenticated': 
                break;

            // case 'auth_first_stage':
            //     break;
            
            default:
                $_SESSION['message']['info'] = $message['auth']['failed'];
                $_SESSION['come_back_url'] = $_SERVER['REQUEST_URI'];
                // header('Location: /app/views/log_in.php');
                break;
        }
    }
    echo "<pre>";
    var_dump($_SESSION);
    echo "</pre>";
    exit ("account is set");
} else {
    $_SESSION['account']['status'] = 'auth_first_stage';
    $_SESSION['message']['info'] = $message['auth']['need_auth'];
    $_SESSION['come_back_url'] = $_SERVER['REQUEST_URI'];
    var_dump($_SESSION);
    echo "<hr>";
    exit("account not set");
    header('Location: /app/views/log_in.php');
}
?>
