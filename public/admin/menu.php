<?php
    $menu_options = [
        ['name' => 'Dashboard', 'icon' => 'fa-home', 'path' => '/admin/dashboard.php'],
        ['name' => 'Configuration', 'icon' => 'fa-cogs', 'path' => '/admin/configuration.php'],
        ['name' => 'Admin Password', 'icon' => 'fa-user', 'path' => '/admin/admin.php'],
        ['name' => 'Reports', 'icon' => 'fa-flag', 'path' => '/admin/reports.php'],
        ['name' => 'Pastes', 'icon' => 'fa-clipboard', 'path' => '/admin/pastes.php'],
        ['name' => 'Users', 'icon' => 'fa-users', 'path' => '/admin/users.php']
    ];
    $current_path = $_SERVER['PHP_SELF'];
?>
<div class="row">
    <div class="col-md-12">
        <ul class="panel quick-menu clearfix">
            <?php foreach ($menu_options as $option): ?>
                <li class="col-xs-3 col-sm-2 col-md-1 <?= ($option['path'] === $current_path) ? 'menu-active' : '' ?>">
                    <a href="<?= $option['path']; ?>">
                        <i class="fa <?= $option['icon']; ?>"></i>
                        <?= $option['name']; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
