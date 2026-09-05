<?php
declare (strict_types=1);

namespace tsaotai\addons;

use think\Service as ThinkService;

class Service extends ThinkService
{
    public function register(): void
    {
        $this->loadConfig();
        $this->app->bind('addons', fn() => new Addons());
    }

    public function boot(): void
    {
        Loader::boot();
    }

    protected function loadConfig(): void
    {
        $config = $this->app->config->get('addons', []);
        if ($config === []) {
            $configFile = __DIR__ . '/../config.php';
            if (is_file($configFile)) {
                $config = include $configFile;
            }
        }
        Config::load(is_array($config) ? $config : []);
        if (empty(Config::get('path'))) {
            Config::set('path', $this->app->rootPath('addons'));
        }
    }
}
