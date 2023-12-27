<?php 
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/models/models_base.php');

class Contacts extends ModelsBase {
    /**
     * @return int id of user or int(0) if user not found or input data invalid
     */
    function get_contact_id(array $data): int{
        if ((isset($data['contact_type_id'])) 
        && (isset($data['client_id']))
        && (isset($data['contact']))) {
            $query = "SELECT `id` FROM `contacts` 
                WHERE 
                `type_id` = '{$this -> clear_input($data['contact_type_id'])}' 
                AND `client_id` = '{$this -> clear_input($data['client_id'])}' 
                AND `contact`   = '{$this -> clear_input($data['contact'])}' 
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
    function save_new_contact(array $data): bool{
        if ((isset($data['contact_type_id'])) 
            && (isset($data['client_id']))
            && (isset($data['contact']))) {
            $query = "INSERT INTO `contacts`(
                `type_id`, 
                `client_id`, 
                `contact`) VALUES (
                    '{$data['contact_type_id']}',
                    '{$data['client_id']}',
                    '{$data['contact']}'
                );";
            $this -> connect_to_db();
            $result = $this -> connection -> query($query);
            $this -> close();
            return true;
        } else return false;
    }
}
?>
