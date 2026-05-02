<?php
declare (strict_types=1);

namespace tsaotai\addons\facade;

use think\Facade;

/**
 * Class Addons
 * @package tsaotai\addons\facade
 * @method static void load()
 * @method static void registerRoutes()
 * @method static array getAddons()
 * @method static array scanAddons()
 * @method static bool create(string $name, array $options = [])
 */
class Addons extends Facade
{
    protected static function getFacadeClass()
    {
        return 'addons';
    }
}
