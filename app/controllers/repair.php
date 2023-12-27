<?php
if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}

require_once ($_SERVER['DOCUMENT_ROOT'].'/app/models/repairs.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/models/clients.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/models/devices.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/models/statuses.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/models/contacts.php');

include ($_SERVER['DOCUMENT_ROOT'].'/configs/db.php');

$model_repairs 
    = isset($params_database_main) 
    ? new Repairs($params_database_main) 
    : new Repairs();

$model_clients 
    = isset($params_database_main) 
    ? new Clients($params_database_main) 
    : new Clients();

$model_contacts 
    = isset($params_database_main) 
    ? new Contacts($params_database_main) 
    : new Contacts();

$model_devices 
    = isset($params_database_main) 
    ? new Devices($params_database_main) 
    : new Devices();
$model_statuses 
    = isset($params_database_main) 
    ? new Statuses($params_database_main) 
    : new Statuses();

class Repair {
    function __construct() {
        global $model_repairs;
        $this -> model = $model_repairs;
    }

    function render_html_rows(): void{
        $result = $this -> model -> list_repairs();
        $row_counter = 1;
        foreach ($result as $value) {
            if ($row_counter % 2 == 0) echo "<tr id='repair_{$value['id']}' class='even list_line'>"; 
            else echo "<tr id='repair_{$value['id']}' class='odd list_line'>";
            echo "<td>".$value['id']."</td>";
            echo "<td>".$value['status']."</td>";
            echo "<td>"
                .$value['surname']
                ." "
                .$value['first_name']
                ." "
                .$value['last_name']
                ."</td>";
            echo "<td>"
                .$value['contact_type']
                .": ".$value['contact']
                ."</td>";
            echo "<td>".$value['device_name']."</td>";
            echo "<td>".$value['description']."</td>";
            echo "<td>".$value['price']."</td>";
            echo "<td>".$value['master_conclusion']."</td>";
            echo "<td>".$value['register_date']."</td>";
            echo "<td>".$value['done_date']."</td>";
            echo "<td class='js_full_info' style='display: none;'>"
                .json_encode($value)
                ."</td>";
            echo "</tr>";
            $row_counter++;
        }
    }
}

if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'create_new_repair':
            if (!$model_clients -> get_client_id($_POST)) {
                $model_clients -> save_new_user($_POST);
                $_SESSION['message']['info'] = "Створено нового клієнта у БД.";
            }
            $_POST['client_id'] = $model_clients -> get_client_id($_POST);
            if (!$model_contacts -> get_contact_id($_POST)) {
                $model_contacts -> save_new_contact($_POST);
                if (isset($_SESSION['message']['info'])) {
                    $_SESSION['message']['info'] 
                        .= "<hr>Створено новий контакт у БД.";
                } else $_SESSION['message']['info'] 
                    = "Створено новий контакт у БД.";
            }
            $_POST['contact_id'] = $model_contacts -> get_contact_id($_POST);
            if (!$model_devices -> get_device_id($_POST)) {
                $model_devices -> save_new_device($_POST);
                if (isset($_SESSION['message']['info'])) {
                    $_SESSION['message']['info'] 
                        .= "<hr>Створено новий пристрій у БД.";
                } else $_SESSION['message']['info'] 
                    = "Створено новий пристрій у БД.";
            }
            $_POST['device_id'] = $model_devices -> get_device_id($_POST);
            $_POST['register_date'] = explode("T", $_POST['register_date']);
            $_POST['register_date'] 
                = $_POST['register_date'][0] 
                . " " 
                . $_POST['register_date'][1];
            $controller = new Repair();
            $_POST['id'] = $controller -> model -> save_new_repair($_POST);
            if (isset($_SESSION['message']['info'])) {
                $_SESSION['message']['info'] 
                    .= "<hr>Створено нову заявку на ремонт #{$_POST['id']}.";
            } else $_SESSION['message']['info'] 
                = "Створено нову заявку на ремонт #{$_POST['id']}.";
            header("Location: {$_POST['back_path']}");
            break;
        
        case 'edit_repair':
            $_POST['status_id'] = $_POST['status'];
            unset($_POST['status']);
            if (!$model_clients -> get_client_id($_POST)) {
                $model_clients -> save_new_user($_POST);
                $_SESSION['message']['info'] = "Створено нового клієнта у БД.";
            }
            $_POST['client_id'] = $model_clients -> get_client_id($_POST);
            if (!$model_contacts -> get_contact_id($_POST)) {
                $model_contacts -> save_new_contact($_POST);
                if (isset($_SESSION['message']['info'])) {
                    $_SESSION['message']['info'] 
                        .= "<hr>Створено новий контакт у БД.";
                } else $_SESSION['message']['info'] 
                    = "Створено новий контакт у БД.";
            }
            $_POST['contact_id'] = $model_contacts -> get_contact_id($_POST);
            if (!$model_devices -> get_device_id($_POST)) {
                $model_devices -> save_new_device($_POST);
                if (isset($_SESSION['message']['info'])) {
                    $_SESSION['message']['info'] 
                        .= "<hr>Створено новий пристрій у БД.";
                } else $_SESSION['message']['info'] 
                    = "Створено новий пристрій у БД.";
            }
            $_POST['device_id'] = $model_devices -> get_device_id($_POST);
            $controller = new Repair();
            $controller -> model -> update_repair($_POST);
            if (isset($_SESSION['message']['info'])) {
                $_SESSION['message']['info'] 
                    .= "<hr>Відредаговано заявку на ремонт #{$_POST['id']}.";
            } else $_SESSION['message']['info'] 
                = "Відредаговано заявку на ремонт #{$_POST['id']}.";
            header("Location: {$_POST['back_path']}");
            break;

        default:
            # code...
            break;
    }
}
?>
