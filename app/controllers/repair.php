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

    public function build_query_url(array $overrides = []): string {
        $base_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        parse_str($_SERVER['QUERY_STRING'] ?? '', $query);
        foreach ($overrides as $key => $value) {
            if ($value === null) {
                unset($query[$key]);
            } else {
                $query[$key] = $value;
            }
        }
        return $base_path . '?' . http_build_query($query);
    }

    private function build_page_url(int $page): string {
        return $this->build_query_url(['page' => $page]);
    }

    function get_statuses(): array {
        global $model_statuses;
        return $model_statuses->list_elements('statuses');
    }

    function get_sortable_columns(): array {
        return [
            'id' => '№',
            'status' => 'Статус',
            'surname' => 'ПІБ',
            'contact_type' => 'Контакти',
            'device_name' => 'Пристрій',
            'description' => 'Причина звернення',
            'price' => 'Вартість',
            'master_conclusion' => 'Коментар майстра',
            'register_date' => 'Дата прийому',
            'done_date' => 'Дата видачі',
        ];
    }

    function sanitize_sort_by(string $sort_by): string {
        $allowed = array_keys($this->get_sortable_columns());
        return in_array($sort_by, $allowed, true) ? $sort_by : 'register_date';
    }

    function sanitize_sort_dir(string $sort_dir): string {
        $sort_dir = strtoupper($sort_dir);
        return in_array($sort_dir, ['ASC', 'DESC'], true) ? $sort_dir : 'DESC';
    }

    private function get_status_row_class(string $status): string {
        switch ($status) {
            case 'Нове замовлення':
                return 'status-new-order';
            case 'Діагностика':
                return 'status-diagnostics';
            case 'Виконано':
                return 'status-done';
            case 'Видано':
                return 'status-delivered';
            case 'Видано без ремонту':
                return 'status-no-repair';
            default:
                return 'status-default';
        }
    }

    private function get_allowed_status_transitions(string $role): array {
        return [];
    }

    private function is_status_change_allowed(int $desired_status_id, int $original_status_id, string $role): bool {
        return $role === 'superadmin';
    }

    function get_total_pages(int $perPage, int $status_id = 0): int {
        $count = $this->model->count_repairs($status_id);
        return max(1, (int) ceil($count / $perPage));
    }

    function render_html_rows(int $page = 1, int $perPage = 0, int $status_id = 0, string $sort_by = 'register_date', string $sort_dir = 'DESC'): void{
        $offset = 0;
        if ($perPage > 0) {
            $offset = ($page - 1) * $perPage;
        }

        $result = $this->model->list_repairs($status_id, $perPage, $offset, $sort_by, $sort_dir);
        $row_counter = 1;
        foreach ($result as $value) {
            $status_class = $this -> get_status_row_class($value['status']);
            if ($row_counter % 2 == 0) {
                echo "<tr id='repair_{$value['id']}' class='even list_line {$status_class}'>";
            } else {
                echo "<tr id='repair_{$value['id']}' class='odd list_line {$status_class}'>";
            }
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
            $device_display = $value['device_name'];
            $device_attrs = [];
            if (!empty($value['device_color'])) {
                $device_attrs[] = $value['device_color'];
            }
            if (!empty($value['device_serial_number'])) {
                $device_attrs[] = 'SN: ' . $value['device_serial_number'];
            }
            if (!empty($value['device_cosmetic_condition'])) {
                $device_attrs[] = $value['device_cosmetic_condition'];
            }
            if (count($device_attrs) > 0) {
                $device_display .= ' [' . implode(', ', $device_attrs) . ']';
            }
            echo "<td>".$device_display."</td>";
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

    function render_pagination(int $page, int $perPage, int $status_id = 0, string $sort_by = 'register_date', string $sort_dir = 'DESC'): void {
        $total_pages = $this->get_total_pages($perPage, $status_id);
        if ($total_pages <= 1) return;

        echo "<div class='pagination_container'><nav class='pagination'>";
        if ($page > 1) {
            echo "<a href='".$this->build_page_url($page - 1)."' class='pagination_button pagination_prev'>&laquo; Попередня</a>";
        }

        for ($page_number = 1; $page_number <= $total_pages; $page_number++) {
            $class = $page_number === $page ? 'pagination_button pagination_active' : 'pagination_button';
            echo "<a href='".$this->build_page_url($page_number)."' class='{$class}'>".$page_number."</a>";
        }

        if ($page < $total_pages) {
            echo "<a href='".$this->build_page_url($page + 1)."' class='pagination_button pagination_next'>Наступна &raquo;</a>";
        }
        echo "</nav></div>";
    }
}

// Ensure workflow statuses exist in the database before any status-dependent logic runs.
$model_statuses->ensure_statuses([
    'Нове замовлення',
    'Діагностика',
    'Очікує узгодження',
    'Узгоджено',
    'Скасовано',
    'Відмовлено',
    'Виконано',
    'Видано',
    'Видано без ремонту'
]);

if (isset($_POST['action'])) {
    // determine current role name (if any)
    $current_role = $_SESSION['account']['role_name'] ?? null;
    switch ($_POST['action']) {
        case 'create_new_repair':
            if ($current_role !== 'superadmin') {
                $_SESSION['message']['error'] = 'Недостатньо прав для створення заявки.';
                header('Location: /');
                exit();
            }
            $_POST['status_id'] = $model_statuses->get_id_by_name('Нове замовлення');
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
            $repair_id = intval($_POST['id'] ?? 0);
            $current_status_id = $this->model->get_repair_status_id($repair_id);
            if ($current_status_id === null) {
                $_SESSION['message']['error'] = 'Заявку не знайдено.';
                header("Location: {$_POST['back_path']}");
                exit();
            }

            $desired_status_id = intval($_POST['status_id']);
            if (!$this->is_status_change_allowed($desired_status_id, intval($current_status_id), $current_role)) {
                $_SESSION['message']['error'] = 'Недостатньо прав для редагування заявки.';
                header("Location: {$_POST['back_path']}");
                exit();
            }

            $_POST['manager_id'] = $_SESSION['account']['id'];

            if (strpos($_POST['register_date'], 'T') !== false) {
                $register_parts = explode('T', $_POST['register_date']);
                if (count($register_parts) === 2) {
                    $_POST['register_date'] = $register_parts[0] . ' ' . $register_parts[1];
                }
            }

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
