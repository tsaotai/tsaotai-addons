<?php
declare (strict_types=1);

namespace tsaotai\addons;

use think\Service as ThinkService;

class Service extends ThinkService
{
    public function register()
    {
        // 加载配置
        $this->mergeConfigFrom(__DIR__ . '/config.php', 'addons');

        // 注册服务
        $this->app->bind('addons', function () {
            return new Addons($this->app);
        });
    }

    public function boot()
    {
        // 启动服务
        if (Config::get('auto_register', true)) {
            $this->loadRoutes();
        }

        if (Config::get('auto_load', true)) {
            $this->loadAddons();
        }
    }

    protected function loadRoutes()
    {
        // 自动注册插件路由
        Router::register();
    }

    protected function loadAddons()
    {
        // 自动加载插件
        Loader::load();
    }
}
