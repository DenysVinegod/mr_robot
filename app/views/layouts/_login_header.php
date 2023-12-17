<?php
if (isset($_SESSION['message'])) {
    foreach($_SESSION['message'] as $key => $value) {
        echo ("<div class='message {$key}'>{$value}</div>");
    }
}
?>