<?php 

$available_menu_items = [
    'CRUD users' => [
        'id'        => 'crud_users',
        'label'     => 'CRUD users',
        'img_path'  => '/app/assets/images/style/user_white.png',
        'type'      => 'tag_a',
        'action'    => '/app/views/crud_users.php',
        'category'  => 'apps'
    ],
    'List repairs' => [
        'id'        => 'item_repairs',
        'label'     => 'Ремонти',
        'img_path'  => '/app/assets/images/style/repair.png',
        'type'      => 'tag_a',
        'action'    => '/app/views/repairs.php',
        'category'  => 'apps'
    ],
    'logout' => [
        'id'        => 'item_logout',
        'label'     => 'Вихід',
        'img_path'  => '/app/assets/images/style/log_out.png',
        'type'      => 'tag_a',
        'action'    => '?account_action=logout',
        'category'  => 'account'
    ]
];

$user_modules = $_SESSION['account']['modules'] ?? [];

$menu_items = [];
foreach ($user_modules as $module) {
    if (isset($available_menu_items[$module])) {
        $menu_items[] = $available_menu_items[$module];
    }
}

$menu_items[] = $available_menu_items['logout'];

?>