<?php
declare (strict_types=1);

namespace tsaotai\addons;

use think\facade\Route;

class Router
{
    private static function useLog(): bool
    {
        return class_exists('\think\facade\Log');
    }

    private static function log(string $level, string $message): void
    {
        if (self::useLog()) {
            try {
                \think\facade\Log::$level($message);
            } catch (\Throwable $e) {
                // 日志记录失败，忽略
            }
        }
    }

    public static function register(): void
    {
        $addonNames = AddonDiscovery::getAddonNames();

        foreach ($addonNames as $dirName) {
            try {
                // 必须存在配置文件且 identifier 与目录名一致
                if (!AddonDiscovery::hasConfig($dirName)) continue;
                if (!AddonDiscovery::validateIdentifier($dirName)) {
                    self::log('warning', "插件 {$dirName} 的 identifier 与目录名不一致，跳过路由注册");
                    continue;
                }

                $config = AddonDiscovery::getConfig($dirName);

                // 检查插件是否启用
                if (($config['state'] ?? 'enable') !== 'enable') continue;

                $identifier = $config['identifier'] ?? '';
                if (empty($identifier)) continue;

                // 自动注册每个插件的 Plugin 控制器路由
                $controller = "\\addons\\{$identifier}\\controller\\Plugin";
                Route::any("plugin/{$identifier}/update",    "{$controller}@update");
                Route::any("plugin/{$identifier}/rule",      "{$controller}@rule");
                Route::any("plugin/{$identifier}/install",   "{$controller}@install");
                Route::any("plugin/{$identifier}/uninstall", "{$controller}@uninstall");
                Route::any("plugin/{$identifier}",           "{$controller}@index");

                // 加载插件自定义路由（无分组包装，保持原有路由格式）
                $pluginDir = AddonDiscovery::getAddonPath($dirName);
                $routeFile = $pluginDir . DIRECTORY_SEPARATOR . 'route.php';
                if (is_file($routeFile)) {
                    require $routeFile;
                }
            } catch (\Exception $e) {
                self::log('error', "注册插件 {$dirName} 路由失败: " . $e->getMessage());
            } catch (\Error $e) {
                self::log('error', "注册插件 {$dirName} 路由出错: " . $e->getMessage());
            }
        }
    }
}