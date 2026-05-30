<?php
declare (strict_types=1);

namespace tsaotai\addons;

use think\facade\Cache;

class AddonDiscovery
{
    private const CACHE_KEY_ADDONS = 'tsaotai_addons_list';
    private const CACHE_KEY_CONFIG_PREFIX = 'tsaotai_addons_config_';
    private const CACHE_TTL = 3600;

    /**
     * 获取所有插件目录名称
     *
     * @param bool $useCache 是否使用缓存
     * @return array
     */
    public static function getAddonNames(bool $useCache = true): array
    {
        if ($useCache) {
            $cached = Cache::get(self::CACHE_KEY_ADDONS);
            if ($cached !== null) {
                return $cached;
            }
        }

        $addonsPath = addons_path();
        $names = [];

        if (!is_dir($addonsPath)) {
            return $names;
        }

        $dirs = glob(rtrim($addonsPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);
        if ($dirs !== false) {
            foreach ($dirs as $dir) {
                $names[] = basename($dir);
            }
        }

        if ($useCache) {
            Cache::set(self::CACHE_KEY_ADDONS, $names, self::CACHE_TTL);
        }
        return $names;
    }

    /**
     * 获取插件路径
     *
     * @param string $name
     * @return string
     */
    public static function getAddonPath(string $name): string
    {
        return addons_path($name);
    }

    /**
     * 检查插件是否存在
     *
     * @param string $name
     * @return bool
     */
    public static function exists(string $name): bool
    {
        return is_dir(self::getAddonPath($name));
    }

    /**
     * 获取插件配置文件路径
     *
     * @param string $name
     * @return string
     */
    public static function getConfigPath(string $name): string
    {
        return self::getAddonPath($name) . DIRECTORY_SEPARATOR . 'plugin.php';
    }

    /**
     * 检查插件是否有配置文件
     *
     * @param string $name
     * @return bool
     */
    public static function hasConfig(string $name): bool
    {
        return is_file(self::getConfigPath($name));
    }

    /**
     * 获取插件配置
     *
     * @param string $name
     * @return array
     */
    public static function getConfig(string $name): array
    {
        $cacheKey = self::CACHE_KEY_CONFIG_PREFIX . $name;
        $cached = Cache::get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $configPath = self::getConfigPath($name);
        $config = [];
        if (is_file($configPath)) {
            $config = include $configPath;
            $config = is_array($config) ? $config : [];
        }

        Cache::set($cacheKey, $config, self::CACHE_TTL);
        return $config;
    }

    /**
     * 检查插件是否已安装
     *
     * @param string $name
     * @return bool
     */
    public static function isInstalled(string $name): bool
    {
        return is_file(self::getAddonPath($name) . DIRECTORY_SEPARATOR . 'install.lock');
    }

    /**
     * 校验插件 identifier 是否与目录名一致
     *
     * @param string $name 插件目录名
     * @return bool
     */
    public static function validateIdentifier(string $name): bool
    {
        $config = self::getConfig($name);
        $identifier = $config['identifier'] ?? '';

        if (empty($identifier)) {
            return false;
        }

        return $identifier === $name;
    }

    /**
     * 清除所有插件缓存
     *
     * @return void
     */
    public static function clearCache(): void
    {
        Cache::delete(self::CACHE_KEY_ADDONS);

        $addons = self::getAddonNames(false); // 不使用缓存重新读取
        foreach ($addons as $name) {
            Cache::delete(self::CACHE_KEY_CONFIG_PREFIX . $name);
        }
    }

    /**
     * 清除单个插件的缓存
     *
     * @param string $name
     * @return void
     */
    public static function clearPluginCache(string $name): void
    {
        Cache::delete(self::CACHE_KEY_CONFIG_PREFIX . $name);
        Cache::delete(self::CACHE_KEY_ADDONS); // 也清除列表缓存，因为可能影响
    }
}
