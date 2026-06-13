<?php
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

/**
 * Check if current user has any of allowed roles.
 * @param array $allowed array of role names
 * @return bool
 */
function user_has_role(array $allowed): bool {
    if (!isset($_SESSION['account']) || !isset($_SESSION['account']['role_name'])) return false;
    $role = $_SESSION['account']['role_name'];
    if (!$role) return false;
    if (in_array('superadmin', $allowed) && $role === 'superadmin') return true;
    return in_array($role, $allowed);
}

if (isset($_SESSION['account'])) {
    if (isset($_SESSION['account']['status'])) {
        switch ($_SESSION['account']['status']) {
            case 'authenticated': 
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
    }
} else {
    $_SESSION['account']['status'] = 'auth_first_stage';
    $_SESSION['come_back_url'] = $_SERVER['REQUEST_URI'];
    header('Location: /app/views/log_in.php');
}
?>
