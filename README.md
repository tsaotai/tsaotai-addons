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
- [AI 开发指南](#ai-开发指南)
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
php think addon:make demo --title="示例插件" --description="这是一个示例插件" --author="教员" --plugin-version="2026.1.1"
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
    'version' => '2026.1.1',
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
    // 【核心唯一标识】必须与插件目录名一致，小写字母，不可重复、不可修改
    'identifier' => 'demo',
    
    // 【插件展示名称】后台列表、插件页头部正式显示名称，支持中文
    'title' => '示例插件',
    
    // 【插件完整描述】详细说明插件作用、能力、使用场景，禁止过短，便于后期查阅
    'description' => '这是一个示例插件',
    
    // 【当前版本号】语义化版本格式 主版本.次版本.修订号
    'version' => '2026.1.1',
    
    // 【开发作者】填写开发负责人/团队名称，用于版权与溯源
    'author' => '教员',
    
    // 【创建/更新日期】固定格式 YYYY-MM-DD，版本迭代同步更新
    'create' => '2026-05-03',
    'update' => '2026-05-03',
    
    // 【图标标识】BootstrapIcons 纯图标名，不带 bi- 前缀
    'icon' => 'tools',
    
    // 【功能分类】tool=工具类 / function=功能类 / business=业务类
    'category' => 'tool',
    
    // 【排序权重】纯数字，数值越大，后台插件列表展示越靠前
    'sort' => 0,
    
    // 【运行状态】enable=默认启用 / disable=默认禁用
    'state' => 'enable',
    
    // 【独立配置页】布尔值 true=存在单独配置页面 / false=无额外配置
    'config' => false,
    
    // 【安装流程】布尔值 true=需要执行安装逻辑（建表/初始化数据） / false=直接启用
    'install' => true,
    
    // 【卸载清理】布尔值 true=卸载同步删除业务数据 / false=保留数据，防止误删丢失
    'clean' => false,
    
    // 【依赖插件】多个依赖逗号分隔，无依赖留空；填写目标插件 identifier 标识
    'rely' => '',
    
    // 【后台访问入口】插件独立管理页面路由地址，用于菜单点击跳转
    'entry' => 'addons/demo',
    
    // 【适用范围】admin=仅后台使用 / index=仅前台使用 / all=全模块通用
    'scope' => 'admin',
    
    // 【插件品类】free=免费版 / basic=基础版 / pro=专业付费版
    'classify' => 'free',
    
    // 【文档地址】填写文档/知识库链接，无文档留空
    'domain' => '',
    
    // 【开源协议】标注版权协议，内部项目可自定义填写内部专用
    'licence' => 'Apache-2.0',
    
    // 【补充备注】填写使用注意事项、特殊说明、限制条件，运营/维护查看
    'remark' => ''
];
```

### 3. 开发主控制器

主控制器继承 `BaseController`：

```php
<?php
declare (strict_types=1);

namespace addons\demo\controller;

use tsaotai\addons\BaseController;

class Demo extends BaseController
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

**视图解析规则**：

`BaseController::fetch()` 方法严格遵循 TP8 原生规范：

- 空模板时：自动解析为 `控制器目录/控制器名(小写)/方法名`
- 示例：`Demo::index()` → `view/demo/index.html`
- 支持完整路径：`$this->fetch('demo/index')`

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

### 7. 自定义路由

插件可以在 `route.php` 中定义自定义路由：

```php
<?php
use think\facade\Route;

// 插件自定义路由示例
Route::get('demo/api/list', 'addons\demo\controller\Demo@list');
```

**自动路由注册**：

系统会自动为每个启用的插件注册以下标准路由：

| 路由 | 方法 | 说明 |
| --- | --- | --- |
| `plugin/{identifier}` | ANY | 插件管理首页 |
| `plugin/{identifier}/install` | ANY | 安装插件 |
| `plugin/{identifier}/uninstall` | ANY | 卸载插件 |
| `plugin/{identifier}/update` | ANY | 更新日志 |
| `plugin/{identifier}/rule` | ANY | 使用规则 |

### 8. 加载插件资源

插件可以加载多种资源文件：

```php
// 配置文件：config.php
// 公共函数：common.php  
// 请求绑定：request.php
// 服务文件：service.php
// 依赖注入：provider.php
// 事件监听：event.php
// 中间件：middleware.php
```

