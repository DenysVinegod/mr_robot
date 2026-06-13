<?php
if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}

$current_role = $_SESSION['account']['role_name'] ?? null;

if ($current_role !== 'superadmin') {
    $_SESSION['message']['error'] = 'Недостатньо прав для цієї дії.';
    header('Location: /');
    exit();
}

include ($_SERVER['DOCUMENT_ROOT'].'/configs/db.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/views/layouts/_main_header.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/models/users.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/models/clients.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/models/contacts.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/models/devices.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/models/repairs.php');

$model_users = isset($params_database_main) ? new Users($params_database_main) : new Users();
$model_clients = isset($params_database_main) ? new Clients($params_database_main) : new Clients();
$model_contacts = isset($params_database_main) ? new Contacts($params_database_main) : new Contacts();
$model_devices = isset($params_database_main) ? new Devices($params_database_main) : new Devices();
$model_repairs = isset($params_database_main) ? new Repairs($params_database_main) : new Repairs();

$users = $model_users->list_all_users();
$clients = $model_clients->list_elements('clients');
$contacts = $model_contacts->list_elements('contacts');
$devices = $model_devices->list_elements('devices');
$repairs = $model_repairs->list_repairs(0, 1000, 0);
$contact_types = $model_contacts->list_elements('contact_types');
$device_types = $model_devices->list_elements('device_types');
$roles = $model_users->list_elements('roles');

$clients_by_id = [];
foreach ($clients as $client) {
    $clients_by_id[$client['id']] = trim($client['first_name'] . ' ' . $client['surname'] . ' ' . $client['last_name']);
}

$contact_types_by_id = [];
foreach ($contact_types as $type) {
    $contact_types_by_id[$type['id']] = $type['name'];
}

$device_types_by_id = [];
foreach ($device_types as $type) {
    $device_types_by_id[$type['id']] = $type['name'];
}
?>

<!-- ================= USER EDIT MODAL ================= -->
<div id="modal_user_editor" class="modal">
    <div class="modal_header">
        <p>Редагування користувача</p>
        <button data-close-button class="close-button">&times;</button>
    </div>
    <div class="modal_body">
        <form action="/app/controllers/admin.php" method="post">
            <input type="hidden" name="action" value="update_user">
            <input type="hidden" name="id" id="user_id">
            <input type="hidden" name="back_path" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
            
            <fieldset>
                <legend>Дані користувача</legend>
                <label for="user_login">Логін</label>
                <input id="user_login" class="editor_input" type="text" name="login" required>
                
                <label for="user_password">Новий пароль (залиште пусто щоб не змінювати)</label>
                <input id="user_password" class="editor_input" type="password" name="password">
                
                <label for="user_role_id">Роль</label>
                <select id="user_role_id" class="editor_input" name="role_id" required>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?php echo $role['id']; ?>"><?php echo $role['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </fieldset>
            
            <fieldset class="controll_buttons">
                <input type="submit" class="button" value="Зберегти">
            </fieldset>
        </form>
    </div>
</div>

<!-- ================= CLIENT EDIT MODAL ================= -->
<div id="modal_client_editor" class="modal">
    <div class="modal_header">
        <p>Редагування клієнта</p>
        <button data-close-button class="close-button">&times;</button>
    </div>
    <div class="modal_body">
        <form action="/app/controllers/admin.php" method="post">
            <input type="hidden" name="action" value="update_client">
            <input type="hidden" name="id" id="client_id">
            <input type="hidden" name="back_path" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
            
            <fieldset>
                <legend>Дані про клієнта</legend>
                <label for="admin_client_first_name">Ім'я</label>
                <input id="admin_client_first_name" class="editor_input" type="text" name="first_name" required>
                
                <label for="admin_client_surname">Прізвище</label>
                <input id="admin_client_surname" class="editor_input" type="text" name="surname" required>
                
                <label for="admin_client_last_name">По батькові</label>
                <input id="admin_client_last_name" class="editor_input" type="text" name="last_name" required>
            </fieldset>
            
            <fieldset class="controll_buttons">
                <input type="submit" class="button" value="Зберегти">
            </fieldset>
        </form>
    </div>
</div>

