# TsaoTai Addons

> 基于 ThinkPHP 8 的插件化管理系统

## 项目简介

TsaoTai Addons 是一个专门为 ThinkPHP 8 设计的插件系统，提供完整的插件开发、管理和运行支持。支持自动加载插件、路由注册、配置管理等功能，让插件开发更加简单高效。

## 技术栈

| 分类 | 技术 | 说明 |
| --- | --- | --- |
| 框架 | ThinkPHP 8 | 核心框架 |
| PHP | >= 8.0.0 | PHP 版本要求 |
| 缓存 | ThinkPHP Cache（可选） | 插件列表缓存 |
| 日志 | ThinkPHP Log（可选） | 异常日志记录 |
| 插件 | Composer | 插件化管理 |

## 系统架构

```
tsaotai-addons/
├── src/                    # 核心源码
│   ├── Addons.php         # 插件管理器
│   ├── Loader.php         # 插件加载器
│   ├── Router.php         # 路由注册器（清单缓存 + plugin 单网关）
│   ├── PluginGateway.php  # plugin/:id 管理路由分发
│   ├── Generator.php      # 插件生成器
│   ├── AddonDiscovery.php # 插件发现服务
│   ├── BaseController.php # 基础控制器
│   ├── PluginController.php # 插件管理控制器
│   ├── Config.php         # 配置管理
│   ├── Service.php        # 服务提供者
│   ├── command/           # 命令行工具
│   └ facade/              # Facade 支持
│   └ helper.php           # 助手函数
│
├── config.php             # 默认配置
├── composer.json          # 包配置
├── CHANGELOG.md           # 更新日志
├── README.md              # 说明文档
└── AI-DEVELOPMENT-GUIDE.md # AI 开发指南
```

## 核心特性

- ✅ **自动加载**：安装后自动加载插件和路由，无需额外配置
- ✅ **插件生成器**：命令行快速创建插件，支持多种可选目录
- ✅ **缓存机制**：可选的插件列表和配置缓存，提升性能
- ✅ **异常处理**：单个插件失败不影响其他插件
- ✅ **向后兼容**：保留旧版本控制器，确保旧插件正常运行
- ✅ **规范开发**：标准化插件开发模板和目录结构

## 安装

```bash
composer require tsaotai/tsaotai-addons
```

安装后，包会通过 ThinkPHP 服务提供者自动加载插件和路由，无需额外配置！

## 快速创建插件

### 命令行方式（推荐）

支持 3 种命令：

```bash
php think addon:make demo
php think plugin:make demo
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

### 命令选项说明

| 选项 | 说明 |
| --- | --- |
| `--title` | 插件标题 |
| `--description` | 插件描述 |
| `--author` | 插件作者 |
| `--plugin-version` | 插件版本 |
| `--with-model` | 创建 model 目录 |
| `--with-validate` | 创建 validate 目录 |
| `--with-public` | 创建 public 目录 |
| `--with-base` | 创建插件自己的 Base 控制器（默认启用） |
| `--with-config` | 创建 `config.php` 插件配置文件 |
| `--with-common` | 创建 `common.php` 公共函数文件 |
| `--with-service` | 创建 `service.php` 服务文件 |
| `--with-provider` | 创建 `provider.php` 服务提供者 |
| `--with-event` | 创建 `event.php` 事件配置 |
| `--with-middleware` | 创建 `middleware.php` 中间件配置 |

## 插件目录结构

```
addons/
└── demo/                    # 插件目录（唯一标识）
    ├── controller/          # 控制器
    │   ├── Base.php         # 基础控制器
    │   ├── Demo.php         # 主控制器
    │   └── Plugin.php       # 插件管理控制器
    ├── view/                # 视图文件
    │   ├── demo/            # 插件视图
    │   └── plugin/          # 管理视图
    ├── data/                # 数据文件
    │   └── plugin/
    │       ├── readme.md
    │       ├── rule.md
    │       └── update.md
    ├── route.php            # 插件路由
    ├── plugin.php           # 插件配置（必填）
    ├── README.md            # 插件说明
    └── .gitignore           # Git 忽略文件
