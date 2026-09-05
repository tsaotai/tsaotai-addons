<?php
declare (strict_types=1);

namespace tsaotai\addons;

class AddonDiscovery
{
    /** @var list<string>|null */
    private static ?array $names = null;

    /** @var array<string, array> */
    private static array $configs = [];

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
     * 有 plugin.php 的插件目录名。同进程只扫一遍。
     *
     * @return list<string>
     */
    public static function getAddonNames(): array
    {
        if (self::$names !== null) {
            return self::$names;
        }

        $names = [];
        $addonsPath = addons_path();
        if (is_dir($addonsPath)) {
            $dirs = glob(rtrim($addonsPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);
            if ($dirs !== false) {
                foreach ($dirs as $dir) {
                    if (is_file($dir . DIRECTORY_SEPARATOR . 'plugin.php')) {
                        $names[] = basename($dir);
                    }
                }
            }
        }

        self::$names = $names;
        return $names;
    }

    public static function getAddonPath(string $name): string
    {
        return addons_path($name);
    }

    public static function exists(string $name): bool
    {
        return is_dir(self::getAddonPath($name));
    }

    public static function getConfigPath(string $name): string
    {
        return self::getAddonPath($name) . DIRECTORY_SEPARATOR . 'plugin.php';
    }

    public static function hasConfig(string $name): bool
    {
        return is_file(self::getConfigPath($name));
    }

    public static function getConfig(string $name): array
    {
        if (array_key_exists($name, self::$configs)) {
            return self::$configs[$name];
        }

        $config = [];
        $configPath = self::getConfigPath($name);
        if (is_file($configPath)) {
            $loaded = include $configPath;
            $config = is_array($loaded) ? $loaded : [];
        }

        self::$configs[$name] = $config;
        return $config;
    }

    /**
     * identifier 等于目录名且 state 为 enable。
     *
     * @return list<string>
     */
    public static function enabledNames(): array
    {
        $out = [];
        foreach (self::getAddonNames() as $name) {
            $config = self::getConfig($name);
            if ($config === []) {
                continue;
            }
            if (($config['identifier'] ?? '') !== $name) {
                self::log('warning', "插件 {$name} 的 identifier 与目录名不一致，跳过");
                continue;
            }
            if (($config['state'] ?? 'enable') !== 'enable') {
                continue;
            }
            $out[] = $name;
        }
        return $out;
    }

    public static function validateIdentifier(string $name): bool
    {
        $identifier = self::getConfig($name)['identifier'] ?? '';
        return $identifier !== '' && $identifier === $name;
    }

    public static function resetMemory(): void
    {
        self::$names = null;
        self::$configs = [];
    }

    public static function clearCache(): void
    {
        self::resetMemory();
        self::dropLegacyManifest();
    }

    public static function clearPluginCache(string $name): void
    {
        unset(self::$configs[$name]);
        self::$names = null;
        self::dropLegacyManifest();
    }

    /** 丢掉 2026.1.8 留下的路由清单文件，避免旧缓存继续生效 */
    private static function dropLegacyManifest(): void
    {
        $runtime = function_exists('runtime_path')
            ? runtime_path()
            : (sys_get_temp_dir() . DIRECTORY_SEPARATOR);
        $file = rtrim((string)$runtime, '/\\') . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'addons_route_manifest.php';
        if (is_file($file)) {
            @unlink($file);
        }
    }
}
