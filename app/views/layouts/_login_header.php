<?php
if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}
$_SESSION['service_block']['authentication_process'] = 'started';
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/controllers/access.php');

if (isset($_SESSION['message'])) {
    foreach($_SESSION['message'] as $key => $value) {
        echo ("<div class='message {$key}'>{$value}</div>");
        unset($_SESSION['message']);
    }
}
?>