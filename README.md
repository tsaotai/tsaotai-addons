# TsaoTai Addons

ThinkPHP 8 插件运行时。发现 `addons/`、加载启用插件、`require` 各插件自己的 `route.php`。

不提供脚手架、不提供 `/plugin/:id`、不写 `install.lock`、不缓存目录清单。开通由产品（应用中心 / control）处理。

## 安装

```bash
composer require tsaotai/tsaotai-addons
```

服务提供者会自动 `boot`。

## 启动时做什么

一次遍历启用插件（`identifier` 等于目录名，`state` 为 `enable`）：

1. 加载该目录的 `config.php` / `common.php` / `service.php` / `provider.php` / `event.php` / `middleware.php`（有才加载）
2. `require` `route.php`

同一次请求（或 Worker 进程）内 `plugin.php` 只读一遍。改插件后立刻生效，不必清 `runtime/cache`。

## 控制器

业务控制器继承 `tsaotai\addons\BaseController`。只设置 `view_path` 为 `addons/{插件}/view/`，**不复制**主题 `base.html`。模板写：

```html
{extend name="../view/smartadmin/base.html"}
```

空 `fetch()` 按 `控制器子目录/方法名` 找模板。

## API

| 调用 | 说明 |
|------|------|
| `addons()` | 插件管理器 |
| `addons('demo')` | 某个插件的 `plugin.php` 信息 |
| `addons_path()` / `addons_path('demo')` | 插件根路径 |
| `AddonDiscovery::clearCache()` | 清进程内表 |

## 配置

```php
return [
    'path' => root_path('addons'),
    'auto_register' => true,
    'auto_load' => true,
];
```

## License

Apache-2.0
