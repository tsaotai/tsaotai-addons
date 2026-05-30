<?php
declare (strict_types=1);

namespace tsaotai\addons;

use think\facade\Route;
use think\facade\Log;

class Router
{
    public static function register(): void
    {
        $addonNames = AddonDiscovery::getAddonNames();

        foreach ($addonNames as $dirName) {
            try {
                // 必须存在配置文件且 identifier 与目录名一致
                if (!AddonDiscovery::hasConfig($dirName)) continue;
                if (!AddonDiscovery::validateIdentifier($dirName)) {
                    Log::warning("插件 {$dirName} 的 identifier 与目录名不一致，跳过路由注册");
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
            } catch (\Exception $e) {
                Log::error("注册插件 {$dirName} 路由失败: " . $e->getMessage());
            } catch (\Error $e) {
                Log::error("注册插件 {$dirName} 路由出错: " . $e->getMessage());
            }
        }
    }
}
