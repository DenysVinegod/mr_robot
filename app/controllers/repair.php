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
    private $clientsModel;
    private $contactsModel;
    private $devicesModel;
    private $statusesModel;

    function __construct() {
        global $model_repairs, $model_clients, $model_contacts, $model_devices, $model_statuses;
        $this->model = $model_repairs;
        $this->clientsModel = $model_clients;
        $this->contactsModel = $model_contacts;
        $this->devicesModel = $model_devices;
        $this->statusesModel = $model_statuses;
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
        return $this->statusesModel->list_elements('statuses');
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

    /**
     * Convert an HTML datetime-local value into a database datetime string.
     */
    public function format_register_date(string $registerDate): string {
        if (strpos($registerDate, 'T') === false) {
            return $registerDate;
        }
        $parts = explode('T', $registerDate, 2);
        return $parts[0] . ' ' . $parts[1];
    }

    /**
     * Map repair status text to a CSS class for row highlighting.
     */
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

    public function ensureClient(array $data): int {
        if (!$this->clientsModel->get_client_id($data)) {
            $this->clientsModel->save_new_user($data);
        }
        return $this->clientsModel->get_client_id($data);
    }

    public function ensureContact(array $data): int {
        if (!$this->contactsModel->get_contact_id($data)) {
            $this->contactsModel->save_new_contact($data);
        }
        return $this->contactsModel->get_contact_id($data);
    }

    public function ensureDevice(array $data): int {
        $device_id = $this->devicesModel->get_device_id($data);
        if ($device_id > 0) {
            return $device_id;
        }

        $new_device_id = $this->devicesModel->save_new_device($data);
        if ($new_device_id > 0) {
            return $new_device_id;
        }

        return $this->devicesModel->get_device_id($data);
    }

    /**
     * Append a short informational message to the session.
     */
    private function addInfoMessage(string $text): void {
        if (isset($_SESSION['message']['info'])) {
            $_SESSION['message']['info'] .= '<hr>' . $text;
        } else {
            $_SESSION['message']['info'] = $text;
        }
    }

    private function getDeviceDisplay(array $value): string {
        $deviceDisplay = $value['device_name'];
        $deviceAttrs = [];
        if (!empty($value['device_color'])) {
            $deviceAttrs[] = $value['device_color'];
        }
        if (!empty($value['device_serial_number'])) {
            $deviceAttrs[] = 'SN: ' . $value['device_serial_number'];
        }
        if (!empty($value['device_cosmetic_condition'])) {
            $deviceAttrs[] = $value['device_cosmetic_condition'];
        }
        return empty($deviceAttrs) ? $deviceDisplay : $deviceDisplay . ' [' . implode(', ', $deviceAttrs) . ']';
    }

    private function renderRepairRow(array $value, int $rowCounter): void {
        $rowClass = $rowCounter % 2 === 0 ? 'even' : 'odd';
        $statusClass = $this->get_status_row_class($value['status']);
        $deviceDisplay = $this->getDeviceDisplay($value);
        $jsonData = htmlspecialchars(json_encode($value, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');

        $id = intval($value['id']);
        $safeStatus = htmlspecialchars($value['status'], ENT_QUOTES, 'UTF-8');
        $safeName = htmlspecialchars($value['surname'] . ' ' . $value['first_name'] . ' ' . $value['last_name'], ENT_QUOTES, 'UTF-8');
        $safeContact = htmlspecialchars($value['contact_type'] . ': ' . $value['contact'], ENT_QUOTES, 'UTF-8');
        $safeDescription = htmlspecialchars($value['description'], ENT_QUOTES, 'UTF-8');
        $safePrice = htmlspecialchars($value['price'], ENT_QUOTES, 'UTF-8');
        $safeConclusion = htmlspecialchars($value['master_conclusion'], ENT_QUOTES, 'UTF-8');
        $safeRegisterDate = htmlspecialchars($value['register_date'], ENT_QUOTES, 'UTF-8');
        $safeDoneDate = htmlspecialchars($value['done_date'], ENT_QUOTES, 'UTF-8');

        echo "<tr id='repair_{$id}' class='{$rowClass} list_line {$statusClass}'>";
        echo "<td>{$id}</td>";
        echo "<td>{$safeStatus}</td>";
        echo "<td>{$safeName}</td>";
        echo "<td>{$safeContact}</td>";
        echo "<td>{$deviceDisplay}</td>";
        echo "<td>{$safeDescription}</td>";
        echo "<td>{$safePrice}</td>";
        echo "<td>{$safeConclusion}</td>";
        echo "<td>{$safeRegisterDate}</td>";
        echo "<td>{$safeDoneDate}</td>";
        echo "<td class='js_full_info' style='display: none;'>{$jsonData}</td>";
        echo '</tr>';
    }

    private function get_allowed_status_transitions(string $role): array {
        return [];
    }

    public function is_status_change_allowed(int $desired_status_id, int $original_status_id, string $role): bool {
        return $role === 'superadmin';
    }

    function get_total_pages(int $perPage, int $status_id = 0): int {
        $count = $this->model->count_repairs($status_id);
        return max(1, (int) ceil($count / $perPage));
    }

    function render_html_rows(int $page = 1, int $perPage = 0, int $status_id = 0, string $sort_by = 'register_date', string $sort_dir = 'DESC'): void {
        $offset = 0;
        if ($perPage > 0) {
            $offset = ($page - 1) * $perPage;
        }

        $result = $this->model->list_repairs($status_id, $perPage, $offset, $sort_by, $sort_dir);
        $row_counter = 1;
        foreach ($result as $value) {
            $this->renderRepairRow($value, $row_counter);
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

$controller = new Repair();

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

            $client_id = $controller->ensureClient($_POST);
            if ($client_id <= 0) {
                $_SESSION['message']['error'] = 'Не вдалося зберегти дані клієнта.';
                header("Location: {$_POST['back_path']}");
                exit();
            }
            $_POST['client_id'] = $client_id;

            $contact_id = $controller->ensureContact($_POST);
            if ($contact_id <= 0) {
                $_SESSION['message']['error'] = 'Не вдалося зберегти контакт клієнта.';
                header("Location: {$_POST['back_path']}");
                exit();
            }
            $_POST['contact_id'] = $contact_id;

            $device_id = $controller->ensureDevice($_POST);
            if ($device_id <= 0) {
                $_SESSION['message']['error'] = 'Не вдалося зберегти пристрій. Виберіть існуючий пристрій або введіть дані нового.';
                header("Location: {$_POST['back_path']}");
                exit();
            }
            $_POST['device_id'] = $device_id;

            $_POST['register_date'] = $controller->format_register_date($_POST['register_date']);
            $_POST['id'] = $controller->model->save_new_repair($_POST);
            if ($_POST['id'] <= 0) {
                $_SESSION['message']['error'] = 'Не вдалося зберегти замовлення на ремонт.';
                header("Location: {$_POST['back_path']}");
                exit();
            }
            if (isset($_SESSION['message']['info'])) {
                $_SESSION['message']['info'] 
                    .= "<hr>Створено нову заявку на ремонт #{$_POST['id']}";
            } else {
                $_SESSION['message']['info'] = "Створено нову заявку на ремонт #{$_POST['id']}";
            }
            header("Location: {$_POST['back_path']}");
            break;
        
        case 'edit_repair':
            $_POST['status_id'] = $_POST['status'];
            unset($_POST['status']);
            $repair_id = intval($_POST['id'] ?? 0);
            $current_status_id = $controller->model->get_repair_status_id($repair_id);
            if ($current_status_id === null) {
                $_SESSION['message']['error'] = 'Заявку не знайдено.';
                header("Location: {$_POST['back_path']}");
                exit();
            }

            $desired_status_id = intval($_POST['status_id']);
            if (!$controller->is_status_change_allowed($desired_status_id, intval($current_status_id), $current_role)) {
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
