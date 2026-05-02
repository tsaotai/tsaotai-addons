# TsaoTai Addons

TsaoTai plugin system for ThinkPHP 8.

## 安装

```bash
composer require tsaotai/tsaotai-addons
```

## 快速开始

### 方式 A：自动加载（推荐）

安装后，包会通过 ThinkPHP 服务提供者自动加载插件和路由，无需额外配置！

### 方式 B：手动配置

如果需要手动配置：

#### 1. 配置插件加载器

在 `app/addons.php`：
```php
<?php
use tsaotai\addons\Loader;

Loader::load();
```

#### 2. 配置插件路由

在 `route/addons.php`：
```php
<?php
use tsaotai\addons\Router;

Router::register();
```

---

## 配置

### 配置文件

在 `config/addons.php` 中配置，然后在 `app/provider.php` 或其他地方加载：

```php
<?php
return [
    // 插件路径
    'path' => root_path('addons'),

    // 是否自动注册路由
    'auto_register' => true,

    // 是否自动加载插件
    'auto_load' => true,
];
```

### 加载配置

在 `app/provider.php` 或服务提供者中加载：

```php
<?php
use tsaotai\addons\Config;

$config = require config_path('addons.php');
Config::load($config);
```

### 动态配置

```php
<?php
use tsaotai\addons\Config;

// 获取配置
$path = Config::get('path');

// 设置配置
Config::set('auto_register', false);

// 批量设置
Config::set([
    'auto_register' => true,
    'auto_load' => true,
]);
```

---

## 使用 Facade（更简洁）

```php
<?php
use tsaotai\addons\facade\Addons;

// 获取所有插件信息
$addons = Addons::getAddons();

// 扫描插件目录
$addons = Addons::scanAddons();
```

---

## 使用助手函数

包提供了便捷的助手函数：

### addons()

```php
<?php
// 获取插件管理器
$addons = addons();

// 获取单个插件信息
$demo = addons('demo');
```

### addons_url()

```php
<?php
// 生成插件 URL
$url = addons_url('demo/index');
// 相当于 url('addons/demo/index')
```

### addons_view()

```php
<?php
// 渲染插件视图
return addons_view('demo/index/index', $vars);
```

### addons_path()

```php
<?php
// 获取插件根目录
$path = addons_path();

// 获取指定插件目录
$demoPath = addons_path('demo');

// 获取插件下的文件
$pluginConfig = addons_path('demo/plugin.php');
```

---

## 快速创建插件

使用内置的插件生成器快速创建插件：

```php
<?php
use tsaotai\addons\facade\Addons;

// 快速创建一个插件
Addons::create('demo', [
    'title' => '示例插件',
    'description' => '这是一个示例插件',
    'author' => 'Your Name',
    'version' => '1.0.0',
]);

// 创建包含可选目录的插件
Addons::create('demo', [
    'title' => '示例插件',
    'with_model' => true,      // 生成 model 目录
    'with_validate' => true,   // 生成 validate 目录
    'with_public' => true,     // 生成 public 目录
]);
```

---

## 创建你的第一个插件

### 目录结构
```
addons/
└── demo/                    # 插件目录（唯一标识）
    ├── controller/          # 控制器
    │   ├── Index.php        # 前台控制器
    │   └── Plugin.php       # 插件管理控制器
    ├── view/                # 视图文件
    │   └── index/
    │       └── index.html
    ├── plugin.php           # 插件配置
    └── route.php            # 插件路由（可选）
```

### 插件配置 `plugin.php`
```php
<?php
return [
    'identifier'    => 'demo',
    'title'         => '示例插件',
    'description'   => '这是一个示例插件',
    'version'       => '1.0.0',
    'author'        => '教员',
    'create'        => '2026-05-02',
    'update'        => '2026-05-02',
    'icon'          => 'puzzle',
    'category'      => 'tool',
    'sort'          => 0,
    'state'         => 'enable',
    'config'        => false,
    'install'       => true,
    'clean'         => false,
    'rely'          => '',
    'entry'         => 'addons/demo',
    'scope'         => 'admin',
    'classify'      => 'free',
    'domain'        => '',
    'license'       => 'Apache-2.0',
    'remark'        => ''
];
```

