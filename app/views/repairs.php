<?php
$scripts = '<script src="/app/assets/js/repairs.js?v=0.2" defer></script>';
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/views/layouts/_main_header.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/controllers/repair.php');

$controller = new Repair();
$controller->model->set_native_table("repairs");

$per_page = 10;
$page = isset($_GET['page']) && intval($_GET['page']) > 0 ? intval($_GET['page']) : 1;
$status_filter = isset($_GET['status']) ? intval($_GET['status']) : 0;
$sort_by = isset($_GET['sort_by']) ? $controller->sanitize_sort_by($_GET['sort_by']) : 'register_date';
$sort_dir = isset($_GET['sort_dir']) ? $controller->sanitize_sort_dir($_GET['sort_dir']) : 'DESC';
$page = max(1, min($page, $controller->get_total_pages($per_page, $status_filter)));
$all_clients = $model_clients->list_elements('clients');
$all_contacts = $model_contacts->list_elements('contacts');
$all_devices = $model_devices->list_elements('devices');
$contact_types = $controller->model->list_elements('contact_types');
$device_types = $controller->model->list_elements('device_types');
?>

<script>
window.mrRobotData = {
  clients: <?php echo json_encode($all_clients, JSON_UNESCAPED_UNICODE); ?>,
  contacts: <?php echo json_encode($all_contacts, JSON_UNESCAPED_UNICODE); ?>,
  devices: <?php echo json_encode($all_devices, JSON_UNESCAPED_UNICODE); ?>,
  contactTypes: <?php echo json_encode($contact_types, JSON_UNESCAPED_UNICODE); ?>,
  deviceTypes: <?php echo json_encode($device_types, JSON_UNESCAPED_UNICODE); ?>
};
</script>

