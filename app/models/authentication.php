<?php 
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/models/models_base.php');

class Authentication extends ModelsBase {
    function find_user_by_login(string $login): array{
        $login = $this->clear_input($login);
        $query = "SELECT u.`id`, u.`password`, u.`role_id`, r.`name` AS `role_name` 
            FROM `users` u
            LEFT JOIN `roles` r ON u.`role_id` = r.`id`
            WHERE u.`login` = '{$login}' LIMIT 1;";
        $this->connect_to_db();
        $result = $this->connection->query($query);
        $this->close();
        if ($row = $result->fetch_assoc()) {
            $row['search_status'] = 'success';
            return $row;
        } else {
            return ['search_status' => 'empty'];
        }
    }
}
?>
