<?php
if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/views/layouts/_login_header.php');
?>

<form action="/app/controllers/authentication.php" method="post">
    <fieldset>
        <div id="login_header">
            <p>Авторизуйтесь</p>
        </div>
        <table>
            <tr>
                <th><label for="input_user_login">Логін</label></th>
                <td>
                    <input id="input_user_login" 
                        name="user_login" 
                        pattern="[A-z,1-9]{5,50}" 
                        maxlength="50"
                        placeholder="login"
                        title="Введіть логін латинецею та/або цифрами, понад 5 символів."
                        required>
                </td>
            </tr>
            <tr>
                <th><label for="input_user_password">Пароль</label></th>
                <td>
                    <input id="input_user_password" 
                        type="password" 
                        name="user_password" 
                        pattern="[A-z,А-я,1-9,!@#\$%\^&\*_]{5,50}" 
                        maxlength="50" 
                        placeholder="password" 
                        title="Введіть пароль, понад 5 символів. Допустимі символи: латинця, 
                        кирилиця та цифри." 
                        required>
                </td>
            </tr>
        </table>
        <div id="submit_wrapper">
            <input type="submit" value="Вхід">
        </div>

    </fieldset>
</form>

<?php 
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/views/layouts/_login_footer.php');
?>
