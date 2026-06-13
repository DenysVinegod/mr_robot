<?php
$menu_items = [
    [   // repairs
        'id'        => 'item_repairs',
        'label'     => 'Ремонти',
        'img_path'  => '/app/assets/images/style/repair.png',
        'type'      => 'tag_a',
        'action'    => '/app/views/repairs.php',
        'category'  => 'apps'
    ],
];

if (isset($_SESSION['account']['role_name']) && in_array($_SESSION['account']['role_name'], ['superadmin', 'reception'])) {
    $menu_items[] = [
        'id'        => 'item_resources',
        'label'     => 'Ресурси',
        'img_path'  => '/app/assets/images/style/database.png',
        'type'      => 'tag_a',
        'action'    => '/app/views/resources.php',
        'category'  => 'apps'
    ];
}

if (isset($_SESSION['account']['role_name']) && $_SESSION['account']['role_name'] === 'superadmin') {
    $menu_items[] = [
        'id'        => 'item_admin',
        'label'     => 'Адміністрація',
        'img_path'  => '/app/assets/images/style/settings.png',
        'type'      => 'tag_a',
        'action'    => '/app/views/admin.php',
        'category'  => 'apps'
    ];
}

$menu_items[] = [
    'id'        => 'item_logout',
    'label'     => 'Вихід',
    'img_path'  => '/app/assets/images/style/log_out.png',
    'type'      => 'tag_a',
    'action'    => '?account_action=logout',
    'category'  => 'account'
];
?>

<nav class="main_menu">
    <div id="apps_menu" class="main_menu_item">
        <img src="/app/assets/images/style/apps.png" alt="apps button">
    </div>
    <div id="account_menu" class="main_menu_item">
        <img src="/app/assets/images/style/user_white.png" alt="account button">
    </div>
</nav>

<div id="main_menu_window">
    <div id="hiden_info" class="hidden">
        <?php echo (json_encode($menu_items)); ?>
    </div>
    <div id="items_container">Items container</div>
</div>