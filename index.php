<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/views/layouts/_main_header.php');
?>
<nav id="categories_menu">
    <div class="card_button_wrapper">
        <a id="button_repairs" 
            class='menu_button' 
            href="/app/views/repairs.php">
            <img src='/app/assets/images/style/repair.png'>
        </a>
    </div>
</nav>

<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/views/layouts/_main_footer.php');
?>
