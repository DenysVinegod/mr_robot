<?php
if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}

include ($_SERVER['DOCUMENT_ROOT'].'/configs/db.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/models/users.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/models/clients.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/models/contacts.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/models/devices.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/models/repairs.php');

$current_role = $_SESSION['account']['role_name'] ?? null;

if ($current_role !== 'superadmin') {
    $_SESSION['message']['error'] = 'Недостатньо прав для цієї дії.';
    header('Location: /');
    exit();
}

$model_users = isset($params_database_main) ? new Users($params_database_main) : new Users();
$model_clients = isset($params_database_main) ? new Clients($params_database_main) : new Clients();
$model_contacts = isset($params_database_main) ? new Contacts($params_database_main) : new Contacts();
$model_devices = isset($params_database_main) ? new Devices($params_database_main) : new Devices();
$model_repairs = isset($params_database_main) ? new Repairs($params_database_main) : new Repairs();

if (!isset($_POST['action'])) {
    header('Location: /');
    exit();
}

$action = $_POST['action'];

// Helper function for database queries
function execute_query($query) {
    global $params_database_main;
    $conn = new mysqli(
        $params_database_main['dbhost'] ?? 'localhost',
        $params_database_main['dbuser'] ?? 'mr_robot',
        $params_database_main['dbpass'] ?? '',
        $params_database_main['dbname'] ?? 'mr_robot'
    );
    $conn->query('SET NAMES utf8');
    $result = $conn->query($query);
    $conn->close();
    return $result;
}

function get_count(string $query): int {
    $result = execute_query($query);
    if ($result && $row = $result->fetch_assoc()) {
        return intval($row['count']);
    }
    return 0;
}

function get_rows(string $query): array {
    $result = execute_query($query);
    $rows = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    }
    return $rows;
}

function implode_ids(array $rows, string $field = 'id', int $limit = 5): string {
    $values = array_column($rows, $field);
    if (empty($values)) {
        return '';
    }
    $short = array_slice($values, 0, $limit);
    $suffix = count($values) > $limit ? ', ...' : '';
    return implode(', ', $short) . $suffix;
}

function redirect_back(array $data = []) {
    $back_path = $_POST['back_path'] ?? '/app/views/admin.php';
    $tab = $_POST['tab'] ?? null;
    if (!empty($data)) {
        foreach ($data as $key => $value) {
            $_SESSION['message'][$key] = $value;
        }
    }
    $path = parse_url($back_path, PHP_URL_PATH) ?: '/app/views/admin.php';
    if ($tab) {
        $back_path = $path . '?tab=' . urlencode($tab);
    } else {
        $back_path = $path;
    }
    header("Location: {$back_path}");
    exit();
}

// ============= USERS =============
if ($action === 'create_user') {
    if (isset($_POST['login'], $_POST['password'], $_POST['role_id'])) {
        if ($model_users->save_new_user($_POST)) {
            redirect_back(['info' => 'Користувача створено.']);
        }
    }
    redirect_back(['error' => 'Не вдалося створити користувача.']);
}

if ($action === 'update_user') {
    if (isset($_POST['id'], $_POST['login'], $_POST['role_id'])) {
        if ($model_users->update_user($_POST)) {
            redirect_back(['info' => 'Користувача оновлено.']);
        }
    }
    redirect_back(['error' => 'Не вдалося оновити користувача.']);
}

if ($action === 'delete_user') {
    if (isset($_POST['id'])) {
        $id = intval($_POST['id']);
        $repairCount = get_count("SELECT COUNT(*) AS `count` FROM `repairs` WHERE `manager_id` = {$id};");

        if ($repairCount > 0) {
            $repairRows = get_rows("SELECT `id` FROM `repairs` WHERE `manager_id` = {$id} ORDER BY `id` ASC LIMIT 10;");
            $repairIds = implode_ids($repairRows);
            redirect_back(['error' => "Користувача не можна видалити — знайдено {$repairCount} ремонт(ів) (IDs: {$repairIds}). Розгляньте каскадне видалення."]);
        }

        if ($model_users->delete_user($id)) {
            redirect_back(['info' => 'Користувача видалено.']);
        }
    }
    redirect_back(['error' => 'Не вдалося видалити користувача.']);
}

if ($action === 'delete_user_cascade') {
    if (isset($_POST['id'])) {
        $id = intval($_POST['id']);
        execute_query("DELETE FROM `repairs` WHERE `manager_id` = {$id};");
        if ($model_users->delete_user($id)) {
            redirect_back(['info' => 'Користувача і пов’язані ремонти видалено.']);
        }
    }
    redirect_back(['error' => 'Не вдалося виконати каскадне видалення користувача.']);
}

