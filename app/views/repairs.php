<?php
$scripts = '<script src="/app/assets/js/repairs.js" defer></script>';
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/views/layouts/_main_header.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/controllers/repair.php');

$controller = new Repair();
$controller -> model -> set_native_table("repairs");
?>

<nav class="additional_menu">
    <div class="menu_button_wrapper">
        <a id="button_back" 
            class='tooltip menu_button' 
            href="/">
            <img src='/app/assets/images/style/log_out.png'>
            <span class='tooltiptext'>Назад</span>
        </a>
    </div>
    <div class="filler"></div>
    <div class="menu_button_wrapper">
        <div id="new_repair" 
            class='tooltip menu_button' 
            data-modal-target='#modal_editor'>
            <img src='/app/assets/images/style/pencil.png'>
            <span class='tooltiptext'>Створити</span>
        </div>
    </div>
</nav>

<div id="modal_new_repair_editor" class="modal">
    <div class="modal_header">
        <p>Нове замовлення</p>
        <button data-close-button class="close-button">&times;</button>
    </div>
    <div class="modal_body">
        <form action="/app/controllers/repair.php" method="POST">
            <fieldset>
                <legend>Дані про замовника:</legend>
                <input id="customer_surname" 
                    class="editor_input"
                    type="text" 
                    name="surname" 
                    pattern="[A-z,А-я,ІЇЄіїє]{1,50}" 
                    maxlength="50" 
                    placeholder="Прізвище клієнта" 
                    title="Введіть прізвище клієнта, до 50 символів. Допустимі символи: латинця, кирилиця, цифри."
                    required>

                <input id="customer_first_name" 
                    class="editor_input"
                    type="text" 
                    name="first_name" 
                    pattern="[A-z,А-я,ІЇЄіїє]{1,50}" 
                    maxlength="50" 
                    placeholder="Ім'я клієнта" 
                    title="Введіть ім'я клієнта, до 50 символів. Допустимі символи: латинця, кирилиця, цифри."
                    required>

                <input id="customer_last_name" 
                    class="editor_input"
                    type="text" 
                    name="last_name" 
                    pattern="[A-z,А-я,ІЇЄіїє]{1,50}" 
                    maxlength="50" 
                    placeholder="По батькові клієнта" 
                    title="Введіть по батькові клієнта, до 50 символів. Допустимі символи: латинця, кирилиця, цифри."
                    required>
                <fieldset>
                    <legend>Контакти:</legend>
                    <select id="customer_contact_type_id" 
                        class="editor_input" 
                        name="contact_type_id"
                        required>
                        <?php
                            foreach ($controller 
                            -> model -> list_elements("contact_types") 
                            as $value) {
                                echo ("<option value='{$value['id']}'>
                                        {$value['name']}
                                    </option>");
                            }
                        ?>
                    </select>
                    <input id="customer_contact" 
                        class="editor_input" 
                        type="text" 
                        name="contact" 
                        maxlength="100" 
                        placeholder="Контакт клієнта" 
                        title="Введіть контактну інформацію клієнта (до 100 сим.)" 
                        required>
                </fieldset>
            </fieldset>
            <fieldset>
                <legend>Дані про пристрій:</legend>                
                <select id="customer_device_type" 
                    class="editor_input" 
                    name="device_type_id"
                    required>
                    <?php
                        foreach ($controller 
                        -> model -> list_elements("device_types") 
                        as $value) {
                            echo ("<option value='{$value['id']}'>
                                    {$value['name']}
                                </option>");
                        }
                    ?>
                </select>
            </fieldset>
            <fieldset>
                <legend>Дані про замовлення:</legend>
                <label for="registered_datetime" class="nowrap">
                    Дата/час прийняття заявки
                    <input id="time_updater_chbox" 
                        type="checkbox" 
                        name="time_updater">
                    <label for="time_updater_chbox">Автоматично</label>
                </label>
                <input id="registered_datetime" 
                    class="editor_input"
                    name="register_date" 
                    type="datetime-local"
                    value="<?php echo date("Y-m-d\\TH:i:s", time()); ?>"
                    required>
                <textarea id="problem_description"
                    class="editor_input"
                    name="description"
                    maxlength="1000"
                    rows="5"
                    cols="35"
                    placeholder="Причина звернення/коментар"
                    required></textarea>
                <input id="price" 
                    class="editor_input"
                    type="text" 
                    name="price" 
                    maxlength="500" 
                    placeholder="Ціна" 
                    title="Введіть ціну, до 500 символів.">
                </fieldset>
            <fieldset id="additional_fieldset" style="display: none;">
                <input id="status_id" 
                    type="text"
                    name="status_id"
                    value="1">
                <input id="action" 
                    type="text"
                    name="action"
                    value="create_new_repair">
                <input id="manager_id" 
                    name="manager_id" 
                    type="text" 
                    value="<?php echo $_SESSION['account']['id']; ?>"
                    required>
                <input id="back_path" 
                    name="back_path"
                    type="text" 
                    value="<?php echo $_SERVER['REQUEST_URI']; ?>"
                    required>
            </fieldset>
            <fieldset id="control_buttons">
                <div id="reset_wrapper">
                    <input type="reset" value="Скинути">
                </div>
                <div id="submit_wrapper">
                    <input type="submit" value="Зберегти">
                </div>
            </fieldset>
        </form>
    </div>
</div>

<div id="modal_repair_editor" class="modal">
    <div class="modal_header">
        <p>Замовлення #<span id="editor_repair_id"></span></p>
        <button data-close-button class="close-button">&times;</button>
    </div>
    <div class="modal_body">
        <form action="/app/controllers/repair.php" method="POST">
            <fieldset>
                <legend>Дані про замовника:</legend>
                <input id="repair_editor_surname" 
                    class="editor_input"
                    type="text" 
                    name="surname" 
                    pattern="[A-z,А-я,ІЇЄіїє]{1,50}" 
                    maxlength="50" 
                    placeholder="Прізвище клієнта" 
                    title="Введіть прізвище клієнта, до 50 символів. Допустимі символи: латинця, кирилиця, цифри."
                    required>

                <input id="repair_editor_first_name" 
                    class="editor_input"
                    type="text" 
                    name="first_name" 
                    pattern="[A-z,А-я,ІЇЄіїє]{1,50}" 
                    maxlength="50" 
                    placeholder="Ім'я клієнта" 
                    title="Введіть ім'я клієнта, до 50 символів. Допустимі символи: латинця, кирилиця, цифри."
                    required>

                <input id="repair_editor_last_name" 
                    class="editor_input"
                    type="text" 
                    name="last_name" 
                    pattern="[A-z,А-я,ІЇЄіїє]{1,50}" 
                    maxlength="50" 
                    placeholder="По батькові клієнта" 
                    title="Введіть по батькові клієнта, до 50 символів. Допустимі символи: латинця, кирилиця, цифри."
                    required>
                <fieldset>
                    <legend>Контакти:</legend>
                    <select id="repair_editor_contact_type_id" 
                        class="editor_input" 
                        name="contact_type_id"
                        required>
                        <?php
                            foreach ($controller 
                            -> model -> list_elements("contact_types") 
                            as $value) {
                                echo ("<option value='{$value['id']}'>
                                        {$value['name']}
                                    </option>");
                            }
                        ?>
                    </select>
                    <input id="repair_editor_contact" 
                        class="editor_input" 
                        type="text" 
                        name="contact" 
                        maxlength="100" 
                        placeholder="Контакт клієнта" 
                        title="Введіть контактну інформацію клієнта (до 100 сим.)" 
                        required>
                </fieldset>
            </fieldset>
            <fieldset>
                <legend>Дані про пристрій:</legend>                
                <select id="repair_editor_device_type" 
                    class="editor_input" 
                    name="device_type_id"
                    required>
                    <?php
                        foreach ($controller 
                        -> model -> list_elements("device_types") 
                        as $value) {
                            echo ("<option value='{$value['id']}'>
                                    {$value['name']}
                                </option>");
                        }
                    ?>
                </select>
            </fieldset>
            <fieldset>
                <legend>Дані про замовлення:</legend>
                <label for="repair_editor_registered_datetime" class="nowrap">
                    Дата/час прийняття заявки
                    <input id="repair_editor_time_updater_chbox" 
                        type="checkbox" 
                        name="time_updater">
                    <label for="repair_editor_time_updater_chbox">Автоматично</label>
                </label>
                <input id="repair_editor_registered_datetime" 
                    class="editor_input"
                    name="register_date" 
                    type="datetime-local"
                    value="<?php echo date("Y-m-d\\TH:i:s", time()); ?>"
                    required>
                <textarea id="repair_editor_problem_description"
                    class="editor_input"
                    name="description"
                    maxlength="1000"
                    rows="5"
                    cols="35"
                    placeholder="Причина звернення/коментар"
                    required></textarea>
            </fieldset>
            <fieldset id="repair_editor_additional_fieldset" 
                style="display: none;">
                <input id="repair_editor_action" 
                    type="text"
                    name="action"
                    value="edit_repair">
                <input id="repair_editor_manager_id" 
                    name="manager_id" 
                    type="text" 
                    value="<?php echo $_SESSION['account']['id']; ?>"
                    required>
                <input id="repair_editor_back_path" 
                    name="back_path"
                    type="text" 
                    value="<?php echo $_SERVER['REQUEST_URI']; ?>"
                    required>
            </fieldset>
            <fieldset id="repair_editor_control_buttons">
                <div id="reset_wrapper">
                    <input type="reset" value="Скинути">
                </div>
                <div id="submit_wrapper">
                    <input type="submit" value="Зберегти">
                </div>
            </fieldset>
        </form>
    </div>
</div>

<div id="overlay"></div>

<div class="repairs_table_container">
    <table id="repairs_list">
        <tr class="headers">
            <th>#</th>
            <th>Статус</th>
            <th>ПІБ</th>
            <th>Контакти</th>
            <th>Пристрій</th>
            <th>Причина звернення</th>
            <th>Вартість<br>грн</th>
            <th>Поломка<br>Коментар<br>майстра</th>
            <th>Дата<br>прийому</th>
            <th>Дата<br>видачі</th>
        </tr>
        <?php $controller -> render_html_rows(); ?>
    </table>
</div>

<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/views/layouts/_main_footer.php');
?>
