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

        $dirs = glob(rtrim($addonsPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);
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
        $configPath = self::getConfigPath($name);
        if (is_file($configPath)) {
            $config = include $configPath;
            return is_array($config) ? $config : [];
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
}
