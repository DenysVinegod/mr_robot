<?php 
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/models/models_base.php');

class Authentication extends ModelsBase {
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
}
?>
