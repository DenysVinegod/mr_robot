<?php 
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/models/models_base.php');

class Repairs extends ModelsBase {
    /**
     * @return bool false if input array invalid
     */
    function save_new_repair(array $data): bool{
        if ((isset($data['client_id'])) 
        && (isset($data['status_id'])) 
        && (isset($data['device_id'])) 
        && (isset($data['description'])) 
        && (isset($data['price'])) 
        && (isset($data['manager_id'])) 
        && (isset($data['register_date']))) {
            $query = "INSERT INTO `repairs`(
                `status_id`, 
                `client_id`, 
                `device_id`, 
                `description`, 
                `price`, 
                `manager_id`, 
                `register_date`) VALUES (
                    '{$data['status_id']}', 
                    '{$data['client_id']}', 
                    '{$data['device_id']}', 
                    '{$data['description']}', 
                    '{$data['price']}', 
                    '{$data['manager_id']}', 
                    '{$data['register_date']}'
                );";
            $this -> connect_to_db();
            $result = $this -> connection -> query($query);
            $this -> close();
            return true;
        } else return false;
    }

    function list_repairs(string $status='all'): array{
        $array = array();
        $query = "SELECT 
            `repairs`.`id`, 
            `repairs`.`client_id`, 
            `statuses`.`name` AS `status`, 
            `clients`.`surname`, 
            `clients`.`first_name`, 
            `clients`.`last_name`, 
            `contact_types`.`name` AS `contact_type`, 
            `contacts`.`contact`, 
            `device_types`.`name` AS `device_name`, 
            `repairs`.`description`, 
            `repairs`.`price`, 
            `repairs`.`master_conclusion`, 
            `repairs`.`register_date`, 
            `repairs`.`done_date`
            FROM `repairs` 
            INNER JOIN `statuses` 
                ON `repairs`.`status_id` = `statuses`.`id`
            INNER JOIN `clients`
                ON `repairs`.`client_id` = `clients`.`id`
            INNER JOIN `contacts`
                ON `repairs`.`contact_id` = `contacts`.`id`
            INNER JOIN `contact_types`
                ON `contacts`.`type_id` = `contact_types`.`id`
            INNER JOIN `devices`
                ON `repairs`.`device_id` = `devices`.`id`
            INNER JOIN `device_types`
                ON `devices`.`type_id` = `device_types`.`id`;";
        $this -> connect_to_db();
        $result = $this -> connection -> query($query);
        $this -> close();
        $array_counter = 0;
        while ($row = $result -> fetch_assoc()) {
            $array[$array_counter] = $row;
            $array_counter++;
        }
        return $array;
    }
}
?>
