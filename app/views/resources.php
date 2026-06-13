<?php
header('Location: /app/views/admin.php');
exit();

$model_clients = isset($params_database_main) ? new Clients($params_database_main) : new Clients();
$model_contacts = isset($params_database_main) ? new Contacts($params_database_main) : new Contacts();
$model_devices = isset($params_database_main) ? new Devices($params_database_main) : new Devices();

$clients = $model_clients->list_elements('clients');
$contacts = $model_contacts->list_elements('contacts');
$devices = $model_devices->list_elements('devices');
$contact_types = $model_contacts->list_elements('contact_types');
$device_types = $model_devices->list_elements('device_types');
?>

<!-- Modals for editing -->
<div id="modal_client_editor" class="modal">
    <div class="modal_header">
        <p>Редагування клієнта</p>
        <button data-close-button class="close-button">&times;</button>
    </div>
    <div class="modal_body">
        <form action="/app/controllers/resources.php" method="post">
            <input type="hidden" name="action" value="update_client">
            <input type="hidden" name="id" id="client_id">
            <input type="hidden" name="back_path" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
            
            <fieldset>
                <legend>Дані про клієнта</legend>
                <label for="client_first_name">Ім'я</label>
                <input id="client_first_name" class="editor_input" type="text" name="first_name" required>
                
                <label for="client_surname">Прізвище</label>
                <input id="client_surname" class="editor_input" type="text" name="surname" required>
                
                <label for="client_last_name">По батькові</label>
                <input id="client_last_name" class="editor_input" type="text" name="last_name" required>
            </fieldset>
            
            <fieldset class="controll_buttons">
                <input type="submit" class="button" value="Зберегти">
            </fieldset>
        </form>
    </div>
</div>

<div id="modal_contact_editor" class="modal">
    <div class="modal_header">
        <p>Редагування контакту</p>
        <button data-close-button class="close-button">&times;</button>
    </div>
    <div class="modal_body">
        <form action="/app/controllers/resources.php" method="post">
            <input type="hidden" name="action" value="update_contact">
            <input type="hidden" name="id" id="contact_id">
            <input type="hidden" name="back_path" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
            
            <fieldset>
                <legend>Дані про контакт</legend>
                <label for="contact_client_id">Клієнт ID</label>
                <input id="contact_client_id" class="editor_input" type="number" name="client_id" required>
                
                <label for="contact_type_id">Тип контакту</label>
                <select id="contact_type_id" class="editor_input" name="contact_type_id" required>
                    <?php foreach ($contact_types as $type): ?>
                        <option value="<?php echo $type['id']; ?>"><?php echo $type['name']; ?></option>
                    <?php endforeach; ?>
                </select>
                
                <label for="contact_contact">Контакт</label>
                <input id="contact_contact" class="editor_input" type="text" name="contact" required>
            </fieldset>
            
            <fieldset class="controll_buttons">
                <input type="submit" class="button" value="Зберегти">
            </fieldset>
        </form>
    </div>
</div>

<div id="modal_device_editor" class="modal">
    <div class="modal_header">
        <p>Редагування пристрою</p>
        <button data-close-button class="close-button">&times;</button>
    </div>
    <div class="modal_body">
        <form action="/app/controllers/resources.php" method="post">
            <input type="hidden" name="action" value="update_device">
            <input type="hidden" name="id" id="device_id">
            <input type="hidden" name="back_path" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
            
            <fieldset>
                <legend>Дані про пристрій</legend>
                <label for="device_client_id">Клієнт ID</label>
                <input id="device_client_id" class="editor_input" type="number" name="client_id" required>
                
                <label for="device_type_id">Тип пристрою</label>
                <select id="device_type_id" class="editor_input" name="device_type_id" required>
                    <?php foreach ($device_types as $type): ?>
                        <option value="<?php echo $type['id']; ?>"><?php echo $type['name']; ?></option>
                    <?php endforeach; ?>
                </select>
                
                <label for="device_description">Опис</label>
                <textarea id="device_description" class="editor_input" name="description" rows="4"></textarea>
            </fieldset>
            
            <fieldset class="controll_buttons">
                <input type="submit" class="button" value="Зберегти">
            </fieldset>
        </form>
    </div>
</div>

<div id="overlay"></div>

<!-- ================= CLIENTS SECTION ================= -->
<h2>Клієнти</h2>
<div class="panel">
    <form action="/app/controllers/resources.php" method="post">
        <input type="hidden" name="action" value="create_client">
        <input type="hidden" name="back_path" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
        
        <fieldset class="form_row">
            <div class="form_column">
                <label for="new_client_first_name">Ім'я</label>
                <input id="new_client_first_name" type="text" name="first_name" required>
            </div>
            <div class="form_column">
                <label for="new_client_surname">Прізвище</label>
                <input id="new_client_surname" type="text" name="surname" required>
            </div>
            <div class="form_column">
                <label for="new_client_last_name">По батькові</label>
                <input id="new_client_last_name" type="text" name="last_name" required>
            </div>
            <div class="form_column form_submit">
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
                    <form action="/app/controllers/resources.php" method="post" onsubmit="return confirm('Ви впевнені?');">
                        <input type="hidden" name="action" value="delete_client">
                        <input type="hidden" name="id" value="<?php echo $client['id']; ?>">
                        <input type="hidden" name="back_path" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                        <button type="submit" class="button button_danger button_small">🗑️ Видалити</button>
                    </form>
                </td>
            </tr>
        <?php $counter++; endforeach; ?>
    </table>
