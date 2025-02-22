<?php
// If module_name counted in _SESSION[account][modules] than pass throught else redirect to /
function tryAccessModule(string $module_name): void{
    $allow_acces = false;
    foreach ($_SESSION['account']['modules'] as $module) {
        if (isset($_SESSION['account']['modules'])) {
            if ($module == $module_name) {
                $allow_acces = true;
            }
        }
    }
    if (!$allow_acces) {
        $_SESSION['message']['info'] = 'Відмовлено у доступі до модуля : '.$module_name;
    }
}

if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}

if (isset($_GET['account_action'])) {
    if ($_GET['account_action'] == 'logout'){
        session_destroy();
        header('Location: /app/views/log_in.php');
        exit();
    }
}

$message['auth']['need_auth']       = 'Необхідна аутентифікація користувача!';
$message['auth']['access_denyed']   = 'Аутентифікуйтеся щоб отримати доступ!';
$message['auth']['failed']          = 'Проблеми із входом у систему!';

if (isset($_SESSION['account'])) {
    if (isset($_SESSION['account']['status'])) {
        switch ($_SESSION['account']['status']) {
            case 'authenticated': 
                require_once $_SERVER['DOCUMENT_ROOT'].'/app/models/users.php';
                $model = new Users();
                $_SESSION['account']['modules'] = $model ->getModules($_SESSION['account']['id']);
                break;

            case 'auth_first_stage': 
                $_SESSION['message']['info'] = $message['auth']['need_auth'];
                if ($_SERVER['REQUEST_URI'] != '/app/views/log_in.php') {
                    $_SESSION['come_back_url'] = $_SERVER['REQUEST_URI'];
                    header('Location: /app/views/log_in.php');
                }
                break;

            default:
                $_SESSION['message']['error'] = $message['auth']['failed']
                    ." account/status: {$_SESSION['account']['status']}";
                $_SESSION['come_back_url'] = $_SERVER['REQUEST_URI'];
                header('Location: /app/views/log_in.php');
                break;
        }
    } else { 
        echo "\$_SESSION['account']['status'] is unset, please check your session"; 
        exit(); 
    }
} else {
    $_SESSION['account']['status'] = 'auth_first_stage';
    $_SESSION['come_back_url'] = $_SERVER['REQUEST_URI'];
    header('Location: /app/views/log_in.php');
}


?>
