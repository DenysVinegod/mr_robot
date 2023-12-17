<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/models/authentication.php');
include ($_SERVER['DOCUMENT_ROOT'].'/configs/db.php');

$obj_instance 
    = isset($params_database_main) 
    ? new Authentication($params_database_main) 
    : new Authentication();

$obj_instance -> show_params_html();



?>