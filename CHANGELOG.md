# 更新日志

格式参考 [Keep a Changelog](https://keepachangelog.com/zh-CN/1.0.0/)。

***

## [2026.1.9] - 2026-09-05

### 移除

- 脚手架：`Generator`、`php think addon:make` / `plugin:make` / `make:addon`
- 默认管理路由 `plugin/:id/[:action]` 与 `PluginGateway`
- `PluginController`（安装/卸载/`install.lock`、update/rule 页）
- 空壳 `CommonController`
- `addons_url()` / `addons_view()`
- `Router` 与 `runtime/cache/addons_route_manifest.php`（生产环境 1 小时脏清单会 404）
- Think Cache 对目录/配置的 1 小时缓存

### 优化

- 启动一次遍历：加载约定文件 + `require route.php`
- 同进程内 `plugin.php` 只读一遍；只认带 `plugin.php` 的目录
- `clearCache()` 会删掉旧的路由清单文件

---

## [2026.1.8] - 2026-09-04

### 🔧 优化

- **插件管理路由收口**：`plugin/:identifier/[:action]` 一条网关替代每个插件 5 条 `plugin/{id}/install|uninstall|...` 路由；install/uninstall 仅接受 POST
- **路由清单缓存**：启用插件的 `route.php` 写入 `runtime/cache/addons_route_manifest.php`（默认 3600s，`app_debug` 下不缓存）；安装/卸载时 `Router::clearManifest()`
- **BaseController**：明确不复制主题 `base.html`，只设置插件 `view_path`（`{extend name="../view/..."}` 解析到项目根 `view/`）

---

## [2026.1.7] - 2026-05-30

### 🐛 修复

- ✅ **路由加载问题修复**：恢复 Router.php 中的路由加载逻辑
  - Router.php 负责加载插件自定义 route.php（无分组包装，保持原有路由格式）
  - Loader.php 移除路由加载，只负责加载配置、服务等
  - 避免路由被错误包装导致所有插件无法访问

### 🛡️ 增强稳定性

- ✅ **缓存机制改为可选依赖**：当 ThinkPHP Cache 不可用时自动降级
  - 移除 `use think\facade\Cache` 硬依赖
  - 使用 `class_exists()` 动态检测 Cache 是否可用
  - 所有缓存操作包裹在 try-catch 中，失败时自动回退

- ✅ **日志系统改为可选依赖**：当 ThinkPHP Log 不可用时自动降级
  - 移除 `use think\facade\Log` 硬依赖
  - 使用 `class_exists()` 动态检测 Log 是否可用
  - 所有日志操作包裹在 try-catch 中，失败时自动忽略

---

## [2026.1.6] - 2026-05-30

### 🐛 修复

- ✅ **路由加载问题修复**：恢复 Router.php 中的路由加载，移除 Loader.php 中的路由加载
  - Router.php 负责加载插件自定义路由（无分组包装，保持原有路由格式）
  - Loader.php 只负责加载配置、服务等，不再加载路由
  - 避免路由被错误包装导致 `addons/demo/demo` 而不是 `demo`

### 🛡️ 增强稳定性

- ✅ **缓存机制异常处理增强**：当 ThinkPHP Cache 不可用时自动降级，不影响正常功能
  - `AddonDiscovery::useCache()` - 检测缓存是否可用
  - 所有缓存操作添加 try-catch，防止缓存异常导致插件加载失败
  - 移除 `use think\facade\Cache` 依赖，改为动态检测

- ✅ **日志系统异常处理增强**：当 ThinkPHP Log 不可用时自动降级
  - `Loader::useLog()` - 检测日志是否可用
  - `Router::useLog()` - 检测日志是否可用
  - 所有日志操作添加 try-catch，防止日志异常影响插件加载
  - 移除 `use think\facade\Log` 依赖，改为动态检测

### 📚 文档更新

- ✅ 更新 README.md，添加缓存管理使用说明

---

## [2026.1.5] - 2026-05-30

### 🐛 修复

- ✅ **route.php 重复加载问题修复**：Router 中移除了对 route.php 的重复加载，避免路由冲突
- ✅ **createBaseController 逻辑错误修复**：修复了条件判断逻辑，保持原有行为

### ✨ 新增

- ✅ **配置缓存机制**：使用 ThinkPHP Cache 缓存插件列表和配置，提升性能
  - `AddonDiscovery::getAddonNames()` - 支持缓存，TTL 1小时
  - `AddonDiscovery::getConfig()` - 支持缓存，TTL 1小时
  - `AddonDiscovery::clearCache()` - 清除所有缓存
  - `AddonDiscovery::clearPluginCache()` - 清除单个插件缓存

- ✅ **异常处理机制**：增强系统稳定性
  - `Loader::load()` - 添加 try-catch，单个插件失败不影响其他插件
  - `Router::register()` - 添加 try-catch，单个插件路由注册失败不影响其他插件
  - 使用 Log 记录警告和错误信息

### 🔧 改进

- ✅ **PluginController 安装/卸载时自动清除缓存**：确保缓存及时更新
- ✅ **Addons::create() 创建插件后自动清除缓存**：确保新插件立即可见
- ✅ **Addons::clearCache() / clearPluginCache()**：新增方法方便外部调用

---

## [2026.1.4] - 2026-05-11

### ✨ 新增

- ✅ **插件生成器增强**：新增更多可选文件选项
  - `--with-base` - 创建插件自己的 `controller/Base.php`（默认启用，继承 `addons\common\AuthBase`）
  - `--with-config` - 创建 `config.php` 插件配置文件
  - `--with-common` - 创建 `common.php` 公共函数文件
  - `--with-service` - 创建 `service.php` 服务文件
  - `--with-provider` - 创建 `provider.php` 服务提供者
  - `--with-event` - 创建 `event.php` 事件配置
  - `--with-middleware` - 创建 `middleware.php` 中间件配置

### 🔧 改进

- ✅ **控制器继承结构优化**：主控制器现在继承插件自己的 Base（继承 `addons\common\AuthBase`）
- ✅ **视图模板更新**：统一使用 ThinkPHP 模板引擎格式，继承 `admin@public/base`
- ✅ **文档完善**：更新 README.md，添加所有新选项的说明
- ✅ **插件生成器优化**：新增 `createOptionalFiles()` 方法统一处理可选文件

### 📚 文档更新

- ✅ 更新 README.md，新增生成器选项、目录结构、升级指南等内容

---

## [2026.1.3] - 2026-05-08

### ✨ 新增

- ✅ **插件生成器增强**：更新 `MakeAddon` 命令和 `Generator` 类，支持以下新选项
  - `--with-base` - 生成插件自己的 `controller/Base.php`（默认启用）
  - `--with-config` - 生成 `config.php` 插件配置文件
  - `--with-common` - 生成 `common.php` 公共函数文件
  - `--with-service` - 生成 `service.php` 服务文件
  - `--with-provider` - 生成 `provider.php` 服务提供者
  - `--with-event` - 生成 `event.php` 事件配置
  - `--with-middleware` - 生成 `middleware.php` 中间件配置

### 🔧 改进

- ✅ **控制器继承链更新**：主控制器现在继承插件自己的 `Base`（继承 `addons\common\AuthBase`），符合最新架构规范
- ✅ **视图模板更新**：使用 ThinkPHP 模板引擎格式，继承 `admin@public/base`
- ✅ **代码简化**：Service.php 使用箭头函数简化服务绑定代码

### 📚 文档更新

- ✅ 更新 README.md，添加新生成器选项说明

---

## [2026.1.2] - 2026-05-08

### 🛡️ 稳定性修复

- ✅ **移除不正确的 `Request::bind()` 用法**：Loader.php 中移除了对 `request.php` 文件的自动加载，因为原来的用法不符合 ThinkPHP 8 的规范
- ✅ **新增 `validateIdentifier()` 方法**：在 AddonDiscovery.php 中添加了插件 identifier 与目录名一致性校验
- ✅ **增强 Loader 安全性**：加载插件时校验 identifier，不一致则跳过，防止配置错误导致的潜在问题
- ✅ **增强 Router 安全性**：路由注册时校验 identifier，不一致则跳过

### 📚 文档更新

- ✅ 更新 README.md，添加稳定性修复说明

---

## [2026.1.1] - 2026-05-07

### 🚀 重大更新

- ✅ **保留 CommonController 类**：作为向后兼容的空壳，继承自 BaseController
- ✅ **BaseController 完全恢复 1.7.7 状态**：确保旧插件 100% 兼容
- ✅ **BaseController::initialize() 移除返回类型**：保持与旧插件的兼容性
- ✅ **PluginController 继承结构调整**：现在直接继承 BaseController
- ✅ **统一使用 `declare (strict_types=1);`**：所有文件统一类型声明
- ✅ **Loader 和 Router 类添加返回类型声明**
- ✅ **Service.php 修复 Config 类导入问题**
- ✅ **版本号统一使用 2026.1.1**
- ✅ **更新 Generator**：生成的控制器继承 BaseController
- ✅ **Loader 简化**：移除多余的 isPluginEnabled() 方法，直接内联逻辑

### 📚 文档更新

- ✅ 新增 AI-DEVELOPMENT-GUIDE.md - AI 辅助开发插件指南

---

## [1.7.7] - 2026-05-03

### ✨ 最终优化版本

- ✅ 完整的插件系统功能
- ✅ 统一的 CHANGELOG 格式（无 v 前缀）
- ✅ 插件生成器包含 route.php
- ✅ 完善的文档和示例
- ✅ 所有版本优化的整合

---

## [1.7.6] - 2026-05-03

### ✨ 优化完善

- ✅ 补充 route.php 文件生成功能
- ✅ 修复生成的 update.md 版本号格式（移除v前缀）
- ✅ 优化 MakeAddon 命令使用容器获取 Generator
- ✅ 更新 README 目录结构，包含 route.php

---

## [1.7.5] - 2026-05-03

### ✨ 完善和优化

- ✅ 统一 CHANGELOG 格式和样式
- ✅ 优化插件生成器细节
- ✅ 更新文档说明

---

## [1.7.0] - 2026-05-03

### ✨ 优化插件生成器

- ✅ 新增 route.php 文件生成
- ✅ 优化视图文件，样式与 demo 保持一致
- ✅ 优化 data/plugin/update.md 格式
- ✅ 保持 rule.md 原样
- ✅ 不依赖外部 demo 目录，代码更简洁
- ✅ 新增完整的插件视图模板
- ✅ 更新 README 文档说明

---

## [1.6.1] - 2026-05-03

### 🐛 修复

- ✅ **createDirectoryStructure() 方法缺少 $name 参数的 bug**：
  - 在 `createDirectoryStructure()` 方法签名中添加 $name 参数
  - 更新 `create()` 方法中的调用，传入 $name

---

## [1.6.0] - 2026-05-03

### ✨ 优化插件生成器，添加命令别名

- ✅ 完全重写插件生成器，使其生成的结构与 demo 一致
- ✅ 主控制器类名现在与插件名一致（如 Demo 插件生成 Demo 控制器，而非 Index 控制器）
- ✅ 使用 CommonController 替代 BaseController
- ✅ 视图目录结构调整为 view/demo/ 和 view/plugin/
- ✅ 补充完整的视图文件：view/plugin/index.html、view/plugin/rule.html、view/plugin/update.html
- ✅ 新增 data/ 目录，包含 data/plugin/readme.md、data/plugin/update.md、data/plugin/rule.md
- ✅ 自动生成插件的 README.md 和 .gitignore
- ✅ 修复 plugin.php 配置文件中 $date 变量未定义的问题
- ✅ 命令添加别名，现在可以用 `addon:make` 或 `plugin:make` 快速创建插件

---

## [1.5.1] - 2026-05-03

### 🐛 修复

- ✅ **命令选项名冲突修复**：
  - 将 MakeAddon.php 中的 `--version` 选项重命名为 `--plugin-version`
  - 避免与 ThinkPHP 自带的 `--version` 选项冲突
  - 更新 README.md 中的命令使用示例

---

## [1.5.0] - 2026-05-03

### ✨ 新增命令行工具

- ✅ 添加 `make:addon` 命令行工具
- ✅ 支持通过命令行快速创建插件
- ✅ 支持可选目录通过参数指定：--with-model、--with-validate、--with-public
- ✅ 支持传入插件信息：--title、--description、--author、--plugin-version
- ✅ 更新 Service.php，注册 MakeAddon 命令

---

## [1.4.1] - 2026-05-03

### 🐛 修复

- ✅ **路径处理和类型安全问题修复**：
  - 修复 AddonDiscovery.php，在 getAddonPath() 方法中添加 rtrim() 处理
  - 在 getConfig() 方法中添加类型安全检查，当配置不是数组时返回空数组
  - 修复 Generator.php，在 writeFile() 方法后添加缺失的 createdPaths 记录

---

## [1.4.0] - 2026-05-03

### 🔧 重构：添加 AddonDiscovery 服务类

- ✅ 新增 AddonDiscovery 服务类，统一插件发现逻辑
- ✅ 提供 getAddonNames()、getAddonPath()、exists()、getConfig()、isInstalled() 等方法
- ✅ 重构 Addons.php、Loader.php、Router.php，使用 AddonDiscovery 替代重复的扫描逻辑
- ✅ 消除代码重复（减少约 40-50 行）
- ✅ 移除 Addons 类中未使用的 App 依赖

---

## [1.3.0] - 2026-05-03

### ✨ 新增插件生成器和 addons_path 助手函数

- ✅ 添加 Generator 类，支持自动生成插件
- ✅ 添加 addons_path() 助手函数，类似 root_path()
- ✅ 支持可选目录生成：model、validate、public
- ✅ 插件生成支持回滚机制（创建失败自动清理已创建的文件）
- ✅ 所有类统一使用 addons_path() 获取插件目录
- ✅ 完善 README.md，添加插件生成器使用说明

---

## [1.2.2] - 2026-05-02

### 🐛 修复

- ✅ **Config 类文件缺失问题修复**：
  - 完善 Config 类实现，添加 get()、set()、load()、getAll() 方法
  - 添加类型声明和注释
  - 新增默认配置文件 config.php

---

## [1.2.1] - 2026-05-02

### 🐛 修复

- ✅ **mergeConfigFrom() 不存在问题修复**：
  - 移除 Service.php 中不存在的 mergeConfigFrom() 方法调用

---

## [1.2.0] - 2026-05-02

### ✨ 新增配置支持、助手函数

- ✅ 添加 Config 类，提供配置管理功能
- ✅ 添加助手函数：addons()、addons_url()、addons_view()
- ✅ 增强 Service 类，支持配置加载和开关控制（auto_register、auto_load）

---

## [1.1.1] - 2026-05-02

### 🐛 修复

- ✅ **方法名冲突问题修复**：
  - 将 registerRoutes() 重命名为 registerPluginRoutes()，避免与父类冲突

---

## [1.1.0] - 2026-05-02

### ✨ 新增服务提供者、Facade

- ✅ 添加 ThinkPHP 服务提供者，自动加载和注册插件
- ✅ 添加 Addons 管理器类
- ✅ 添加 Facade 支持，更简洁的 API
- ✅ 增强插件信息扫描功能
- ✅ 更新 composer.json，配置 autoload、extra.think.services 等

---

## [1.0.1] - 2026-05-02

### 📝 完善文档和包信息

- ✅ 新增 LICENSE (Apache-2.0)
- ✅ 新增 README.md
- ✅ 新增 .gitignore
- ✅ 完善 composer.json 包信息
- ✅ 将 Route.php 重命名为 Router.php，避免命名冲突

---

## [1.0.0] - 2026-05-02

### ✨ 初始版本

- ✅ 从项目独立为 Composer 包
- ✅ BaseController - 插件基础控制器
- ✅ CommonController - 带登录验证的基础控制器
- ✅ PluginController - 插件管理控制器
- ✅ Loader - 插件加载器
- ✅ Router - 插件路由注册器
- ✅ 完整的插件系统功能