<?php
namespace tsaotai\addons;

use think\facade\Route;

class Router
{
    public static function register()
    {
        // 插件目录
        $addonDir = addons_path();
        if (!is_dir($addonDir)) {
            return;
        }

        // 遍历所有插件，自动注册 Plugin 路由 + 加载自定义路由
        foreach (scandir($addonDir) as $dirName) {
            if (in_array($dirName, ['.', '..'])) continue;
            if (!is_dir(addons_path($dirName))) continue;

            $pluginDir  = addons_path($dirName);
            $configFile = $pluginDir . DIRECTORY_SEPARATOR . 'plugin.php';

            // 必须存在配置文件
            if (!is_file($configFile)) continue;

            $config     = include $configFile;
            $identifier = $config['identifier'] ?? '';
            if (empty($identifier)) continue;

            // 自动注册每个插件的 Plugin 控制器路由
            $controller = "\\addons\\{$identifier}\\controller\\Plugin";
            Route::any("plugin/{$identifier}/update",    "{$controller}@update");
            Route::any("plugin/{$identifier}/rule",      "{$controller}@rule");
            Route::any("plugin/{$identifier}/install",   "{$controller}@install");
            Route::any("plugin/{$identifier}/uninstall", "{$controller}@uninstall");
            Route::any("plugin/{$identifier}",           "{$controller}@index");

            // 加载插件自定义路由（如有）
            $routeFile = $pluginDir . DIRECTORY_SEPARATOR . 'route.php';
            if (is_file($routeFile)) {
                require $routeFile;
            }
        }
    }
}
