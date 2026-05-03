# TsaoTai Addons

TsaoTai plugin system for ThinkPHP 8.

[![Latest Stable Version](https://img.shields.io/packagist/v/tsaotai/tsaotai-addons.svg)](https://packagist.org/packages/tsaotai/tsaotai-addons)
[![License](https://img.shields.io/packagist/l/tsaotai/tsaotai-addons.svg)](https://packagist.org/packages/tsaotai/tsaotai-addons)

---

## 目录

- [安装](#安装)
- [快速开始](#快速开始)
- [配置](#配置)
- [快速创建插件](#快速创建插件)
- [开发插件指南](#开发插件指南)
- [API 文档](#api-文档)
- [升级指南](#升级指南)

---

## 安装

```bash
composer require tsaotai/tsaotai-addons
```

---

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

## 快速创建插件

### 方式 1：命令行（推荐）

现在支持 3 种命令，任你选择：

```bash
# 创建基本插件
php think addon:make demo

# 或者这个
php think plugin:make demo

# 原来的命令也能用
php think make:addon demo
```

**创建带完整信息的插件：**

```bash
php think addon:make demo --title="示例插件" --description="这是一个示例插件" --author="教员" --plugin-version="1.0.0"
```

**创建包含可选目录的插件：**

```bash
php think addon:make demo --with-model --with-validate --with-public
```

**命令选项说明：**

| 选项 | 说明 |
| --- | --- |
| `--title` | 插件标题 |
| `--description` | 插件描述 |
| `--author` | 插件作者 |
| `--plugin-version` | 插件版本 |
| `--with-model` | 创建 model 目录 |
| `--with-validate` | 创建 validate 目录 |
| `--with-public` | 创建 public 目录 |

### 方式 2：代码方式

```php
<?php
use tsaotai\addons\facade\Addons;

// 快速创建一个插件
Addons::create('demo', [
    'title' => '示例插件',
    'description' => '这是一个示例插件',
    'author' => '教员',
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

## 开发插件指南

### 1. 理解插件结构

自动生成的插件目录结构如下：

```
addons/
└── demo/                    # 插件目录（唯一标识）
    ├── controller/          # 控制器
    │   ├── Demo.php         # 主控制器（与插件名一致）
    │   └── Plugin.php       # 插件管理控制器
    ├── view/                # 视图文件
    │   ├── demo/            # 插件视图
    │   │   └── index.html
    │   └── plugin/          # 管理视图
    │       ├── index.html
    │       ├── rule.html
    │       └── update.html
    ├── data/                # 数据文件
    │   └── plugin/
    │       ├── readme.md
    │       ├── rule.md
    │       └── update.md
    ├── route.php            # 插件自定义路由（可选）
    ├── plugin.php           # 插件配置（必填）
    ├── README.md            # 插件说明
    └── .gitignore           # Git 忽略文件

# 可选目录（通过参数指定）
    ├── model/               # 模型（可选）
    ├── validate/            # 验证器（可选）
    └── public/              # 公共资源（可选）
        ├── css/
        ├── js/
        └── images/
```

### 2. 插件配置详解

`plugin.php` 是插件的核心配置文件，所有字段都有详细注释：

```php
<?php
return [
    // 【核心唯一标识】必须与插件目录名一致
    'identifier' => 'demo',
    
    // 【插件展示名称】后台显示
    'title' => '示例插件',
    
    // 【插件完整描述】详细说明
    'description' => '这是一个示例插件',
    
    // 【当前版本号】语义化版本
    'version' => '1.0.0',
    
    // 【开发作者】
    'author' => '教员',
    
    // 【创建/更新日期】
    'create' => '2026-05-03',
    'update' => '2026-05-03',
    
    // 【图标标识】BootstrapIcons 名称
    'icon' => 'tools',
    
    // 【功能分类】tool/function/business
    'category' => 'tool',
    
    // 【排序权重】数字越大越靠前
    'sort' => 0,
    
    // 【运行状态】enable/disable
    'state' => 'enable',
    
    // 【独立配置页】true/false
    'config' => false,
    
    // 【安装流程】是否需要安装逻辑
    'install' => true,
    
    // 【卸载清理】是否删除数据
    'clean' => false,
    
    // 【依赖插件】逗号分隔
    'rely' => '',
    
    // 【后台访问入口】
    'entry' => 'addons/demo',
    
    // 【适用范围】admin/index/all
    'scope' => 'admin',
    
    // 【插件品类】free/basic/pro
    'classify' => 'free',
    
    // 【文档地址】
    'domain' => '',
    
    // 【开源协议】
    'licence' => 'Apache-2.0',
    
    // 【补充备注】
    'remark' => ''
];
```

### 3. 开发主控制器

主控制器继承 `CommonController`（自动带登录验证）：

```php
<?php
declare (strict_types=1);

namespace addons\demo\controller;

use tsaotai\addons\CommonController;

class Demo extends CommonController
{
    // 插件首页
    public function index()
    {
        // 分配变量到视图
        $this->assign('name', 'TsaoTai');
        
        // 渲染视图（自动定位到 view/demo/index.html）
        return $this->fetch();
    }
    
    // API 接口示例
    public function api()
    {
        return json(['code' => 1, 'msg' => 'success', 'data' => []]);
    }
}
```

### 4. 开发插件管理控制器

管理控制器继承 `PluginController`，自动拥有安装/卸载功能：

```php
<?php
declare (strict_types=1);

namespace addons\demo\controller;

use tsaotai\addons\PluginController;

class Plugin extends PluginController
{
    // 自定义安装逻辑
    public function install(): \think\response\Json
    {
        // 你的安装代码，比如建表、初始化数据
        // ...
        
        return parent::install();
    }
    
    // 自定义卸载逻辑
    public function uninstall(): \think\response\Json
    {
        // 你的卸载代码
        // ...
        
        return parent::uninstall();
    }
    
    // 插件设置页面
    public function setting()
    {
        return $this->fetch();
    }
}
```

### 5. 视图开发

视图文件放在 `view/` 目录下：

```html
<!-- view/demo/index.html -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Demo Plugin</title>
</head>
<body>
    <h1>Hello, {$name}!</h1>
</body>
</html>
```

### 6. 数据文件

插件数据放在 `data/` 目录下：

- `data/plugin/update.md` - 更新日志
- `data/plugin/rule.md` - 使用规则
- `data/plugin/readme.md` - 数据说明

### 7. 使用助手函数

包提供了便捷的助手函数：

#### addons()

```php
<?php
// 获取插件管理器
$addons = addons();

// 获取单个插件信息
$demo = addons('demo');
```

#### addons_url()

```php
<?php
// 生成插件 URL
$url = addons_url('demo/index');
// 相当于 url('addons/demo/index')
```

#### addons_view()

```php
<?php
// 渲染插件视图
return addons_view('demo/index/index', $vars);
```

#### addons_path()

```php
<?php
// 获取插件根目录
$path = addons_path();

// 获取指定插件目录
$demoPath = addons_path('demo');

// 获取插件下的文件
$pluginConfig = addons_path('demo/plugin.php');
```

### 8. 访问插件

- 前台页面：`http://your-domain/addons/demo`
- 插件管理：`http://your-domain/plugin/demo`

---

## 使用 Facade（更简洁）

```php
<?php
use tsaotai\addons\facade\Addons;

// 获取所有插件信息
$addons = Addons::getAddons();

// 扫描插件目录
$addons = Addons::scanAddons();

// 创建新插件
Addons::create('demo', ['title' => '示例插件']);
```

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
| `tsaotai\addons\AddonDiscovery` | 插件发现服务（统一插件目录扫描） |
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
- `model/` - 模型（可选）
- `validate/` - 验证器（可选）
- `public/` - 公共资源（可选）
- `plugin.php` - 插件配置（必填）
- `route.php` - 插件路由（可选）
- `common.php` - 公共函数（可选）

---

## 升级指南

### 从 1.5.x 升级到 1.6.x

1. 无需修改现有插件代码
2. 可以使用新的命令别名 `addon:make` 和 `plugin:make`
3. 插件生成器现在生成的结构更符合规范

### 从 1.4.x 升级到 1.5.x

1. 无需修改现有插件代码
2. 可以使用新的命令行工具创建插件
3. `--version` 选项改为 `--plugin-version` 避免冲突

### 从 1.3.x 升级到 1.4.x

1. 无需修改现有插件代码
2. 内部重构，使用 `AddonDiscovery` 统一插件发现逻辑

### 从 1.2.x 升级到 1.3.x

1. 无需修改现有插件代码
2. 可以使用新的插件生成器
3. 可以使用 `addons_path()` 助手函数

### 从 1.1.x 升级到 1.2.x

1. 无需修改现有插件代码
2. 可以使用新的配置功能
3. 可以使用新的助手函数

### 从 1.0.x 升级到 1.1.x

1. 无需修改现有插件代码
2. 建议删除 `app/addons.php` 和 `route/addons.php` 中的手动配置，使用自动加载
3. 可以使用新的 Facade 来简化代码

---

##