<?php
if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}

require_once ($_SERVER['DOCUMENT_ROOT'].'/app/models/users.php');

$model = new Users();

if (sizeof($_POST)) {
    if (!isset($_POST['user_login'])) {
        $_SESSION['message']['warning'] = "Login needed, asshole!";
        header('Location: /app/views/log_in.php');
        exit();
    }
    
    $search = $model -> find_user_by_login($_POST['user_login']);
    if ($search['search_status'] == 'success') {
        // Login exist
        if (!isset($_POST['user_password'])) {
            $_SESSION['message']['warning'] = "Where is password, asshole!?";
            header('Location: /app/views/log_in.php');
            exit();
        }

        if (password_verify($_POST['user_password'], $search['password'])) {
            $_SESSION['account']['status'] = 'authenticated';
            $_SESSION['account']['login'] = $_POST['user_login'];
            $_SESSION['account'] = array_merge($_SESSION['account'], $search);
            $_SESSION['message']['info'] = 'Authenticated successfully!';
            if (isset($_SESSION['come_back_url'])) {
                $back_path = 
                    ($_SESSION['come_back_url'] == '/app/views/log_in.php') ? 
                    "/" : $_SESSION['come_back_url'];
                unset($_SESSION['come_back_url']);
            } else $back_path = "/";
            header("Location: {$back_path}");
            exit();
        } else {
            $pass_hash = password_hash($_POST['user_password'], PASSWORD_DEFAULT);
            $_SESSION['message']['warning'] = "Password {$pass_hash} wrong.";
            header('Location: /app/views/log_in.php');
        }
    } else {
        // Username not found
        $_SESSION['message']['warning'] = "User {$_POST['user_login']} not found.";
        header('Location: /app/views/log_in.php');
    }
} else {
    $_SESSION['message']['error'] = 'WTF, body!? Do authentication normally!';
    header('Location: /app/views/log_in.php');
    exit();
}
?>
