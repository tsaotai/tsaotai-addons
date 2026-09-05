<?php
declare (strict_types=1);

namespace tsaotai\addons;

use think\facade\Config as ThinkConfig;
use think\facade\Event;
use think\facade\Middleware;

class Loader
{
    private static bool $booted = false;

    private static function log(string $level, string $message): void
    {
        if (!class_exists('\think\facade\Log')) {
            return;
        }
        try {
            \think\facade\Log::$level($message);
        } catch (\Throwable $e) {
        }
    }

    /**
     * 一次遍历启用插件：加载约定文件并 require route.php。
     */
    public static function boot(): void
    {
        if (self::$booted) {
            return;
        }

        $load = (bool)Config::get('auto_load', true);
        $routes = (bool)Config::get('auto_register', true);
        if (!$load && !$routes) {
            return;
        }

        self::$booted = true;
        foreach (AddonDiscovery::enabledNames() as $plugin) {
            $pluginDir = AddonDiscovery::getAddonPath($plugin) . DIRECTORY_SEPARATOR;
            if ($load) {
                self::loadPlugin($plugin, $pluginDir);
            }
            if ($routes) {
                self::registerRoute($pluginDir);
            }
        }
    }

    /** @deprecated 用 boot() */
    public static function load(): void
    {
        self::boot();
    }

    /** @deprecated 用 boot() */
    public static function registerRoutes(): void
    {
        self::boot();
    }

    private static function loadPlugin(string $plugin, string $pluginDir): void
    {
        try {
            $configFile = $pluginDir . 'config.php';
            if (is_file($configFile)) {
                ThinkConfig::load($configFile, $plugin);
            }

            is_file($pluginDir . 'common.php') && require_once $pluginDir . 'common.php';
            is_file($pluginDir . 'service.php') && include $pluginDir . 'service.php';
            is_file($pluginDir . 'provider.php') && app()->bind(include $pluginDir . 'provider.php');
            is_file($pluginDir . 'event.php') && Event::load(include $pluginDir . 'event.php');
            is_file($pluginDir . 'middleware.php') && Middleware::import(include $pluginDir . 'middleware.php');
        } catch (\Throwable $e) {
            self::log('error', "加载插件 {$plugin} 失败: " . $e->getMessage());
        }
    }

    private static function registerRoute(string $pluginDir): void
    {
        $routeFile = $pluginDir . 'route.php';
        if (!is_file($routeFile)) {
            return;
        }
        try {
            require $routeFile;
        } catch (\Throwable $e) {
            self::log('error', '加载插件路由失败: ' . $e->getMessage());
        }
    }
}
