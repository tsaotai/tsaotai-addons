<?php
declare (strict_types=1);

namespace tsaotai\addons;

class Addons
{
    protected array $addons = [];
    protected Generator $generator;

    public function __construct(Generator $generator)
    {
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
        $addons = [];
        $names = AddonDiscovery::getAddonNames();

        foreach ($names as $name) {
            $addons[$name] = $this->getAddonInfo($name);
        }

        return $addons;
    }

    public function create(string $name, array $options = []): bool
    {
        return $this->generator->create($name, $options);
    }

    protected function getAddonInfo(string $name): array
    {
        $info = [
            'name' => $name,
            'title' => $name,
            'description' => '',
            'version' => '2026.1.1',
            'author' => '',
            'state' => 'enable',
            'installed' => AddonDiscovery::isInstalled($name),
        ];

        if (AddonDiscovery::hasConfig($name)) {
            $config = AddonDiscovery::getConfig($name);
            $info = array_merge($info, $config);
        }

        return $info;
    }
}
