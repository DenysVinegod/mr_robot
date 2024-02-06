<?php 
if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}

require_once ($_SERVER['DOCUMENT_ROOT'].'/app/controllers/access.php');
?>

<!DOCTYPE html>
	<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html" 
                charset="UTF-8" />
			<meta name="viewport" 
                content="width=device-width, initial-scale=1" />
			<meta name="keywords" content="R O B O T, ремонт комп'ютерної 
                техніки, Галич" />
			<meta name="description" content="Ремонт комп'ютерної техніки" />
			
			<link rel="shortcut icon" 
                href="/app/assets/images/style/favicon-32.png" />
			<link rel="stylesheet" type="text/css" 
                href="/app/assets/css/login_style.css"/>
			<link rel="stylesheet" type="text/css" 
				href="/app/assets/css/login_media-queries.css" />
            
            <script src="/app/assets/js/login_script.js" defer></script>

			<title>Аутентифікація</title>
        </head>
		<body>
            <div id="header">
                <div id="logo">
                    <a href="/">
                        <img src="/app/assets/images/style/logo.png" alt="logo">
                    </a>
                </div>
            </div>
            <div id="body">
                <div id="message_block">
                    <?php if (isset($_SESSION['message'])) {
                        foreach($_SESSION['message'] as $key => $value) {
                            echo ("<div class='message {$key}'>{$value}</div>");
                            unset($_SESSION['message']);
                        }
                    } ?>
                </div>