<!-- ================= CONTACT EDIT MODAL ================= -->
<div id="modal_contact_editor" class="modal">
    <div class="modal_header">
        <p>Редагування контакту</p>
        <button data-close-button class="close-button">&times;</button>
    </div>
    <div class="modal_body">
        <form action="/app/controllers/admin.php" method="post">
            <input type="hidden" name="action" value="update_contact">
            <input type="hidden" name="id" id="admin_contact_id">
            <input type="hidden" name="back_path" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
            
            <fieldset>
                <legend>Дані про контакт</legend>
                <label for="admin_contact_client_id">Клієнт ID</label>
                <input id="admin_contact_client_id" class="editor_input" type="number" name="client_id" required>
                
                <label for="admin_contact_type_id">Тип контакту</label>
                <select id="admin_contact_type_id" class="editor_input" name="contact_type_id" required>
                    <?php foreach ($contact_types as $type): ?>
                        <option value="<?php echo $type['id']; ?>"><?php echo $type['name']; ?></option>
                    <?php endforeach; ?>
                </select>
                
                <label for="admin_contact_contact">Контакт</label>
                <input id="admin_contact_contact" class="editor_input" type="text" name="contact" required>
            </fieldset>
            
            <fieldset class="controll_buttons">
                <input type="submit" class="button" value="Зберегти">
            </fieldset>
        </form>
    </div>
</div>

<!-- ================= DEVICE EDIT MODAL ================= -->
<div id="modal_device_editor" class="modal">
    <div class="modal_header">
        <p>Редагування пристрою</p>
        <button data-close-button class="close-button">&times;</button>
    </div>
    <div class="modal_body">
        <form action="/app/controllers/admin.php" method="post">
            <input type="hidden" name="action" value="update_device">
            <input type="hidden" name="id" id="admin_device_id">
            <input type="hidden" name="back_path" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
            
            <fieldset>
                <legend>Дані про пристрій</legend>
                <label for="admin_device_client_id">Клієнт ID</label>
                <input id="admin_device_client_id" class="editor_input" type="number" name="client_id" required>
                
                <label for="admin_device_type_id">Тип пристрою</label>
                <select id="admin_device_type_id" class="editor_input" name="device_type_id" required>
                    <?php foreach ($device_types as $type): ?>
                        <option value="<?php echo $type['id']; ?>"><?php echo $type['name']; ?></option>
                    <?php endforeach; ?>
                </select>
                
                <label for="admin_device_description">Опис</label>
                <textarea id="admin_device_description" class="editor_input" name="description" rows="4"></textarea>
            </fieldset>
            
            <fieldset class="controll_buttons">
                <input type="submit" class="button" value="Зберегти">
            </fieldset>
        </form>
    </div>
</div>

<div id="overlay"></div>

<h1>Адміністрація</h1>

<div class="admin_tab_row">
    <div class="admin_tabs">
        <button class="admin_tab_btn active" data-tab="users" onclick="show_tab('users', this)">Користувачі</button>
        <button class="admin_tab_btn" data-tab="clients" onclick="show_tab('clients', this)">Клієнти</button>
        <button class="admin_tab_btn" data-tab="contacts" onclick="show_tab('contacts', this)">Контакти</button>
        <button class="admin_tab_btn" data-tab="devices" onclick="show_tab('devices', this)">Пристрої</button>
        <button class="admin_tab_btn" data-tab="repairs" onclick="show_tab('repairs', this)">Ремонти</button>
    </div>
    <div class="pagination_container">
        <div class="pagination">
            <button id="admin_prev_page" class="pagination_button" type="button">«</button>
            <span id="admin_page_info">1 / 1</span>
            <button id="admin_next_page" class="pagination_button" type="button">»</button>
        </div>
    </div>
</div>

<!-- ================= USERS SECTION ================= -->
<div id="users" class="admin_tab_content active">
<h2>Користувачі</h2>
<div class="admin_panel">
    <form class="admin_form" action="/app/controllers/admin.php" method="post">
        <input type="hidden" name="action" value="create_user">
        <input type="hidden" name="back_path" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
        <fieldset class="admin_form_row">
            <div class="admin_form_column">
                <label for="new_user_login">Логін</label>
                <input id="new_user_login" type="text" name="login" required>
            </div>
            <div class="admin_form_column">
                <label for="new_user_password">Пароль</label>
                <input id="new_user_password" type="password" name="password" required>
            </div>
            <div class="admin_form_column">
                <label for="new_user_role_id">Роль</label>
                <select id="new_user_role_id" name="role_id" required>
                    <option value="">Виберіть роль</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?php echo $role['id']; ?>"><?php echo $role['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="admin_form_column admin_form_submit">
                <button type="submit" class="button button_primary">Створити</button>
            </div>
        </fieldset>
    </form>
</div>

