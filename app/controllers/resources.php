<?php
if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}

header('Location: /app/views/admin.php');
exit();

$model_clients = isset($params_database_main) ? new Clients($params_database_main) : new Clients();
$model_contacts = isset($params_database_main) ? new Contacts($params_database_main) : new Contacts();
$model_devices = isset($params_database_main) ? new Devices($params_database_main) : new Devices();

$current_role = $_SESSION['account']['role_name'] ?? null;
$allowed_roles = ['superadmin', 'reception'];

if (!in_array($current_role, $allowed_roles)) {
    $_SESSION['message']['error'] = 'Недостатньо прав для цієї дії.';
    header('Location: /');
    exit();
}

function redirect_back(array $data = []) {
    $back_path = $_POST['back_path'] ?? '/';
    if (!empty($data)) {
        foreach ($data as $key => $value) {
            $_SESSION['message'][$key] = $value;
        }
    }
    header("Location: {$back_path}");
    exit();
}

if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'create_client':
            if (isset($_POST['first_name'], $_POST['surname'], $_POST['last_name'])) {
                if ($model_clients->save_new_user($_POST)) {
                    redirect_back(['info' => 'Клієнта створено.']);
                }
            }
            redirect_back(['error' => 'Не вдалося створити клієнта.']);
            break;

        case 'create_contact':
            if (isset($_POST['client_id'], $_POST['contact_type_id'], $_POST['contact'])) {
                if ($model_contacts->save_new_contact($_POST)) {
                    redirect_back(['info' => 'Контакт створено.']);
                }
            }
            redirect_back(['error' => 'Не вдалося створити контакт.']);
            break;

        case 'create_device':
            if (isset($_POST['client_id'], $_POST['device_type_id'])) {
                if ($model_devices->save_new_device($_POST)) {
                    redirect_back(['info' => 'Пристрій створено.']);
                }
            }
            redirect_back(['error' => 'Не вдалося створити пристрій.']);
            break;

        default:
            redirect_back(['error' => 'Невідома дія.']);
            break;
    }
}

header('Location: /');
exit();
