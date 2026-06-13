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

function redirect_back(array $data = []) {
    $back_path = $_POST['back_path'] ?? '/app/views/admin.php';
    if (!empty($data)) {
        foreach ($data as $key => $value) {
            $_SESSION['message'][$key] = $value;
        }
    }
    header("Location: {$back_path}");
    exit();
}

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
        if ($model_users->delete_user(intval($_POST['id']))) {
            redirect_back(['info' => 'Користувача видалено.']);
        }
    }
    redirect_back(['error' => 'Не вдалося видалити користувача.']);
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
        $query = "DELETE FROM `clients` WHERE `id` = {$id} LIMIT 1;";
        
        if (execute_query($query)) {
            redirect_back(['info' => 'Клієнта видалено.']);
        }
    }
    redirect_back(['error' => 'Не вдалося видалити клієнта.']);
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
        $query = "DELETE FROM `contacts` WHERE `id` = {$id} LIMIT 1;";
        
        if (execute_query($query)) {
            redirect_back(['info' => 'Контакт видалено.']);
        }
    }
    redirect_back(['error' => 'Не вдалося видалити контакт.']);
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
        $query = "DELETE FROM `devices` WHERE `id` = {$id} LIMIT 1;";
        
        if (execute_query($query)) {
            redirect_back(['info' => 'Пристрій видалено.']);
        }
    }
    redirect_back(['error' => 'Не вдалося видалити пристрій.']);
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