这些文件会在插件加载时自动引入。

### 9. 使用助手函数

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

### 10. 访问插件

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

## AI 开发指南

使用 AI（如 Claude、GPT 等）辅助开发插件，请查看 [AI-DEVELOPMENT-GUIDE.md](./AI-DEVELOPMENT-GUIDE.md)。

包含内容：
- 给 AI 的完整项目上下文
- 插件结构说明
- 常用开发模式
- 可复制的提示词模板
- 完整示例（待办事项插件）

---

## API 文档

### 类说明

| 类名 | 说明 |
| --- | --- |
| `tsaotai\addons\BaseController` | 插件基础控制器（推荐新插件使用），1:1 复刻 TP8 原生视图解析规则 |
| `tsaotai\addons\CommonController` | 旧版本基础控制器（保留，向后兼容） |
| `tsaotai\addons\PluginController` | 插件管理控制器，提供安装/卸载/update/rule 方法 |
| `tsaotai\addons\Loader` | 插件加载器，自动加载配置、路由、事件、中间件等 |
| `tsaotai\addons\Router` | 插件路由注册器，自动注册标准插件路由 |
| `tsaotai\addons\Addons` | 插件管理器，提供插件列表、创建等功能 |
| `tsaotai\addons\Generator` | 插件生成器，命令行创建插件的核心实现 |
| `tsaotai\addons\AddonDiscovery` | 插件发现服务（统一插件目录扫描） |
| `tsaotai\addons\Config` | 配置管理类 |
| `tsaotai\addons\Service` | ThinkPHP 服务提供者，自动注册服务和命令 |

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

### AddonDiscovery API

| 方法 | 说明 |
| --- | --- |
| `AddonDiscovery::getAddonNames()` | 获取所有插件目录名称 |
| `AddonDiscovery::getAddonPath($name)` | 获取插件路径 |
| `AddonDiscovery::exists($name)` | 检查插件是否存在 |
| `AddonDiscovery::hasConfig($name)` | 检查插件是否有配置文件 |
| `AddonDiscovery::getConfig($name)` | 获取插件配置 |
| `AddonDiscovery::isInstalled($name)` | 检查插件是否已安装 |

---

## 插件开发规范

### 命名规范

- 插件目录名：纯小写英文，无下划线，不可修改
- 控制器类名：大驼峰，继承对应基类
- 视图文件：小写下划线分隔
- 配置字段：小写字母，下划线分隔

### 目录说明

| 目录/文件 | 说明 | 必填 |
| --- | --- | --- |
| `controller/` | 控制器 | 是 |
| `view/` | 视图 | 是 |
| `data/` | 数据文件（update.md/rule.md） | 是 |
| `model/` | 模型 | 否 |
| `validate/` | 验证器 | 否 |
| `public/` | 公共资源（css/js/images） | 否 |
| `plugin.php` | 插件配置 | **是** |
| `route.php` | 插件路由 | 否 |
| `common.php` | 公共函数 | 否 |
| `request.php` | 请求绑定 | 否 |
| `service.php` | 服务文件 | 否 |
| `provider.php` | 依赖注入 | 否 |
| `event.php` | 事件监听 | 否 |
| `middleware.php` | 中间件 | 否 |

### 版本号规范

采用年份.季度.修订号格式：
- `2026.1.1` - 2026年第一季度第1个版本
- `2026.2.3` - 2026年第二季度第3个版本

---

## 升级指南

### 从 1.7.x 升级到 2026.1.1

1. **完全兼容，无需修改现有插件代码** ✅
   - `CommonController` 已保留作为向后兼容
   - 旧插件可以继续使用
2. **新插件建议**：直接继承 `BaseController`
3. 版本号格式更新为 2026.1.1
4. `BaseController::fetch()` 采用 TP8 原生视图解析规则
5. **稳定性修复**：
   - 移除不正确的 `Request::bind()` 用法（`request.php` 文件不再自动加载）
   - 增加 `AddonDiscovery::validateIdentifier()` 方法，校验插件 identifier 与目录名一致
   - 加载器和路由注册时会跳过 identifier 不一致的插件，防止配置错误导致的潜在问题

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

## License

Apache-2.0