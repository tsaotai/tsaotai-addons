<?php
declare (strict_types=1);

namespace tsaotai\addons;

class Addons
{
    /** @var array<string, array>|null */
    protected ?array $addons = null;

    public function load(): void
    {
        Loader::boot();
    }

    public function registerRoutes(): void
    {
        Loader::boot();
    }

    public function getAddons(): array
    {
        if ($this->addons === null) {
            $this->addons = $this->scanAddons();
        }
        return $this->addons;
    }

    public function scanAddons(): array
    {
        $addons = [];
        foreach (AddonDiscovery::getAddonNames() as $name) {
            $config = AddonDiscovery::getConfig($name);
            if ($config === []) {
                continue;
            }
            $addons[$name] = $config;
        }
        return $addons;
    }

    public function clearCache(): void
    {
        AddonDiscovery::clearCache();
        $this->addons = null;
    }

    public function clearPluginCache(string $name): void
    {
        AddonDiscovery::clearPluginCache($name);
        $this->addons = null;
    }
}
