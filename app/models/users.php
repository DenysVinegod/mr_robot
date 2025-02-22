<?php 
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/models/models_base.php');

class Users extends ModelsBase {
    function find_user_by_login(string $login): array{
        $login = $this ->clear_input($login);
        $query = "SELECT `id`,`password` FROM `users` WHERE `login` = '{$login}';";
        $this -> connect_to_db();
        $result = $this -> connection -> query($query);
        $this -> close();
        if ($row = $result -> fetch_assoc()) {
            $row['search_status'] = 'success';
            return $row;
        } else {
            $row['search_status'] = 'empty';
            return $row;
        }
    }

    function getModules(int $uid): array{
        $query = "SELECT `modules`.`name` AS `module_name` 
            FROM `users_modules` 
            INNER JOIN `modules` ON `users_modules`.`module_id` = `modules`.`id` 
            WHERE `users_modules`.`user_id` = {$uid};";
        $this -> connect_to_db();
        $result = $this -> connection -> query($query);
        $this -> close();
        $counter = 0;
        $result_array = array();
        while ($row = $result -> fetch_assoc()) {
            $result_array[$counter++] = $row['module_name'];
        }
        return $result_array;
    }
}
?>
