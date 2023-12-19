<?php 
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/models/models_base.php');

class Repairs extends ModelsBase {
    function list_device_types(): array {
        $query = "SELECT * from `device_types`;";
        $this -> connect_to_db();
        $result = $this -> connection -> query($query);
        $this -> close();
        $vault = array();
        while ($row = $result -> fetch_assoc()) {
            $vault[$row['id']-1] = $row;
        }
        return $vault;
    }
}

if (isset($_POST)) {
    echo var_dump($_POST);
}

?>