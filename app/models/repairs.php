<?php 
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/models/models_base.php');

class Repairs extends ModelsBase {
    /**
     * @return bool false if input array invalid
     */
    function save_new_repair(array $data): int{
        if ((isset($data['status_id']))
        && (isset($data['client_id'])) 
        && (isset($data['contact_id'])) 
        && (isset($data['device_id'])) 
        && (isset($data['description'])) 
        && (isset($data['price'])) 
        && (isset($data['manager_id'])) 
        && (isset($data['register_date']))) {
            $query1 = "INSERT INTO `repairs`(
                `status_id`, 
                `client_id`, 
                `contact_id`, 
                `device_id`, 
                `description`, 
                `price`, 
                `manager_id`, 
                `register_date`) VALUES (
                    '{$data['status_id']}', 
                    '{$data['client_id']}', 
                    '{$data['contact_id']}', 
                    '{$data['device_id']}', 
                    '{$data['description']}', 
                    '{$data['price']}', 
                    '{$data['manager_id']}', 
                    '{$data['register_date']}'
                );";
            $query2 = "SELECT `id` FROM `repairs` WHERE 
                `status_id`     = '{$data['status_id']}'    AND 
                `client_id`     = '{$data['client_id']}'    AND 
                `contact_id`    = '{$data['contact_id']}'   AND 
                `device_id`     = '{$data['device_id']}'    AND 
                `description`   = '{$data['description']}'  AND 
                `price`         = '{$data['price']}'        AND 
                `manager_id`    = '{$data['manager_id']}'   AND 
                `register_date` = '{$data['register_date']}'
            ;";
            
            $this -> connect_to_db();
            $this -> connection -> query($query1);
            $result = $this -> connection -> query($query2);
            $this -> close();
            
            $array = $result -> fetch_assoc();

            return $array['id'];
        } else return false;
    }

    function update_repair(array $data): bool{
        if ((isset($data['status_id']))
            && (isset($data['client_id'])) 
            && (isset($data['contact_id'])) 
            && (isset($data['device_id'])) 
            && (isset($data['description'])) 
            && (isset($data['price'])) 
            && (isset($data['manager_id'])) 
            && (isset($data['register_date']))) {
            $statuses = $this -> list_elements('statuses');
            $datetime_now = date("Y-m-d H:i:s", time());
            foreach($statuses as $value) {
                if ($value['name'] == 'Видано') $done_id = $value['id'];
            }
            if ($done_id == $data['status_id']) $additional_mysql 
                = "`done_date` = '{$datetime_now}', "; else $additional_mysql 
                = "`done_date` = NULL, ";
            
            $query = "UPDATE `repairs` SET
                `status_id`     = '{$data['status_id']}', 
                `client_id`     = '{$data['client_id']}', 
                `contact_id`    = '{$data['contact_id']}', 
                `device_id`     = '{$data['device_id']}', 
                `description`   = '{$data['description']}', 
                `price`         = '{$data['price']}', 
                `manager_id`    = '{$data['manager_id']}', 
                `master_conclusion` = '{$data['master_conclusion']}', 
                `register_date` = '{$data['register_date']}', 
                {$additional_mysql}
                `updated_at`    = '{$datetime_now}'  
                WHERE `repairs`.`id` = '{$data['id']}';";

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
            `repairs`.`done_date`,
            `contacts`.`type_id` AS `contact_type_id`, 
            `devices`.`type_id` AS `device_type_id`, 
            `repairs`.`status_id`, 
            `repairs`.`contact_id`, 
            `repairs`.`device_id`
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
                ON `devices`.`type_id` = `device_types`.`id`
            ORDER BY `repairs`.`register_date` DESC;";
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
