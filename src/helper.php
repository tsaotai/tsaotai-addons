<?php
declare (strict_types=1);

if (!function_exists('addons')) {
    function addons(string $name = null): mixed
    {
        if ($name === null) {
            return app('addons');
        }
        return app('addons')->getAddons()[$name] ?? null;
    }
}

if (!function_exists('addons_path')) {
    function addons_path(string $path = ''): string
    {
        $addonsPath = \tsaotai\addons\Config::get('path');
        if (empty($addonsPath)) {
            $addonsPath = root_path('addons');
        }
        return $addonsPath . ($path ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : '');
    }
}

if (!function_exists('addons_url')) {
    function addons_url(string $url = ''): string
    {
        return url('addons/' . ltrim($url, '/'));
    }
}

if (!function_exists('addons_view')) {
    function addons_view(string $template = '', array $vars = []): string
    {
        return view($template, $vars);
    }
}