```

## 插件配置说明

`plugin.php` 是插件的核心配置文件：

```php
<?php
return [
    'identifier'  => 'demo',       // 唯一标识（必填，与目录名一致）
    'title'       => '示例插件',    // 插件名称
    'description' => '这是一个示例插件', // 插件描述
    'version'     => '2026.1.1',   // 版本号
    'author'      => '教员',        // 作者
    'icon'        => 'tools',      // Bootstrap Icons 图标名
    'state'       => 'enable',     // 状态：enable/disable
    'entry'       => 'addons/demo', // 后台入口
    'licence'     => 'Apache-2.0', // 开源协议
];
```

## 缓存管理

### 缓存机制

- ✅ **缓存驱动**：使用 ThinkPHP 默认缓存驱动（文件缓存，存储在 `runtime/cache/`）
- ✅ **缓存有效期**：1小时（3600秒）
- ✅ **自动清理**：安装/卸载插件时自动清除缓存
- ✅ **可选依赖**：当 Cache 不可用时自动降级

### 手动清除缓存

```php
<?php
use tsaotai\addons\AddonDiscovery;
use tsaotai\addons\facade\Addons;

AddonDiscovery::clearCache();
AddonDiscovery::clearPluginCache('demo');

Addons::clearCache();
Addons::clearPluginCache('demo');
```

## API 文档

### 类说明

| 类名 | 说明 |
| --- | --- |
| `tsaotai\addons\BaseController` | 插件基础控制器 |
| `tsaotai\addons\PluginController` | 插件管理控制器 |
| `tsaotai\addons\Loader` | 插件加载器 |
| `tsaotai\addons\Router` | 路由注册器 |
| `tsaotai\addons\PluginGateway` | `plugin/:id` 管理路由分发 |
| `tsaotai\addons\Addons` | 插件管理器 |
| `tsaotai\addons\Generator` | 插件生成器 |
| `tsaotai\addons\AddonDiscovery` | 插件发现服务 |
| `tsaotai\addons\Config` | 配置管理类 |
| `tsaotai\addons\Service` | 服务提供者 |

### AddonDiscovery API

| 方法 | 说明 |
| --- | --- |
| `getAddonNames($useCache = true)` | 获取所有插件目录名称 |
| `getAddonPath($name)` | 获取插件路径 |
| `exists($name)` | 检查插件是否存在 |
| `hasConfig($name)` | 检查插件是否有配置文件 |
| `getConfig($name)` | 获取插件配置 |
| `isInstalled($name)` | 检查插件是否已安装 |
| `validateIdentifier($name)` | 校验 identifier 与目录名一致 |
| `clearCache()` | 清除所有插件缓存 |
| `clearPluginCache($name)` | 清除单个插件缓存 |

### 助手函数

| 函数 | 说明 |
| --- | --- |
| `addons()` | 获取插件管理器实例 |
| `addons_url($url)` | 生成插件 URL |
| `addons_view($template, $vars)` | 渲染插件视图 |
| `addons_path($path)` | 获取插件目录路径 |

## 升级指南

### 从 2026.1.x 升级到 2026.1.8

1. **插件业务路由无感**：各插件 `route.php` 照常加载
2. **插件管理 URL 不变**：`/plugin/{id}`、`/plugin/{id}/update` 等仍可用，改为单路由分发
3. **安装/卸载请用 POST**（原先 `Route::any` 的 GET 安装将返回 JSON 提示）
4. 手动清路由清单：`tsaotai\addons\Router::clearManifest()`

### 从 2026.1.x 升级到 2026.1.7

1. **完全兼容，无需修改现有插件代码** ✅
2. **Bug 修复**：
   - ✅ 修复路由加载问题，恢复 Router.php 中的路由加载逻辑
   - ✅ Loader.php 移除路由加载，避免路由被错误包装
3. **稳定性提升**：缓存和日志改为可选依赖
   - ✅ 当 ThinkPHP Cache 不可用时自动降级
   - ✅ 当 ThinkPHP Log 不可用时自动降级

### 从 2026.1.x 升级到 2026.1.6

1. **完全兼容，无需修改现有插件代码** ✅
2. **Bug 修复**：
   - ✅ 修复路由加载问题，避免路由变成 `addons/demo/demo`
3. **稳定性提升**：增强异常处理
   - ✅ 缓存操作添加 try-catch
   - ✅ 日志操作添加 try-catch

### 从 2026.1.x 升级到 2026.1.5

1. **完全兼容，无需修改现有插件代码** ✅
2. **性能优化**：新增配置缓存机制
   - ✅ 插件列表缓存（TTL 1小时）
   - ✅ 插件配置缓存（TTL 1小时）
3. **稳定性提升**：新增异常处理
   - ✅ 单个插件失败不影响其他插件
   - ✅ 使用 Log 记录警告和错误信息

## AI 开发指南

使用 AI 辅助开发插件，请查看 [AI-DEVELOPMENT-GUIDE.md](./AI-DEVELOPMENT-GUIDE.md)。

## License

Apache-2.0