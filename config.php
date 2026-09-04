<?php
return [
    // 插件路径
    'path' => root_path('addons'),

    // 是否自动注册路由
    'auto_register' => true,

    // 是否自动加载插件
    'auto_load' => true,

    // 启用插件 route.php 清单缓存秒数；0 表示不写磁盘缓存
    'route_manifest_ttl' => 3600,
];