### 前台控制器 `controller/Index.php`
```php
<?php
declare (strict_types=1);

namespace addons\demo\controller;

use tsaotai\addons\BaseController;

class Index extends BaseController
{
    public function index()
    {
        $this->assign('name', 'TsaoTai');
        return $this->fetch();
    }
    
    public function hello($name = 'World')
    {
        return json(['code' => 1, 'msg' => 'Hello ' . $name]);
    }
}
```

### 插件管理控制器 `controller/Plugin.php`
```php
<?php
declare (strict_types=1);

namespace addons\demo\controller;

use tsaotai\addons\PluginController;

class Plugin extends PluginController
{
    // 继承即可，自动拥有安装/卸载/更新日志等功能
    
    // 如果你需要自定义安装逻辑
    public function install(): \think\response\Json
    {
        // 你的自定义安装代码...
        return parent::install();
    }
    
    // 如果你需要自定义卸载逻辑
    public function uninstall(): \think\response\Json
    {
        // 你的自定义卸载代码...
        return parent::uninstall();
    }
}
```

### 视图文件 `view/index/index.html`
```html
<!DOCTYPE html>
<html>
<head>
    <title>示例插件</title>
</head>
<body>
    <h1>Hello, {$name}!</h1>
    <p>欢迎使用 TsaoTai 插件系统！</p>
</body>
</html>
```

---

## 访问插件

- 前台页面：`http://your-domain/addons/demo`
- 插件管理：`http://your-domain/plugin/demo`

---

## API 文档

### 类说明

| 类名 | 说明 |
| --- | --- |
| `tsaotai\addons\BaseController` | 插件基础控制器 |
| `tsaotai\addons\CommonController` | 插件基础控制器（带登录验证） |
| `tsaotai\addons\PluginController` | 插件管理控制器 |
| `tsaotai\addons\Loader` | 插件加载器 |
| `tsaotai\addons\Router` | 插件路由注册器 |
| `tsaotai\addons\Addons` | 插件管理器 |
| `tsaotai\addons\Generator` | 插件生成器 |
| `tsaotai\addons\Config` | 配置管理类 |
| `tsaotai\addons\Service` | ThinkPHP 服务提供者 |

### Facade API

| 方法 | 说明 |
| --- | --- |
| `Addons::load()` | 加载插件 |
| `Addons::registerRoutes()` | 注册插件路由 |
| `Addons::getAddons()` | 获取所有插件信息 |
| `Addons::scanAddons()` | 扫描插件目录 |
| `Addons::create($name, $options)` | 创建新插件 |

### Config API

| 方法 | 说明 |
| --- | --- |
| `Config::get($name, $default)` | 获取配置 |
| `Config::set($name, $value)` | 设置配置 |
| `Config::load($config)` | 加载配置 |

---

## 插件开发规范

### 命名规范
- 插件目录名：纯小写英文，无下划线
- 控制器类名：大驼峰，继承对应基类
- 视图文件：小写下划线分隔

### 目录说明
- `controller/` - 控制器
- `view/` - 视图
- `data/` - 数据文件（可选）
- `plugin.php` - 插件配置（必填）
- `route.php` - 插件路由（可选）
- `common.php` - 公共函数（可选）

---

## 升级指南

### 从 1.1.x 升级到 1.2.x

1. 无需修改现有插件代码
2. 可以使用新的配置功能
3. 可以使用新的助手函数

### 从 1.0.x 升级到 1.1.x

1. 无需修改现有插件代码
2. 建议删除 `app/addons.php` 和 `route/addons.php` 中的手动配置，使用自动加载
3. 可以使用新的 Facade 来简化代码

---

## 许可证

Apache-2.0
