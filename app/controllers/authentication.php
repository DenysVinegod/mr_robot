<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/models/authentication.php');
include ($_SERVER['DOCUMENT_ROOT'].'/configs/db.php');

$model 
    = isset($params_database_main) 
    ? new Authentication($params_database_main) 
    : new Authentication();

$model -> show_params_html();

var_dump($_POST);

$output_password = password_hash($_POST['user_password'], PASSWORD_DEFAULT);

var_dump($output_password);

?>