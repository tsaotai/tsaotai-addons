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
        Route::any('plugin/:identifier/[:action]', '\\tsaotai\\addons\\PluginGateway@dispatch')
            ->pattern(['identifier' => '[a-zA-Z0-9_]+', 'action' => 'index|update|rule|install|uninstall'])
            ->completeMatch(true);

        foreach (self::getRouteManifest() as $item) {
            $routeFile = $item['routeFile'] ?? '';
            if ($routeFile !== '' && is_file($routeFile)) {
                try {
                    require $routeFile;
                } catch (\Throwable $e) {
                    self::log('error', '加载插件路由失败: ' . $e->getMessage());
                }
            }
        }
    }

    /**
     * 启用插件的 route.php 清单（跳过逐插件 plugin/* 注册与重复读 plugin.php）
     */
    public static function getRouteManifest(): array
    {
        $debug = false;
        try {
            if (class_exists('\think\facade\Config')) {
                $debug = (bool)\think\facade\Config::get('app.app_debug', false);
            }
        } catch (\Throwable $e) {
        }

        $cacheFile = self::manifestPath();
        $ttl = (int)Config::get('route_manifest_ttl', 3600);
        if (!$debug && $ttl > 0 && is_file($cacheFile) && (time() - filemtime($cacheFile)) < $ttl) {
            $cached = include $cacheFile;
            if (is_array($cached)) {
                return $cached;
            }
        }

        $manifest = [];
        foreach (AddonDiscovery::getAddonNames() as $dirName) {
            try {
                if (!AddonDiscovery::hasConfig($dirName)) {
                    continue;
                }
                if (!AddonDiscovery::validateIdentifier($dirName)) {
                    self::log('warning', "插件 {$dirName} 的 identifier 与目录名不一致，跳过路由注册");
                    continue;
                }
                $config = AddonDiscovery::getConfig($dirName);
                if (($config['state'] ?? 'enable') !== 'enable') {
                    continue;
                }
                if (empty($config['identifier'] ?? '')) {
                    continue;
                }
                $routeFile = AddonDiscovery::getAddonPath($dirName) . DIRECTORY_SEPARATOR . 'route.php';
                $manifest[] = [
                    'id'        => $dirName,
                    'routeFile' => is_file($routeFile) ? $routeFile : '',
                ];
            } catch (\Throwable $e) {
                self::log('error', "扫描插件 {$dirName} 路由失败: " . $e->getMessage());
            }
        }

        if (!$debug && $ttl > 0) {
            $dir = dirname($cacheFile);
            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
            @file_put_contents($cacheFile, "<?php\nreturn " . var_export($manifest, true) . ";\n");
        }

        return $manifest;
    }

    public static function clearManifest(): void
    {
        $file = self::manifestPath();
        if (is_file($file)) {
            @unlink($file);
        }
    }

    private static function manifestPath(): string
    {
        $runtime = function_exists('runtime_path')
            ? runtime_path()
            : (sys_get_temp_dir() . DIRECTORY_SEPARATOR);
        return rtrim((string)$runtime, '/\\') . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'addons_route_manifest.php';
    }
}