<div id="modal_new_repair_editor" class="modal">
    <div class="modal_header">
        <p>Нове замовлення</p>
        <button data-close-button class="close-button">&times;</button>
    </div>
    <div class="modal_body">
        <form id="new_repair_form" action="/app/controllers/repair.php" method="POST">
            <fieldset>
                <legend>Дані про замовника</legend>
                <div class="search_group">
                    <label for="customer_search">Пошук клієнта</label>
                    <input id="customer_search" class="editor_input" type="text" placeholder="Введіть прізвище, ім'я або по батькові" autocomplete="off">
                    <div id="customer_suggestions" class="suggestions_list"></div>
                </div>
                <input id="customer_client_id" type="hidden" name="client_id" value="0">
                <input id="customer_contact_id" type="hidden" name="contact_id" value="0">
                <div class="form_split">
                    <input id="customer_surname" 
                        class="editor_input"
                        type="text" 
                        name="surname" 
                        pattern="[A-zА-яІЇЄіїє]{1,50}" 
                        maxlength="50" 
                        placeholder="Прізвище" 
                        required>

                    <input id="customer_first_name" 
                        class="editor_input"
                        type="text" 
                        name="first_name" 
                        pattern="[A-zА-яІЇЄіїє]{1,50}" 
                        maxlength="50" 
                        placeholder="Ім'я" 
                        required>

                    <input id="customer_last_name" 
                        class="editor_input"
                        type="text" 
                        name="last_name" 
                        pattern="[A-zА-яІЇЄіїє]{1,50}" 
                        maxlength="50" 
                        placeholder="По батькові" 
                        required>
                </div>
                <fieldset id="customer_contacts_block" class="dynamic_block">
                    <legend>Контакти</legend>
                    <div id="customer_contacts_existing" class="dynamic_list"></div>
                    <button type="button" id="customer_new_contact_button" class="button button_secondary">Ввести новий контакт</button>
                    <div class="contact_editor">
                        <label for="customer_contact_type_id">Тип контакту</label>
                        <select id="customer_contact_type_id" class="editor_input" name="contact_type_id" required>
                            <?php foreach ($contact_types as $type): ?>
                                <option value="<?php echo $type['id']; ?>"><?php echo htmlspecialchars($type['name'], ENT_QUOTES, 'UTF-8'); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input id="customer_contact" class="editor_input" type="text" name="contact" maxlength="100" placeholder="Контакт клієнта" title="Введіть контактну інформацію клієнта (до 100 сим.)" required>
                    </div>
                    <p class="hint">Оберіть існуючий контакт або введіть новий.</p>
                </fieldset>
            </fieldset>
            <fieldset class="modal_group">
                <legend>Дані про пристрій</legend>
                <label for="customer_device_select">Пристрій клієнта</label>
                <select id="customer_device_select" class="editor_input" name="device_id" required>
                    <option value="0">Оберіть клієнта для пристрою</option>
                    <option value="new">Додати новий пристрій</option>
                </select>
                <div id="customer_new_device_fields" class="hidden">
                    <div class="device_field">
                        <label for="customer_device_type">Тип нового пристрою</label>
                        <select id="customer_device_type" class="editor_input" name="device_type_id">
                            <?php foreach ($device_types as $type): ?>
                                <option value="<?php echo $type['id']; ?>"><?php echo htmlspecialchars($type['name'], ENT_QUOTES, 'UTF-8'); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="device_field">
                        <label for="customer_device_color">Колір</label>
                        <input id="customer_device_color" class="editor_input" type="text" name="device_color" maxlength="100" placeholder="Колір" />
                    </div>
                    <div class="device_field">
                        <label for="customer_device_cosmetic_condition">Косметичний стан</label>
                        <input id="customer_device_cosmetic_condition" class="editor_input" type="text" name="device_cosmetic_condition" maxlength="255" placeholder="Косметичний стан (напр. задряпаний, новий)" />
                    </div>
                    <div class="device_field">
                        <label for="customer_device_serial_number">Серійний номер</label>
                        <input id="customer_device_serial_number" class="editor_input" type="text" name="device_serial_number" maxlength="100" placeholder="Серійний номер" />
                    </div>
                    <div class="device_field device_field-full">
                        <label for="customer_device_description">Опис пристрою</label>
                        <textarea id="customer_device_description" class="editor_input" name="device_description" maxlength="500" rows="2" placeholder="Опис пристрою"></textarea>
                    </div>
                    <div class="device_field device_field-full">
                        <label for="customer_device_equipment">Комплектація</label>
                        <textarea id="customer_device_equipment" class="editor_input" name="device_equipment" maxlength="500" rows="2" placeholder="Комплектація (при наявності коробки, акумулятор, зарядка тощо)"></textarea>
                    </div>
                </div>
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
                    maxlength="5000"
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
            <input id="status_id" type="hidden" name="status_id" value="1">
            <input id="action" type="hidden" name="action" value="create_new_repair">
            <input id="manager_id" type="hidden" name="manager_id" value="<?php echo $_SESSION['account']['id']; ?>">
            <input id="back_path" type="hidden" name="back_path" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
            <fieldset class="controll_buttons">
                <input type="reset" id="reset" class="button" value="Скинути">
                <input type="submit" id="submit" class="button" value="Зберегти">
            </fieldset>
        </form>
    </div>
</div>

