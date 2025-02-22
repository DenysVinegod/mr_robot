<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/app/controllers/access.php';
tryAccessModule('CRUD users');

require_once ($_SERVER['DOCUMENT_ROOT'].'/app/views/layouts/_main_header.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/controllers/user.php');

?>

<div id="modal_new_user_editor" class="modal">
    <div class="modal_header">
        <p>Нове замовлення</p>
        <button data-close-button class="close-button">&times;</button>
    </div>
    <div class="modal_body">
        <form action="/app/controllers/users.php" method="POST">
            <fieldset>
                <legend>Дані про користувача:</legend>
                <input id="user_name" 
                    class="editor_input"
                    type="text" 
                    name="name" 
                    pattern="[A-z,А-я,ІЇЄіїє]{1,500}" 
                    maxlength="50" 
                    placeholder="Ім'я нового користувача" 
                    title="Введіть ім'я користувача, до 500 символів. Допустимі символи: латинця, кирилиця, цифри."
                    required>

                <input id="user_login" 
                    class="editor_input"
                    type="text" 
                    name="login" 
                    pattern="[A-z,А-я,ІЇЄіїє]{1,50}" 
                    maxlength="50" 
                    placeholder="Логін нового користувача" 
                    title="Введіть логін користувача (до 50 символів). Допустимі символи: латинця, кирилиця, цифри."
                    required>
            </fieldset>
            <fieldset class="controll_buttons">
                <input type="reset" id="reset" class="button" value="Скинути">
                <input type="submit" id="submit" class="button" value="Зберегти">
            </fieldset>
        </form>
    </div>
</div>

<div class="repairs_table_container">
    <table id="repairs_list">
        <tr class="headers">
            <th>id</th>
            <th>login</th>
            <th>name</th>
            <?php render_html_rows(); ?>
        </tr>
    </table>
</div>

<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/views/layouts/_main_footer.php');
?>

<?php

function render_html_rows(): void{
    global $users;
    $row_counter = 0;
    foreach ($users as $user) {
        $class_line = $row_counter++ % 2 == 0 ? 'even' : 'odd';
        echo "<tr id='user_{$user['id']}' class='{$class_line} list_line'>";
        echo "<td>{$user['id']}</td>";
        echo "<td>{$user['login']}</td>";
        echo "<td>{$user['name']}</td>";
        echo "<td class='js_full_info' style='display: none;'>"
                .json_encode($user)
                ."</td>";
    }
}

?>
