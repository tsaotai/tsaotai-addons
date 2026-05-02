<?php
declare (strict_types=1);

namespace tsaotai\addons;

use think\App;

class Addons
{
    protected App $app;
    protected array $addons = [];
    protected Generator $generator;

    public function __construct(App $app, Generator $generator)
    {
        $this->app = $app;
        $this->generator = $generator;
    }

    public function load(): void
    {
        Loader::load();
    }

    public function registerRoutes(): void
    {
        Router::register();
    }

    public function getAddons(): array
    {
        if (empty($this->addons)) {
            $this->addons = $this->scanAddons();
        }
        return $this->addons;
    }

    public function scanAddons(): array
    {
        $addonsPath = addons_path();
        $addons = [];

        if (is_dir($addonsPath)) {
            $dirs = glob($addonsPath . '*', GLOB_ONLYDIR);
            if ($dirs !== false) {
                foreach ($dirs as $dir) {
                    $name = basename($dir);
                    $addons[$name] = $this->getAddonInfo($name);
                }
            }
        }

        return $addons;
    }

    public function create(string $name, array $options = []): bool
    {
        return $this->generator->create($name, $options);
    }

    protected function getAddonInfo(string $name): array
    {
        $addonPath = addons_path($name);
        $configFile = $addonPath . '/plugin.php';

        $info = [
            'name' => $name,
            'title' => $name,
            'description' => '',
            'version' => '1.0.0',
            'author' => '',
            'state' => 'enable',
            'installed' => file_exists($addonPath . '/install.lock'),
        ];

        if (file_exists($configFile)) {
            $config = include $configFile;
            $info = array_merge($info, $config);
        }

        return $info;
    }
}
