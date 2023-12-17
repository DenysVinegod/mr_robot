<?php 
echo "controllers/access";

session_start();

if (isset($_SESSION['user'])) {

} else {
    $_SESSION['message']['warning'] = "Необхідна авторизація!";
    $_SESSION['come_back_url'] = $_SERVER['REQUEST_URI'];
    header("Location: /app/views/log_in.php");
}
?>