<div id="modal_repair_editor" class="modal">
    <div class="modal_header">
        <p>Замовлення #<span id="editor_repair_id"></span></p>
        <button id="enable_editor" class="modal_button">
            <img src="/app/assets/images/style/pencil_black.png">
        </button>
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
                <label for="repair_editor_device_description">Опис пристрою</label>
                <textarea id="repair_editor_device_description"
                    class="editor_input"
                    name="device_description"
                    maxlength="500"
                    rows="2"
                    placeholder="Опис пристрою"></textarea>
                <label for="repair_editor_device_color">Колір</label>
                <input id="repair_editor_device_color"
                    class="editor_input"
                    type="text"
                    name="device_color"
                    maxlength="100"
                    placeholder="Колір пристрою">
                <label for="repair_editor_device_cosmetic_condition">Косметичний стан</label>
                <input id="repair_editor_device_cosmetic_condition"
                    class="editor_input"
                    type="text"
                    name="device_cosmetic_condition"
                    maxlength="255"
                    placeholder="Наприклад: задряпаний, новий, зношений">
                <label for="repair_editor_device_serial_number">Серійний номер</label>
                <input id="repair_editor_device_serial_number"
                    class="editor_input"
                    type="text"
                    name="device_serial_number"
                    maxlength="100"
                    placeholder="Серійний номер пристрою">
                <label for="repair_editor_device_equipment">Комплектація</label>
                <textarea id="repair_editor_device_equipment"
                    class="editor_input"
                    name="device_equipment"
                    maxlength="500"
                    rows="2"
                    placeholder="Коробка, ЗУ, кабель, батарея, аксесуари"></textarea>
            </fieldset>
            <fieldset>
                <legend>Дані про замовлення:</legend>
                <label for="editor_status">
                    Статус замовлення</label>
                <select id="editor_status" 
                    name="status" 
                    class="editor_input">
                    <?php
                        foreach($controller 
                            -> model -> list_elements('statuses') 
                            as $element) {
                            echo ("<option value='{$element['id']}'>
                                {$element['name']}
                            </option>");
                        }
                    ?>
                </select>
                <label for="repair_editor_registered_datetime" class="nowrap">
                    Дата/час прийняття заявки</label>
                <input id="repair_editor_registered_datetime" 
                    class="editor_input"
                    name="register_date" 
                    type="datetime-local"
                    required>
                <label for="repair_editor_problem_description">
                    Причина звернення/коментар</label>
                <textarea id="repair_editor_problem_description"
                    class="editor_input"
                    name="description"
                    maxlength="500"
                    rows="5"
                    cols="35"
                    placeholder="Причина звернення/коментар"
                    required></textarea>
                <label for="repair_editor_price">Ціна</label>
                <input id="repair_editor_price" 
                    class="editor_input"
                    type="text" 
                    name="price" 
                    maxlength="500" 
                    placeholder="Ціна" 
                    title="Введіть ціну, до 500 символів.">
                <label for="repair_editor_master_conclusion">
                    Коментар майстра</label>
                <textarea id="repair_editor_master_conclusion"
                    class="editor_input"
                    name="master_conclusion"
                    maxlength="1500"
                    rows="5"
                    cols="35"
                    placeholder="Поломка/Коментар майстра"></textarea>
            </fieldset>
            <fieldset id="repair_editor_additional_fieldset" class="hidden">
                <input id="repair_editor_id" 
                    type="text"
                    name="id"
                    required>
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
            <fieldset class="controll_buttons">
                <input type="submit" id="submit"  class="button" value="Зберегти">
                <div id="print_recipt" class="button">
                    <img id="printer_button" src="/app/assets/images/style/printer_small.png">
                </div>
            </fieldset>
        </form>
    </div>
    <div id="js_full_info_modal" class="hidden">
    </div>
</div>

<div class="repairs_table_container">
    <div class="table_controls">
        <form method="GET" class="filter_form">
            <div class="filter_group">
                <label for="status_filter">Фільтр за статусом:</label>
                <select id="status_filter" name="status" class="input_select">
                    <option value="0"<?php echo $status_filter === 0 ? ' selected' : ''; ?>>Усі статуси</option>
                    <?php foreach ($controller->get_statuses() as $status): ?>
                        <option value="<?php echo $status['id']; ?>"<?php echo $status_filter === intval($status['id']) ? ' selected' : ''; ?>><?php echo htmlspecialchars($status['name'], ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter_group filter_group--sort-row">
                <label for="sort_by">Сортувати:</label>
                <div class="sort_selects">
                    <select id="sort_by" name="sort_by" class="input_select">
                        <?php foreach ($controller->get_sortable_columns() as $key => $label): ?>
                            <option value="<?php echo $key; ?>"<?php echo $sort_by === $key ? ' selected' : ''; ?>><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select id="sort_dir" name="sort_dir" class="input_select">
                        <option value="ASC"<?php echo $sort_dir === 'ASC' ? ' selected' : ''; ?>>За зростанням</option>
                        <option value="DESC"<?php echo $sort_dir === 'DESC' ? ' selected' : ''; ?>>За спаданням</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="button button_primary">Застосувати</button>
        </form>

        <?php $controller->render_pagination($page, $per_page, $status_filter, $sort_by, $sort_dir); ?>
    </div>
    <table id="repairs_list">
        <tr class="headers">
            <th><a href="<?php echo $controller->build_query_url(['sort_by' => 'id', 'sort_dir' => $sort_by === 'id' && $sort_dir === 'ASC' ? 'DESC' : 'ASC', 'page' => 1]); ?>"># <span class="sort_indicator"><?php echo $sort_by === 'id' ? ($sort_dir === 'ASC' ? '▲' : '▼') : ''; ?></span></a></th>
            <th><a href="<?php echo $controller->build_query_url(['sort_by' => 'status', 'sort_dir' => $sort_by === 'status' && $sort_dir === 'ASC' ? 'DESC' : 'ASC', 'page' => 1]); ?>">Статус <span class="sort_indicator"><?php echo $sort_by === 'status' ? ($sort_dir === 'ASC' ? '▲' : '▼') : ''; ?></span></a></th>
            <th><a href="<?php echo $controller->build_query_url(['sort_by' => 'surname', 'sort_dir' => $sort_by === 'surname' && $sort_dir === 'ASC' ? 'DESC' : 'ASC', 'page' => 1]); ?>">ПІБ <span class="sort_indicator"><?php echo $sort_by === 'surname' ? ($sort_dir === 'ASC' ? '▲' : '▼') : ''; ?></span></a></th>
            <th><a href="<?php echo $controller->build_query_url(['sort_by' => 'contact_type', 'sort_dir' => $sort_by === 'contact_type' && $sort_dir === 'ASC' ? 'DESC' : 'ASC', 'page' => 1]); ?>">Контакти <span class="sort_indicator"><?php echo $sort_by === 'contact_type' ? ($sort_dir === 'ASC' ? '▲' : '▼') : ''; ?></span></a></th>
            <th><a href="<?php echo $controller->build_query_url(['sort_by' => 'device_name', 'sort_dir' => $sort_by === 'device_name' && $sort_dir === 'ASC' ? 'DESC' : 'ASC', 'page' => 1]); ?>">Пристрій <span class="sort_indicator"><?php echo $sort_by === 'device_name' ? ($sort_dir === 'ASC' ? '▲' : '▼') : ''; ?></span></a></th>
            <th><a href="<?php echo $controller->build_query_url(['sort_by' => 'description', 'sort_dir' => $sort_by === 'description' && $sort_dir === 'ASC' ? 'DESC' : 'ASC', 'page' => 1]); ?>">Причина звернення <span class="sort_indicator"><?php echo $sort_by === 'description' ? ($sort_dir === 'ASC' ? '▲' : '▼') : ''; ?></span></a></th>
            <th><a href="<?php echo $controller->build_query_url(['sort_by' => 'price', 'sort_dir' => $sort_by === 'price' && $sort_dir === 'ASC' ? 'DESC' : 'ASC', 'page' => 1]); ?>">Вартість<br>грн <span class="sort_indicator"><?php echo $sort_by === 'price' ? ($sort_dir === 'ASC' ? '▲' : '▼') : ''; ?></span></a></th>
            <th><a href="<?php echo $controller->build_query_url(['sort_by' => 'master_conclusion', 'sort_dir' => $sort_by === 'master_conclusion' && $sort_dir === 'ASC' ? 'DESC' : 'ASC', 'page' => 1]); ?>">Поломка<br>Коментар майстра <span class="sort_indicator"><?php echo $sort_by === 'master_conclusion' ? ($sort_dir === 'ASC' ? '▲' : '▼') : ''; ?></span></a></th>
            <th><a href="<?php echo $controller->build_query_url(['sort_by' => 'register_date', 'sort_dir' => $sort_by === 'register_date' && $sort_dir === 'ASC' ? 'DESC' : 'ASC', 'page' => 1]); ?>">Дата<br>прийому <span class="sort_indicator"><?php echo $sort_by === 'register_date' ? ($sort_dir === 'ASC' ? '▲' : '▼') : ''; ?></span></a></th>
            <th><a href="<?php echo $controller->build_query_url(['sort_by' => 'done_date', 'sort_dir' => $sort_by === 'done_date' && $sort_dir === 'ASC' ? 'DESC' : 'ASC', 'page' => 1]); ?>">Дата<br>видачі <span class="sort_indicator"><?php echo $sort_by === 'done_date' ? ($sort_dir === 'ASC' ? '▲' : '▼') : ''; ?></span></a></th>
        </tr>
        <?php $controller->render_html_rows($page, $per_page, $status_filter, $sort_by, $sort_dir); ?>
    </table>
    <?php $controller->render_pagination($page, $per_page, $status_filter, $sort_by, $sort_dir); ?>
</div>

<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/views/layouts/_main_footer.php');
?>
