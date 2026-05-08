<?php
declare (strict_types=1);

namespace tsaotai\addons;

use think\facade\Config;
use think\facade\Route;
use think\facade\Event;
use think\facade\Middleware;
use think\facade\Request;

class Loader
{
    public static function load(): void
    {
        $addonNames = AddonDiscovery::getAddonNames();

        foreach ($addonNames as $plugin) {
            // 获取插件配置和状态
            if (AddonDiscovery::hasConfig($plugin)) {
                // 校验 identifier 是否与目录名一致
                if (!AddonDiscovery::validateIdentifier($plugin)) {
                    continue;
                }
                $config = AddonDiscovery::getConfig($plugin);
                if (($config['state'] ?? 'enable') !== 'enable') {
                    continue;
                }
            }

            $pluginDir = AddonDiscovery::getAddonPath($plugin) . DIRECTORY_SEPARATOR;

            // ====================== 配置加载：直接读取插件根目录 config.php ======================
            $configFile = $pluginDir . 'config.php';
            if (is_file($configFile)) {
                Config::load($configFile, $plugin); // 加载配置，标识为插件名
            }

            // 加载插件工具文件
            is_file($pluginDir . 'common.php')   && require_once $pluginDir . 'common.php';
            is_file($pluginDir . 'service.php')  && include $pluginDir . 'service.php';
            is_file($pluginDir . 'provider.php') && app()->bind(include $pluginDir . 'provider.php');
            is_file($pluginDir . 'event.php')    && Event::load(include $pluginDir . 'event.php');
            is_file($pluginDir . 'middleware.php')&& Middleware::import(include $pluginDir . 'middleware.php');

            // ====================== TP8 原生标准路由（无任何骚操作，官方写法） ======================
            if (is_file($pluginDir . 'route.php')) {
                // 路由分组：前缀 addons/插件名 + 绑定控制器命名空间
                Route::group("addons/{$plugin}", function () use ($pluginDir) {
                    require $pluginDir . 'route.php';
                })->namespace("addons\\{$plugin}\\controller");
            }
        }
    }
}
