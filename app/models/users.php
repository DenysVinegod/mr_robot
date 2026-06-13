<?php 
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/models/models_base.php');

class Users extends ModelsBase {
    /**
     * Збережує нового користувача
     * @return bool
     */
    function save_new_user(array $data): bool{
        if (!isset($data['login']) || !isset($data['password']) || !isset($data['role_id'])) {
            return false;
        }
        $login = $this->clear_input($data['login']);
        $password = password_hash($data['password'], PASSWORD_BCRYPT);
        $role_id = intval($data['role_id']);
        
        $query = "INSERT INTO `users`(`login`, `password`, `role_id`) VALUES ('{$login}', '{$password}', {$role_id});";
        $this->connect_to_db();
        $result = $this->connection->query($query);
        $this->close();
        return $result;
    }

    /**
     * Оновлює користувача
     * @return bool
     */
    function update_user(array $data): bool{
        if (!isset($data['id']) || !isset($data['login']) || !isset($data['role_id'])) {
            return false;
        }
        $id = intval($data['id']);
        $login = $this->clear_input($data['login']);
        $role_id = intval($data['role_id']);
        
        $query = "UPDATE `users` SET `login` = '{$login}', `role_id` = {$role_id} WHERE `id` = {$id};";
        
        // Якщо пароль змінений
        if (!empty($data['password'])) {
            $password = password_hash($data['password'], PASSWORD_BCRYPT);
            $query = "UPDATE `users` SET `login` = '{$login}', `password` = '{$password}', `role_id` = {$role_id} WHERE `id` = {$id};";
        }
        
        $this->connect_to_db();
        $result = $this->connection->query($query);
        $this->close();
        return $result;
    }

    /**
     * Видаляє користувача за ID
     * @return bool
     */
    function delete_user(int $id): bool{
        $id = intval($id);
        $query = "DELETE FROM `users` WHERE `id` = {$id} LIMIT 1;";
        $this->connect_to_db();
        $result = $this->connection->query($query);
        $this->close();
        return $result;
    }

    /**
     * Отримує користувача за ID
     * @return array|null
     */
    function get_user_by_id(int $id): ?array {
        $id = intval($id);
        $query = "SELECT u.*, r.`name` AS `role_name` FROM `users` u 
                  LEFT JOIN `roles` r ON u.`role_id` = r.`id`
                  WHERE u.`id` = {$id} LIMIT 1;";
        $this->connect_to_db();
        $result = $this->connection->query($query);
        $this->close();
        if ($row = $result->fetch_assoc()) {
            return $row;
        }
        return null;
    }

    /**
     * Отримує список всіх користувачів
     * @return array
     */
    function list_all_users(): array {
        $query = "SELECT u.*, r.`name` AS `role_name` FROM `users` u 
                  LEFT JOIN `roles` r ON u.`role_id` = r.`id` 
                  ORDER BY u.`id` DESC;";
        $this->connect_to_db();
        $result = $this->connection->query($query);
        $this->close();
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        return $users;
    }
}
?>
