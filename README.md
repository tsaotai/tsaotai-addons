# TsaoTai Addons

TsaoTai plugin system for ThinkPHP 8.

## 安装

```bash
composer require tsaotai/tsaotai-addons
```

## 快速开始

### 1. 配置插件加载器

在 `app/addons.php`：
```php
<?php
tsaotai\addons\Loader::load();
```

### 2. 配置插件路由

在 `route/addons.php`：
```php
<?php
tsaotai\addons\Router::register();
```

### 3. 创建你的第一个插件

#### 目录结构
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

#### 插件配置 `plugin.php`
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

#### 前台控制器 `controller/Index.php`
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

#### 插件管理控制器 `controller/Plugin.php`
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

#### 视图文件 `view/index/index.html`
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

### 4. 访问插件

- 前台页面：`http://your-domain/addons/demo`
- 插件管理：`http://your-domain/plugin/demo`

## 类说明

| 类名 | 说明 |
| --- | --- |
| `tsaotai\addons\BaseController` | 插件基础控制器 |
| `tsaotai\addons\CommonController` | 插件基础控制器（带登录验证） |
| `tsaotai\addons\PluginController` | 插件管理控制器 |
| `tsaotai\addons\Loader` | 插件加载器 |
| `tsaotai\addons\Router` | 插件路由注册器 |

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

## 许可证

Apache-2.0
