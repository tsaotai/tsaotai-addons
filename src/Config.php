<?php
declare (strict_types=1);

namespace tsaotai\addons;

class Config
{
    protected static $config = [
        'path' => '',
        'auto_register' => true,
        'auto_load' => true,
    ];

    public static function get($name = null, $default = null)
    {
        if ($name === null) {
            return self::$config;
        }

        return self::$config[$name] ?? $default;
    }

    public static function set($name, $value = null): void
    {
        if (is_array($name)) {
            self::$config = array_merge(self::$config, $name);
        } else {
            self::$config[$name] = $value;
        }
    }

    public static function load(array $config): void
    {
        self::$config = array_merge(self::$config, $config);
    }
}