<div class="repairs_table_container">
    <table>
        <tr class="headers">
            <th>ID</th>
            <th>Логін</th>
            <th>Роль</th>
            <th>Дії</th>
        </tr>
        <?php $counter = 1; foreach ($users as $user): ?>
            <tr class="<?php echo $counter % 2 == 0 ? 'even' : 'odd'; ?> list_line">
                <td><?php echo $user['id']; ?></td>
                <td><?php echo $user['login']; ?></td>
                <td><?php echo $user['role_name'] ?? 'Не встановлена'; ?></td>
                <td class="action_buttons">
                    <button class="button button_primary button_small" onclick="open_user_editor(<?php echo htmlspecialchars(json_encode($user)); ?>)">✏️ Редагувати</button>
                    <form action="/app/controllers/admin.php" method="post" onsubmit="return confirm('Ви впевнені?');">
                        <input type="hidden" name="action" value="delete_user">
                        <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                        <input type="hidden" name="back_path" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                        <button type="submit" class="button button_danger button_small">🗑️ Видалити</button>
                    </form>
                    <form action="/app/controllers/admin.php" method="post" onsubmit="return confirm('Каскадне видалення видалить усі ремонти, пов’язані з цим користувачем. Ви впевнені?');">
                        <input type="hidden" name="action" value="delete_user_cascade">
                        <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                        <input type="hidden" name="back_path" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                        <button type="submit" class="button button_secondary button_small">⚠️ Каскадне видалення</button>
                    </form>
                </td>
            </tr>
        <?php $counter++; endforeach; ?>
    </table>
</div>
</div>

<!-- ================= CLIENTS SECTION ================= -->
<div id="clients" class="admin_tab_content">
<h2>Клієнти</h2>
<div class="admin_panel">
    <form class="admin_form" action="/app/controllers/admin.php" method="post">
        <input type="hidden" name="action" value="create_client">
        <input type="hidden" name="back_path" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
        
        <fieldset class="admin_form_row">
            <div class="admin_form_column">
                <label for="admin_new_client_first_name">Ім'я</label>
                <input id="admin_new_client_first_name" type="text" name="first_name" required>
            </div>
            <div class="admin_form_column">
                <label for="admin_new_client_surname">Прізвище</label>
                <input id="admin_new_client_surname" type="text" name="surname" required>
            </div>
            <div class="admin_form_column">
                <label for="admin_new_client_last_name">По батькові</label>
                <input id="admin_new_client_last_name" type="text" name="last_name" required>
            </div>
            <div class="admin_form_column admin_form_submit">
                <button type="submit" class="button button_primary">Створити</button>
            </div>
        </fieldset>
    </form>
</div>

<div class="repairs_table_container">
    <table>
        <tr class="headers">
            <th>ID</th>
            <th>Ім'я</th>
            <th>Прізвище</th>
            <th>По батькові</th>
            <th>Дії</th>
        </tr>
        <?php $counter = 1; foreach ($clients as $client): ?>
            <tr class="<?php echo $counter % 2 == 0 ? 'even' : 'odd'; ?> list_line">
                <td><?php echo $client['id']; ?></td>
                <td><?php echo $client['first_name']; ?></td>
                <td><?php echo $client['surname']; ?></td>
                <td><?php echo $client['last_name']; ?></td>
                <td class="action_buttons">
                    <button class="button button_primary button_small" onclick="open_client_editor(<?php echo htmlspecialchars(json_encode($client)); ?>)">✏️ Редагувати</button>
                    <form action="/app/controllers/admin.php" method="post" onsubmit="return confirm('Ви впевнені?');">
                        <input type="hidden" name="action" value="delete_client">
                        <input type="hidden" name="id" value="<?php echo $client['id']; ?>">
                        <input type="hidden" name="back_path" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                        <button type="submit" class="button button_danger button_small">🗑️ Видалити</button>
                    </form>
                    <form action="/app/controllers/admin.php" method="post" onsubmit="return confirm('Каскадне видалення видалить всі ремонти, контакти та пристрої цього клієнта. Ви впевнені?');">
                        <input type="hidden" name="action" value="delete_client_cascade">
                        <input type="hidden" name="id" value="<?php echo $client['id']; ?>">
                        <input type="hidden" name="back_path" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                        <button type="submit" class="button button_secondary button_small">⚠️ Каскадне видалення</button>
                    </form>
                </td>
            </tr>
        <?php $counter++; endforeach; ?>
    </table>
</div>
</div>

