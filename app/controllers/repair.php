<?php
if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}

require_once ($_SERVER['DOCUMENT_ROOT'].'/app/models/repairs.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/models/clients.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/models/devices.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/models/statuses.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/models/contacts.php');

$model_repairs  = new Repairs();
$model_clients  = new Clients();
$model_contacts = new Contacts();
$model_devices  = new Devices();
$model_statuses = new Statuses();

class Repair {
    public $model;
    
    function __construct() {
        global $model_repairs;
        $this -> model = $model_repairs;
    }

    function render_html_rows(): void{
        $repairs = $this -> model -> list_repairs();
        $row_counter = 1;
        foreach ($repairs as $repair) {
            $class_line = $row_counter++ % 2 == 0 ? 'even' : 'odd';
            echo "<tr id='repair_{$repair['id']}' class='{$class_line} list_line'>";
            echo "<td>".$repair['id']."</td>";
            echo "<td>".$repair['status']."</td>";
            echo "<td>"
                .$repair['surname']
                ." "
                .$repair['first_name']
                ." "
                .$repair['last_name']
                ."</td>";
            echo "<td>"
                .$repair['contact_type']
                .": ".$repair['contact']
                ."</td>";
            echo "<td>".$repair['device_name']."</td>";
            echo "<td>".$repair['description']."</td>";
            echo "<td>".$repair['price']."</td>";
            echo "<td>".$repair['master_conclusion']."</td>";
            echo "<td>".$repair['register_date']."</td>";
            echo "<td>".$repair['done_date']."</td>";
            echo "<td class='js_full_info' style='display: none;'>"
                .json_encode($repair)
                ."</td>";
            echo "</tr>";
        }
    }
}

if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'create_new_repair':
            if (!$model_clients -> get_client_id($_POST)) {
                $model_clients -> save_new_user($_POST);
                $_SESSION['message']['info'] = "Створено нового клієнта у базі.";
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