// ============= CLIENTS =============
if ($action === 'create_client') {
    if (isset($_POST['first_name'], $_POST['surname'], $_POST['last_name'])) {
        if ($model_clients->save_new_user($_POST)) {
            redirect_back(['info' => 'Клієнта створено.']);
        }
    }
    redirect_back(['error' => 'Не вдалося створити клієнта.']);
}

if ($action === 'update_client') {
    if (isset($_POST['id'], $_POST['first_name'], $_POST['surname'], $_POST['last_name'])) {
        $id = intval($_POST['id']);
        $first_name = $_POST['first_name'];
        $surname = $_POST['surname'];
        $last_name = $_POST['last_name'];
        
        $query = "UPDATE `clients` SET `first_name` = '{$first_name}', `surname` = '{$surname}', `last_name` = '{$last_name}' WHERE `id` = {$id};";
        
        if (execute_query($query)) {
            redirect_back(['info' => 'Клієнта оновлено.']);
        }
    }
    redirect_back(['error' => 'Не вдалося оновити клієнта.']);
}

if ($action === 'delete_client') {
    if (isset($_POST['id'])) {
        $id = intval($_POST['id']);

        $repairsCount = get_count("SELECT COUNT(*) AS `count` FROM `repairs` WHERE `client_id` = {$id};");
        $contactsCount = get_count("SELECT COUNT(*) AS `count` FROM `contacts` WHERE `client_id` = {$id};");
        $devicesCount = get_count("SELECT COUNT(*) AS `count` FROM `devices` WHERE `client_id` = {$id};");

        if ($repairsCount > 0 || $contactsCount > 0 || $devicesCount > 0) {
            $details = [];
            if ($repairsCount > 0) {
                $repairRows = get_rows("SELECT `id` FROM `repairs` WHERE `client_id` = {$id} ORDER BY `id` ASC LIMIT 10;");
                $repairIds = implode_ids($repairRows);
                $details[] = "ремонтів: {$repairsCount} (IDs: {$repairIds})";
            }
            if ($contactsCount > 0) {
                $contactRows = get_rows("SELECT `id` FROM `contacts` WHERE `client_id` = {$id} ORDER BY `id` ASC LIMIT 10;");
                $contactIds = implode_ids($contactRows);
                $details[] = "контактів: {$contactsCount} (IDs: {$contactIds})";
            }
            if ($devicesCount > 0) {
                $deviceRows = get_rows("SELECT `id`, `description` FROM `devices` WHERE `client_id` = {$id} ORDER BY `id` ASC LIMIT 10;");
                $deviceSummaries = array_map(function($row) {
                    $label = trim($row['description']) ?: 'Пристрій';
                    return "{$row['id']}: {$label}";
                }, $deviceRows);
                $devicesList = implode(', ', array_slice($deviceSummaries, 0, 5));
                if (count($deviceSummaries) > 5) {
                    $devicesList .= ', ...';
                }
                $details[] = "пристроїв: {$devicesCount} (IDs: {$devicesList})";
            }
            $detailText = implode('; ', $details);
            redirect_back(['error' => "Клієнта не можна видалити — знайдено залежні записи ({$detailText}). Розгляньте каскадне видалення."]);
        }

        $query = "DELETE FROM `clients` WHERE `id` = {$id} LIMIT 1;";
        if (execute_query($query)) {
            redirect_back(['info' => 'Клієнта видалено.']);
        }
    }
    redirect_back(['error' => 'Не вдалося видалити клієнта.']);
}

if ($action === 'delete_client_cascade') {
    if (isset($_POST['id'])) {
        $id = intval($_POST['id']);
        execute_query("DELETE FROM `repairs` WHERE `client_id` = {$id};");
        execute_query("DELETE FROM `contacts` WHERE `client_id` = {$id};");
        execute_query("DELETE FROM `devices` WHERE `client_id` = {$id};");
        $query = "DELETE FROM `clients` WHERE `id` = {$id} LIMIT 1;";
        if (execute_query($query)) {
            redirect_back(['info' => 'Клієнта та всі пов’язані записи видалено.']);
        }
    }
    redirect_back(['error' => 'Не вдалося виконати каскадне видалення клієнта.']);
}

// ============= CONTACTS =============
if ($action === 'create_contact') {
    if (isset($_POST['client_id'], $_POST['contact_type_id'], $_POST['contact'])) {
        if ($model_contacts->save_new_contact($_POST)) {
            redirect_back(['info' => 'Контакт створено.']);
        }
    }
    redirect_back(['error' => 'Не вдалося створити контакт.']);
}

if ($action === 'update_contact') {
    if (isset($_POST['id'], $_POST['client_id'], $_POST['contact_type_id'], $_POST['contact'])) {
        $id = intval($_POST['id']);
        $client_id = intval($_POST['client_id']);
        $contact_type_id = intval($_POST['contact_type_id']);
        $contact = $_POST['contact'];
        
        $query = "UPDATE `contacts` SET `client_id` = {$client_id}, `type_id` = {$contact_type_id}, `contact` = '{$contact}' WHERE `id` = {$id};";
        
        if (execute_query($query)) {
            redirect_back(['info' => 'Контакт оновлено.']);
        }
    }
    redirect_back(['error' => 'Не вдалося оновити контакт.']);
}

