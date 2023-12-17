<?php
session_start();
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/views/layouts/_login_header.php');
?>

<form action="/app/controllers/authentication.php" method="post">
    <fieldset>
        <legend>Дані користувача</legend>
        <input id="input_user_login" name="user_login">
        <label for="input_user_login">Логін</label>
        <input id="input_user_password" type="password" name="user_password">
        <label for="input_user_password">Пароль</label>
        <input type="submit" value="Вхід">
    </fieldset>
</form>