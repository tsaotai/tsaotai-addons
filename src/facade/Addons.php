<?php
declare (strict_types=1);

namespace tsaotai\addons\facade;

use think\Facade;

/**
 * @method static void load()
 * @method static void registerRoutes()
 * @method static array getAddons()
 * @method static array scanAddons()
 * @method static void clearCache()
 * @method static void clearPluginCache(string $name)
 */
class Addons extends Facade
{
    protected static function getFacadeClass()
    {
        return 'addons';
    }
}
