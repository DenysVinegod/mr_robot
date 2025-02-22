<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/app/models/users.php';

$model_users = new Users();
$users = $model_users -> list_elements('users');

?>