<?php
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
                href="/app/assets/css/main_style.css?v=0.0.2"/>
            <link rel="stylesheet" type="text/css" 
                href="/app/assets/css/menu.css"/>
			<?php if(isset($styles)) echo $styles; ?>
			<link rel="stylesheet" type="text/css" 
				href="/app/assets/css/media-queries.css" />

			<script src="/app/assets/js/common_script.js" defer></script>
            <script src="/app/assets/js/menu.js" defer></script>
			<?php if(isset($scripts)) echo $scripts; ?>

			<title>mr. ROBOT</title>
        </head>
		<body>
            <!-- Links image -->
			<img src="/app/assets/images/style/logo_brown.png" 
                style="display: none;">
            <div id="page_wrapper">
                <div id="page_header">
                    <div id="block_logo">
                        <a href="/">
                            <img src="/app/assets/images/style/logo.png" 
                                alt="logo">
                        </a>
                    </div>
                    <div class="filler"></div>
                    <?php include $_SERVER['DOCUMENT_ROOT']
                        ."/app/views/layouts/_main_menu.php" 
                    ?>
                </div>
                <div id="page_body">
                    
                    <div id="overlay"></div>
                    
                    <?php if (isset($_SESSION['message'])) {
                        foreach($_SESSION['message'] as $key => $value) {
                            echo ("<div class='message {$key}'>{$value}</div>");
                            unset($_SESSION['message']);
                        }
                    } ?>
                    