<!-- ================= CONTACTS SECTION ================= -->
<div id="contacts" class="admin_tab_content">
<h2>Контакти</h2>
<div class="admin_panel">
    <form class="admin_form" action="/app/controllers/admin.php" method="post">
        <input type="hidden" name="action" value="create_contact">
        <input type="hidden" name="back_path" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
        
        <fieldset class="admin_form_row">
            <div class="admin_form_column">
                <label for="admin_new_contact_client_id">Клієнт ID</label>
                <input id="admin_new_contact_client_id" type="number" name="client_id" required>
            </div>
            <div class="admin_form_column">
                <label for="admin_new_contact_type_id">Тип контакту</label>
                <select id="admin_new_contact_type_id" name="contact_type_id" required>
                    <?php foreach ($contact_types as $type): ?>
                        <option value="<?php echo $type['id']; ?>"><?php echo $type['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="admin_form_column">
                <label for="admin_new_contact">Контакт</label>
                <input id="admin_new_contact" type="text" name="contact" required>
            </div>
            <div class="admin_form_column admin_form_submit">
                <button type="submit" class="button button_primary">Створити</button>
            </div>
        </fieldset>
    </form>
</div>

<div class="repairs_table_container">
    <table>
        <tr class="headers">
            <th>ID</th>
            <th>Клієнт</th>
            <th>Тип</th>
            <th>Контакт</th>
            <th>Дії</th>
        </tr>
        <?php $counter = 1; foreach ($contacts as $contact): ?>
            <tr class="<?php echo $counter % 2 == 0 ? 'even' : 'odd'; ?> list_line">
                <td><?php echo $contact['id']; ?></td>
                <td><?php echo $clients_by_id[$contact['client_id']] ?? $contact['client_id']; ?></td>
                <td><?php echo $contact_types_by_id[$contact['type_id']] ?? $contact['type_id']; ?></td>
                <td><?php echo $contact['contact']; ?></td>
                <td class="action_buttons">
                    <button class="button button_primary button_small" onclick="open_contact_editor(<?php echo htmlspecialchars(json_encode($contact)); ?>)">✏️ Редагувати</button>
                    <form action="/app/controllers/admin.php" method="post" onsubmit="return confirm('Ви впевнені?');">
                        <input type="hidden" name="action" value="delete_contact">
                        <input type="hidden" name="id" value="<?php echo $contact['id']; ?>">
                        <input type="hidden" name="back_path" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                        <button type="submit" class="button button_danger button_small">🗑️ Видалити</button>
                    </form>
                    <form action="/app/controllers/admin.php" method="post" onsubmit="return confirm('Каскадне видалення видалить всі ремонти з цим контактом. Ви впевнені?');">
                        <input type="hidden" name="action" value="delete_contact_cascade">
                        <input type="hidden" name="id" value="<?php echo $contact['id']; ?>">
                        <input type="hidden" name="back_path" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                        <button type="submit" class="button button_secondary button_small">⚠️ Каскадне видалення</button>
                    </form>
                </td>
            </tr>
        <?php $counter++; endforeach; ?>
    </table>
</div>
</div>

<!-- ================= DEVICES SECTION ================= -->
<div id="devices" class="admin_tab_content">
<h2>Пристрої</h2>
<div class="admin_panel">
    <form class="admin_form" action="/app/controllers/admin.php" method="post">
        <input type="hidden" name="action" value="create_device">
        <input type="hidden" name="back_path" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
        
        <fieldset class="admin_form_row">
            <div class="admin_form_column">
                <label for="admin_new_device_client_id">Клієнт ID</label>
                <input id="admin_new_device_client_id" type="number" name="client_id" required>
            </div>
            <div class="admin_form_column">
                <label for="admin_new_device_type_id">Тип пристрою</label>
                <select id="admin_new_device_type_id" name="device_type_id" required>
                    <?php foreach ($device_types as $type): ?>
                        <option value="<?php echo $type['id']; ?>"><?php echo $type['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="admin_form_column admin_form_submit">
                <button type="submit" class="button button_primary">Створити</button>
            </div>
        </fieldset>
    </form>
</div>

<div class="repairs_table_container">
    <table>
        <tr class="headers">
            <th>ID</th>
            <th>Клієнт</th>
            <th>Тип пристрою</th>
            <th>Опис</th>
            <th>Дії</th>
        </tr>
        <?php $counter = 1; foreach ($devices as $device): ?>
            <tr class="<?php echo $counter % 2 == 0 ? 'even' : 'odd'; ?> list_line">
                <td><?php echo $device['id']; ?></td>
                <td><?php echo $clients_by_id[$device['client_id']] ?? $device['client_id']; ?></td>
                <td><?php echo $device_types_by_id[$device['type_id']] ?? $device['type_id']; ?></td>
                <td><?php echo $device['description'] ?? '-'; ?></td>
                <td class="action_buttons">
                    <button class="button button_primary button_small" onclick="open_device_editor(<?php echo htmlspecialchars(json_encode($device)); ?>)">✏️ Редагувати</button>
                    <form action="/app/controllers/admin.php" method="post" onsubmit="return confirm('Ви впевнені?');">
                        <input type="hidden" name="action" value="delete_device">
                        <input type="hidden" name="id" value="<?php echo $device['id']; ?>">
                        <input type="hidden" name="back_path" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                        <button type="submit" class="button button_danger button_small">🗑️ Видалити</button>
                    </form>
                    <form action="/app/controllers/admin.php" method="post" onsubmit="return confirm('Каскадне видалення видалить всі ремонти з цим пристроєм. Ви впевнені?');">
                        <input type="hidden" name="action" value="delete_device_cascade">
                        <input type="hidden" name="id" value="<?php echo $device['id']; ?>">
                        <input type="hidden" name="back_path" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                        <button type="submit" class="button button_secondary button_small">⚠️ Каскадне видалення</button>
                    </form>
                </td>
            </tr>
        <?php $counter++; endforeach; ?>
    </table>
</div>
</div>

<!-- ================= REPAIRS SECTION ================= -->
<div id="repairs" class="admin_tab_content">
<h2>Ремонти</h2>
<div class="repairs_table_container">
    <table>
        <tr class="headers">
            <th>ID</th>
            <th>Статус</th>
            <th>Клієнт</th>
            <th>Контакт</th>
            <th>Пристрій</th>
            <th>Опис</th>
            <th>Дії</th>
        </tr>
        <?php $counter = 1; foreach ($repairs as $repair): ?>
            <tr class="<?php echo $counter % 2 == 0 ? 'even' : 'odd'; ?> list_line">
                <td><?php echo $repair['id']; ?></td>
                <td><?php echo $repair['status']; ?></td>
                <td><?php echo $repair['surname'] . ' ' . $repair['first_name'] . ' ' . $repair['last_name']; ?></td>
                <td><?php echo $repair['contact_type'] . ': ' . $repair['contact']; ?></td>
                <td><?php echo $repair['device_name']; ?></td>
                <td><?php echo $repair['description']; ?></td>
                <td class="action_buttons">
                    <a href="/app/views/repairs.php?edit_id=<?php echo $repair['id']; ?>" class="button button_primary button_small">✏️ Редагувати</a>
                    <form action="/app/controllers/admin.php" method="post" onsubmit="return confirm('Ви впевнені?');">
                        <input type="hidden" name="action" value="delete_repair">
                        <input type="hidden" name="id" value="<?php echo $repair['id']; ?>">
                        <input type="hidden" name="back_path" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                        <button type="submit" class="button button_danger button_small">🗑️ Видалити</button>
                    </form>
                </td>
            </tr>
        <?php $counter++; endforeach; ?>
    </table>
</div>
</div>

<script>
function getQueryParam(name) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name);
}

