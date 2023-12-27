<?php 
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/models/models_base.php');

class Clients extends ModelsBase {
    /**
     * @return bool false if input array invalid
     */
    function save_new_user(array $data): bool{
        if ((isset($data['first_name'])) 
            && (isset($data['surname'])) 
            && (isset($data['last_name']))) {
            $query = "INSERT INTO `clients`(
                `first_name`, 
                `surname`, 
                `last_name`) VALUES (
                    '{$data['first_name']}',
                    '{$data['surname']}',
                    '{$data['last_name']}'
                );";
            $this -> connect_to_db();
            $result = $this -> connection -> query($query);
            $this -> close();
            return true;
        } else return false;
    }

    /**
     * @return int id of user or int(0) if user not found or input data invalid
     */
    function get_client_id(array $data): int{
        if ((isset($data['first_name'])) 
            && (isset($data['surname'])) 
            && (isset($data['last_name']))) {
            $query = "SELECT `id` FROM `clients` 
                WHERE 
                `first_name` = '{$this -> clear_input($data['first_name'])}' 
                AND `surname` = '{$this -> clear_input($data['surname'])}' 
                AND `last_name` = '{$this -> clear_input($data['last_name'])}' 
                LIMIT 1;";
            $this -> connect_to_db();
            $result = $this -> connection -> query($query);
            $this -> close();
            if ($result -> num_rows) {
                $result = $result -> fetch_column();
                return $result;
            } else return 0;
        } else return 0;
    }
}
?>
