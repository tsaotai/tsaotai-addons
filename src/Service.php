<?php
declare (strict_types=1);

namespace tsaotai\addons;

use think\Service as ThinkService;

class Service extends ThinkService
{
    public function register()
    {
        // 注册服务
        $this->app->bind('addons', function () {
            return new Addons($this->app);
        });
    }

    public function boot()
    {
        // 启动服务
        $this->registerRoutes();
        $this->loadAddons();
    }

    protected function registerRoutes()
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
