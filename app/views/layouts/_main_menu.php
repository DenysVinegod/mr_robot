<?php require_once $_SERVER['DOCUMENT_ROOT'].'/app/helpers/_main_menu_items.php'; ?>

<nav class="main_menu">
    <div id="apps_menu" class="main_menu_item">
        <img src="/app/assets/images/style/apps.png" alt="apps button">
    </div>
    <div id="account_menu" class="main_menu_item">
        <img src="/app/assets/images/style/user_white.png" alt="account button">
    </div>
</nav>

<div id="main_menu_window">
    <div id="hiden_info" style="display: none;">
        <?php echo (json_encode($menu_items)); ?>
    </div>
    <div id="items_container">Items container</div>
</div>