<?php 
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/models/models_base.php');

class Devices extends ModelsBase {
    /**
     * @return int id of user or int(0) if user not found or input data invalid
     */
    function get_device_id(array $data): int{
        if ((isset($data['device_type_id'])) 
        && (isset($data['client_id']))) {
            $query = "SELECT `id` FROM `devices` 
                WHERE 
                `type_id` = '{$this -> clear_input($data['device_type_id'])}' 
                AND `client_id` = '{$this -> clear_input($data['client_id'])}' 
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

    /**
     * @return bool false if input array invalid
     */
    function save_new_device(array $data): bool{
        if ((isset($data['device_type_id'])) 
        && (isset($data['client_id']))) {
            $query = "INSERT INTO `devices`(
                `type_id`, 
                `client_id`) VALUES (
                    '{$data['device_type_id']}',
                    '{$data['client_id']}'
                );";
            $this -> connect_to_db();
            $result = $this -> connection -> query($query);
            $this -> close();
            return true;
        } else return false;
    }
}
?>
