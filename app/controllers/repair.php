<?php
if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}

require_once ($_SERVER['DOCUMENT_ROOT'].'/app/models/repairs.php');
include ($_SERVER['DOCUMENT_ROOT'].'/configs/db.php');

$model 
    = isset($params_database_main) 
    ? new Repairs($params_database_main) 
    : new Repairs();

class Repair {
    function __construct() {
        global $model;
        $this -> model = $model;
    }
}

?>