function setFormsTab(tab_name) {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        let input = form.querySelector('input[name="tab"]');
        if (!input) {
            input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'tab';
            form.appendChild(input);
        }
        input.value = tab_name;
    });
}

function show_tab(tab_name, button) {
    const tabs = document.querySelectorAll('.admin_tab_content');
    const buttons = document.querySelectorAll('.admin_tab_btn');

    tabs.forEach(tab => tab.classList.remove('active'));
    buttons.forEach(btn => btn.classList.remove('active'));

    const targetTab = document.getElementById(tab_name);
    if (targetTab) {
        targetTab.classList.add('active');
    }
    if (button) {
        button.classList.add('active');
    }
    setFormsTab(tab_name);
    resetPagination();
    updatePageInfo();
}

function getSearchFields() {
    return {
        users: [
            { value: 'login', label: 'Логін' },
            { value: 'role_name', label: 'Роль' }
        ],
        clients: [
            { value: 'first_name', label: 'Ім’я' },
            { value: 'surname', label: 'Прізвище' },
            { value: 'last_name', label: 'По батькові' }
        ],
        contacts: [
            { value: 'client', label: 'Клієнт' },
            { value: 'type', label: 'Тип' },
            { value: 'contact', label: 'Контакт' }
        ],
        devices: [
            { value: 'client', label: 'Клієнт' },
            { value: 'type', label: 'Тип пристрою' },
            { value: 'description', label: 'Опис' }
        ],
        repairs: [
            { value: 'id', label: 'ID' },
            { value: 'status', label: 'Статус' },
            { value: 'device_name', label: 'Пристрій' },
            { value: 'contact', label: 'Контакт' }
        ]
    };
}

