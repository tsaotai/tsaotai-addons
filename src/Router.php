<?php
namespace tsaotai\addons;

use think\facade\Route;

class Router
{
    public static function register()
    {
        $addonNames = AddonDiscovery::getAddonNames();

        foreach ($addonNames as $dirName) {
            $pluginDir = AddonDiscovery::getAddonPath($dirName);

            // 必须存在配置文件
            if (!AddonDiscovery::hasConfig($dirName)) continue;

            $config = AddonDiscovery::getConfig($dirName);
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