if ($action === 'delete_contact') {
    if (isset($_POST['id'])) {
        $id = intval($_POST['id']);
        $repairsCount = get_count("SELECT COUNT(*) AS `count` FROM `repairs` WHERE `contact_id` = {$id};");

        if ($repairsCount > 0) {
            $repairRows = get_rows("SELECT `id` FROM `repairs` WHERE `contact_id` = {$id} ORDER BY `id` ASC LIMIT 10;");
            $repairIds = implode_ids($repairRows);
            redirect_back(['error' => "Контакт не можна видалити — знайдено {$repairsCount} ремонт(ів) (IDs: {$repairIds}). Розгляньте каскадне видалення."]);
        }

        $query = "DELETE FROM `contacts` WHERE `id` = {$id} LIMIT 1;";
        if (execute_query($query)) {
            redirect_back(['info' => 'Контакт видалено.']);
        }
    }
    redirect_back(['error' => 'Не вдалося видалити контакт.']);
}

if ($action === 'delete_contact_cascade') {
    if (isset($_POST['id'])) {
        $id = intval($_POST['id']);
        execute_query("DELETE FROM `repairs` WHERE `contact_id` = {$id};");
        $query = "DELETE FROM `contacts` WHERE `id` = {$id} LIMIT 1;";
        if (execute_query($query)) {
            redirect_back(['info' => 'Контакт та пов’язані ремонти видалено.']);
        }
    }
    redirect_back(['error' => 'Не вдалося виконати каскадне видалення контакту.']);
}

// ============= DEVICES =============
if ($action === 'create_device') {
    if (isset($_POST['client_id'], $_POST['device_type_id'])) {
        if ($model_devices->save_new_device($_POST)) {
            redirect_back(['info' => 'Пристрій створено.']);
        }
    }
    redirect_back(['error' => 'Не вдалося створити пристрій.']);
}

if ($action === 'update_device') {
    if (isset($_POST['id'], $_POST['client_id'], $_POST['device_type_id'])) {
        $id = intval($_POST['id']);
        $client_id = intval($_POST['client_id']);
        $device_type_id = intval($_POST['device_type_id']);
        $description = isset($_POST['description']) ? $_POST['description'] : '';
        
        $query = "UPDATE `devices` SET `client_id` = {$client_id}, `type_id` = {$device_type_id}, `description` = '{$description}' WHERE `id` = {$id};";
        
        if (execute_query($query)) {
            redirect_back(['info' => 'Пристрій оновлено.']);
        }
    }
    redirect_back(['error' => 'Не вдалося оновити пристрій.']);
}

if ($action === 'delete_device') {
    if (isset($_POST['id'])) {
        $id = intval($_POST['id']);
        $repairsCount = get_count("SELECT COUNT(*) AS `count` FROM `repairs` WHERE `device_id` = {$id};");

        if ($repairsCount > 0) {
            $repairRows = get_rows("SELECT `id` FROM `repairs` WHERE `device_id` = {$id} ORDER BY `id` ASC LIMIT 10;");
            $repairIds = implode_ids($repairRows);
            redirect_back(['error' => "Пристрій не можна видалити — знайдено {$repairsCount} ремонт(ів) (IDs: {$repairIds}). Розгляньте каскадне видалення."]);
        }

        $query = "DELETE FROM `devices` WHERE `id` = {$id} LIMIT 1;";
        if (execute_query($query)) {
            redirect_back(['info' => 'Пристрій видалено.']);
        }
    }
    redirect_back(['error' => 'Не вдалося видалити пристрій.']);
}

if ($action === 'delete_device_cascade') {
    if (isset($_POST['id'])) {
        $id = intval($_POST['id']);
        execute_query("DELETE FROM `repairs` WHERE `device_id` = {$id};");
        $query = "DELETE FROM `devices` WHERE `id` = {$id} LIMIT 1;";
        if (execute_query($query)) {
            redirect_back(['info' => 'Пристрій та пов’язані ремонти видалено.']);
        }
    }
    redirect_back(['error' => 'Не вдалося виконати каскадне видалення пристрою.']);
}

// ============= REPAIRS =============
if ($action === 'delete_repair') {
    if (isset($_POST['id'])) {
        $id = intval($_POST['id']);
        $query = "DELETE FROM `repairs` WHERE `id` = {$id} LIMIT 1;";
        
        if (execute_query($query)) {
            redirect_back(['info' => 'Ремонт видалено.']);
        }
    }
    redirect_back(['error' => 'Не вдалося видалити ремонт.']);
}

header('Location: /app/views/admin.php');
exit();
?>