function updateSearchControls(tab_name) {
    const fieldSelect = document.getElementById('admin_search_field');
    const fields = getSearchFields()[tab_name] || [];

    fieldSelect.innerHTML = '';
    fields.forEach(field => {
        const option = document.createElement('option');
        option.value = field.value;
        option.textContent = field.label;
        fieldSelect.appendChild(option);
    });
}

const pagination = {
    current: 1,
    pageSize: 10,
    total: 1
};

function resetPagination() {
    pagination.current = 1;
}

function updatePageInfo() {
    const pageInfo = document.getElementById('admin_page_info');
    pageInfo.textContent = `${pagination.current} / ${pagination.total}`;
}

function updatePaginationCount(tab_name) {
    const tab = document.getElementById(tab_name);
    if (!tab) return;
    
    const totalRows = tab.querySelectorAll('table tr.list_line').length;
    pagination.total = Math.max(1, Math.ceil(totalRows / pagination.pageSize));
    if (pagination.current > pagination.total) {
        pagination.current = pagination.total;
    }
    updatePageInfo();
}


function changePage(delta) {
    pagination.current = Math.max(1, Math.min(pagination.total, pagination.current + delta));
    updatePageInfo();
    const tabName = document.querySelector('.admin_tab_content.active')?.id || 'users';
    renderPagination(tabName);
}

function getQueryParam(name) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name);
}

function setFormsTab(tab_name) {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        let input = form.querySelector('input[name="tab"]');
        if (!input) {
            input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'tab';
            form.appendChild(input);
        }
        input.value = tab_name;
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const selectedTab = getQueryParam('tab');
    const initialTab = selectedTab || 'users';
    const button = document.querySelector(`.admin_tab_btn[data-tab="${initialTab}"]`);
    show_tab(initialTab, button);

    document.getElementById('admin_prev_page').addEventListener('click', () => changePage(-1));
    document.getElementById('admin_next_page').addEventListener('click', () => changePage(1));
});

function open_user_editor(data) {
    document.getElementById('user_id').value = data.id;
    document.getElementById('user_login').value = data.login;
    document.getElementById('user_role_id').value = data.role_id;
    document.getElementById('modal_user_editor').classList.add('active');
    document.getElementById('overlay').classList.add('active');
}

function open_client_editor(data) {
    document.getElementById('client_id').value = data.id;
    document.getElementById('admin_client_first_name').value = data.first_name;
    document.getElementById('admin_client_surname').value = data.surname;
    document.getElementById('admin_client_last_name').value = data.last_name;
    document.getElementById('modal_client_editor').classList.add('active');
    document.getElementById('overlay').classList.add('active');
}

function open_contact_editor(data) {
    document.getElementById('admin_contact_id').value = data.id;
    document.getElementById('admin_contact_client_id').value = data.client_id;
    document.getElementById('admin_contact_type_id').value = data.type_id;
    document.getElementById('admin_contact_contact').value = data.contact;
    document.getElementById('modal_contact_editor').classList.add('active');
    document.getElementById('overlay').classList.add('active');
}

function open_device_editor(data) {
    document.getElementById('admin_device_id').value = data.id;
    document.getElementById('admin_device_client_id').value = data.client_id;
    document.getElementById('admin_device_type_id').value = data.type_id;
    document.getElementById('admin_device_description').value = data.description || '';
    document.getElementById('modal_device_editor').classList.add('active');
    document.getElementById('overlay').classList.add('active');
}

// Close modal buttons
document.querySelectorAll('[data-close-button]').forEach(button => {
    button.addEventListener('click', () => {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => modal.classList.remove('active'));
        document.getElementById('overlay').classList.remove('active');
    });
});

// Close modal by clicking overlay
document.getElementById('overlay').addEventListener('click', () => {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => modal.classList.remove('active'));
    document.getElementById('overlay').classList.remove('active');
});
</script>

<?php require_once ($_SERVER['DOCUMENT_ROOT'].'/app/views/layouts/_main_footer.php'); ?>
