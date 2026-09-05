<?php
declare (strict_types=1);

namespace tsaotai\addons;

class Config
{
    /**
     * @var array<string, mixed>
     */
    protected static array $config = [
        'path' => '',
        'auto_register' => true,
        'auto_load' => true,
    ];

    public static function get(?string $name = null, mixed $default = null): mixed
    {
        if ($name === null) {
            return self::$config;
        }

        return self::$config[$name] ?? $default;
    }

    public static function set(string|array $name, mixed $value = null): void
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
