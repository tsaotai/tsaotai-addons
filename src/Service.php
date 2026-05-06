<?php
declare (strict_types=1);

namespace tsaotai\addons;

use think\Service as ThinkService;
use tsaotai\addons\command\MakeAddon;

class Service extends ThinkService
{
    public function register()
    {
        // 加载配置
        $this->loadConfig();

        // 注册 Generator 服务
        $this->app->bind(Generator::class, function () {
            return new Generator($this->app);
        });

        // 注册 Addons 服务
        $this->app->bind('addons', function () {
            return new Addons($this->app->make(Generator::class));
        });
    }

    public function boot()
    {
        // 注册命令
        $this->commands([
            MakeAddon::class,
        ]);

        // 启动服务
        if (Config::get('auto_register', true)) {
            $this->loadRoutes();
        }

        if (Config::get('auto_load', true)) {
            $this->loadAddons();
        }
    }

    protected function loadConfig(): void
    {
        // 从 ThinkPHP 配置中读取 addons 配置
        $config = $this->app->config->get('addons', []);
        
        // 如果没有配置，使用默认配置文件
        if (empty($config)) {
            $configFile = __DIR__ . '/../config.php';
            if (file_exists($configFile)) {
                $config = include $configFile;
            }
        }
        
        // 加载到自定义 Config 类
        Config::load($config);
        
        // 设置默认插件路径
        if (empty(Config::get('path'))) {
            Config::set('path', $this->app->rootPath('addons'));
        }
    }

    protected function loadRoutes(): void
    {
        // 自动注册插件路由
        Router::register();
    }

    protected function loadAddons(): void
    {
        // 自动加载插件
        Loader::load();
    }
}
