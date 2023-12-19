<?php
$scripts = '<script src="/app/assets/js/repairs.js" defer></script>';
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/views/layouts/_main_header.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/controllers/repair.php');

$controller = new Repair();
?>

<nav class="additional_menu">
    <a id="button_back" class='tooltip menu_button' href="/">
        <img src='/app/assets/images/style/log_out.png'>
        <span class='tooltiptext'>Назад</span>
    </a>
    <div class="filler"></div>
    <div id="new_repair" 
        class='tooltip menu_button' 
        data-modal-target='#modal_editor'>
        <img src='/app/assets/images/style/pencil.png'>
        <span class='tooltiptext'>Створити</span>
    </div>
</nav>

<div id="info_shelter" class="block_hiden">
    
</div>

<div id="modal_editor" class="modal">
    <div class="modal_header">
        <p>Нове замовлення</p>
        <button data-close-button class="close-button">&times;</button>
    </div>
    <div class="modal_body">
        <form action="" method="POST">
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

                <input id="customer_phone" 
                    class="editor_input" 
                    type="text" 
                    name="phone" 
                    pattern="[0-9]{10}" 
                    maxlength="10" 
                    placeholder="Телефон клієнта" 
                    title="Введіть телефон клієнта (10 цифр.)" 
                    required>
            </fieldset>
            <fieldset>
                <legend>Дані про пристрій:</legend>                
                <select id="customer_device_type" 
                    class="editor_input" 
                    name="device_type"
                    required>
                    <?php 
                        foreach ($controller -> model -> list_device_types() 
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
                <label for="registered_datetime">
                    Дата/час прийняття заявки
                </label>
                <input id="registered_datetime" 
                    class="editor_input"
                    name="registered_at" 
                    type="datetime-local"
                    value="<?php echo date("Y-m-d\\TH:i:s", time()); ?>"
                    required>
                <textarea id="problem_description"
                    class="editor_input"
                    name="problem_description"
                    maxlength="1000"
                    rows="5"
                    cols="35"
                    placeholder="Причина звернення/коментар"
                    required></textarea>
            </fieldset>
            <fieldset id="additional_fieldset">
                <input id="manager_id" 
                    name="manager_id" 
                    type="text" 
                    value="<?php echo $_SESSION['account']['id']; ?>"
                    required>
            </fieldset>
            <fieldset>
                <input class="editor_input" type="submit" value="Зберегти">
            </fieldset>
        </form>
    </div>
</div>

<div id="overlay"></div>

<div class="repairs_table_container">

</div>

<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/views/layouts/_main_footer.php');
?>