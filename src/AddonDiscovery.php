<?php
declare (strict_types=1);

namespace tsaotai\addons;

class AddonDiscovery
{
    /**
     * 获取所有插件目录名称
     *
     * @return array
     */
    public static function getAddonNames(): array
    {
        $addonsPath = addons_path();
        $names = [];

        if (!is_dir($addonsPath)) {
            return $names;
        }

        $dirs = glob($addonsPath . '*', GLOB_ONLYDIR);
        if ($dirs !== false) {
            foreach ($dirs as $dir) {
                $names[] = basename($dir);
            }
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
        return self::getAddonPath($name) . '/plugin.php';
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
        $configPath = self::getConfigPath($name);
        if (is_file($configPath)) {
            return include $configPath;
        }
        return [];
    }

    /**
     * 检查插件是否已安装
     *
     * @param string $name
     * @return bool
     */
    public static function isInstalled(string $name): bool
    {
        return is_file(self::getAddonPath($name) . '/install.lock');
    }
}
