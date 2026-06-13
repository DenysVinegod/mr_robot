<?php 
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/models/models_base.php');

class Devices extends ModelsBase {
    /**
     * @return int id of user or int(0) if user not found or input data invalid
     */
    function get_device_id(array $data): int{
        if (isset($data['device_id'])) {
            if (intval($data['device_id']) > 0) {
                return intval($data['device_id']);
            }
            if ($data['device_id'] === 'new') {
                return 0;
            }
        }

        if ((isset($data['device_type_id'])) 
        && (isset($data['client_id']))) {
            $serial_number = isset($data['device_serial_number']) ? trim($data['device_serial_number']) : '';
            if ($serial_number !== '') {
                $query = "SELECT `id` FROM `devices` 
                    WHERE 
                    `type_id` = '{$this -> clear_input($data['device_type_id'])}' 
                    AND `client_id` = '{$this -> clear_input($data['client_id'])}' 
                    AND `serial_number` = '{$this -> clear_input($serial_number)}'
                    LIMIT 1;";
                $this -> connect_to_db();
                $result = $this -> connection -> query($query);
                $this -> close();
                if ($result -> num_rows) {
                    $row = $result -> fetch_assoc();
                    return intval($row['id']);
                }
            }
            return 0;
        } else return 0;
    }

    /**
     * @return int inserted device id or 0 if invalid
     */
    function save_new_device(array $data): int{
        if ((isset($data['device_type_id'])) 
            && (isset($data['client_id']))) {
            $description = isset($data['device_description']) ? $this->clear_input($data['device_description']) : '';
            $color = isset($data['device_color']) ? $this->clear_input($data['device_color']) : '';
            $cosmetic_condition = isset($data['device_cosmetic_condition']) ? $this->clear_input($data['device_cosmetic_condition']) : '';
            $serial_number = isset($data['device_serial_number']) ? $this->clear_input($data['device_serial_number']) : '';
            $equipment = isset($data['device_equipment']) ? $this->clear_input($data['device_equipment']) : '';
            $query = "INSERT INTO `devices`(
                `type_id`, 
                `client_id`,
                `description`,
                `color`,
                `cosmetic_condition`,
                `serial_number`,
                `equipment`) VALUES (
                    '{$data['device_type_id']}',
                    '{$data['client_id']}',
                    '{$description}',
                    '{$color}',
                    '{$cosmetic_condition}',
                    '{$serial_number}',
                    '{$equipment}'
                );";
            $this -> connect_to_db();
            $result = $this -> connection -> query($query);
            $insert_id = intval($this->connection->insert_id);
            $this -> close();
            return $result ? $insert_id : 0;
        } else return 0;
    }
}
?>