</div>

<!-- ================= CONTACTS SECTION ================= -->
<h2>Контакти</h2>
<div class="panel">
    <form action="/app/controllers/resources.php" method="post">
        <input type="hidden" name="action" value="create_contact">
        <input type="hidden" name="back_path" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
        
        <fieldset class="form_row">
            <div class="form_column">
                <label for="new_contact_client_id">Клієнт ID</label>
                <input id="new_contact_client_id" type="number" name="client_id" required>
            </div>
            <div class="form_column">
                <label for="new_contact_type_id">Тип контакту</label>
                <select id="new_contact_type_id" name="contact_type_id" required>
                    <?php foreach ($contact_types as $type): ?>
                        <option value="<?php echo $type['id']; ?>"><?php echo $type['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form_column">
                <label for="new_contact">Контакт</label>
                <input id="new_contact" type="text" name="contact" required>
            </div>
            <div class="form_column form_submit">
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
                <td><?php echo $contact['client_id']; ?></td>
                <td><?php echo $contact['type_id']; ?></td>
                <td><?php echo $contact['contact']; ?></td>
                <td class="action_buttons">
                    <button class="button button_primary button_small" onclick="open_contact_editor(<?php echo htmlspecialchars(json_encode($contact)); ?>)">✏️ Редагувати</button>
                    <form action="/app/controllers/resources.php" method="post" onsubmit="return confirm('Ви впевнені?');">
                        <input type="hidden" name="action" value="delete_contact">
                        <input type="hidden" name="id" value="<?php echo $contact['id']; ?>">
                        <input type="hidden" name="back_path" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                        <button type="submit" class="button button_danger button_small">🗑️ Видалити</button>
                    </form>
                </td>
            </tr>
        <?php $counter++; endforeach; ?>
    </table>
</div>

<!-- ================= DEVICES SECTION ================= -->
<h2>Пристрої</h2>
<div class="panel">
    <form action="/app/controllers/resources.php" method="post">
        <input type="hidden" name="action" value="create_device">
        <input type="hidden" name="back_path" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
        
        <fieldset class="form_row">
            <div class="form_column">
                <label for="new_device_client_id">Клієнт ID</label>
                <input id="new_device_client_id" type="number" name="client_id" required>
            </div>
            <div class="form_column">
                <label for="new_device_type_id">Тип пристрою</label>
                <select id="new_device_type_id" name="device_type_id" required>
                    <?php foreach ($device_types as $type): ?>
                        <option value="<?php echo $type['id']; ?>"><?php echo $type['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form_column form_submit">
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
                <td><?php echo $device['client_id']; ?></td>
                <td><?php echo $device['type_id']; ?></td>
                <td><?php echo $device['description'] ?? '-'; ?></td>
                <td class="action_buttons">
                    <button class="button button_primary button_small" onclick="open_device_editor(<?php echo htmlspecialchars(json_encode($device)); ?>)">✏️ Редагувати</button>
                    <form action="/app/controllers/resources.php" method="post" onsubmit="return confirm('Ви впевнені?');">
                        <input type="hidden" name="action" value="delete_device">
                        <input type="hidden" name="id" value="<?php echo $device['id']; ?>">
                        <input type="hidden" name="back_path" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                        <button type="submit" class="button button_danger button_small">🗑️ Видалити</button>
                    </form>
                </td>
            </tr>
        <?php $counter++; endforeach; ?>
    </table>
</div>

<script>
function open_client_editor(data) {
    document.getElementById('client_id').value = data.id;
    document.getElementById('client_first_name').value = data.first_name;
    document.getElementById('client_surname').value = data.surname;
    document.getElementById('client_last_name').value = data.last_name;
    document.getElementById('modal_client_editor').classList.add('active');
    document.getElementById('overlay').classList.add('active');
}

function open_contact_editor(data) {
    document.getElementById('contact_id').value = data.id;
    document.getElementById('contact_client_id').value = data.client_id;
    document.getElementById('contact_type_id').value = data.type_id;
    document.getElementById('contact_contact').value = data.contact;
    document.getElementById('modal_contact_editor').classList.add('active');
    document.getElementById('overlay').classList.add('active');
}

function open_device_editor(data) {
    document.getElementById('device_id').value = data.id;
    document.getElementById('device_client_id').value = data.client_id;
    document.getElementById('device_type_id').value = data.type_id;
    document.getElementById('device_description').value = data.description || '';
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
