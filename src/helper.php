<?php
if (!function_exists('addons')) {
    function addons(string $name = null)
    {
        if ($name === null) {
            return app('addons');
        }
        return app('addons')->getAddons()[$name] ?? null;
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
