# Changelog

所有重要的变更都会记录在此文件中。

---

## [1.7.5] - 2026-05-03

### ✨ 完善和优化

- 统一 CHANGELOG 格式和样式
- 优化插件生成器细节
- 更新文档说明

---

## [1.7.0] - 2026-05-03

### ✨ 优化插件生成器

- 新增 route.php 文件生成
- 优化视图文件，样式与 demo 保持一致
- 优化 data/plugin/update.md 格式
- 保持 rule.md 原样
- 不依赖外部 demo 目录，代码更简洁
- 新增完整的插件视图模板
- 更新 README 文档说明

---

## [1.6.1] - 2026-05-03

### 🐛 修复 createDirectoryStructure() 方法缺少 $name 参数的 bug

- 在 `createDirectoryStructure()` 方法签名中添加 `$name` 参数
- 更新 `create()` 方法中的调用，传入 `$name`

---

## [1.6.0] - 2026-05-03

### ✨ 优化插件生成器，添加命令别名

- 完全重写插件生成器，使其生成的结构与 demo 一致
- 主控制器类名现在与插件名一致（如 Demo 插件生成 Demo 控制器，而非 Index 控制器）
- 使用 CommonController 替代 BaseController
- 视图目录结构调整为 view/demo/ 和 view/plugin/
- 补充完整的视图文件：view/plugin/index.html、view/plugin/rule.html、view/plugin/update.html
- 新增 data/ 目录，包含 data/plugin/readme.md、data/plugin/update.md、data/plugin/rule.md
- 自动生成插件的 README.md 和 .gitignore
- 修复 plugin.php 配置文件中 $date 变量未定义的问题
- 命令添加别名，现在可以用 `addon:make` 或 `plugin:make` 快速创建插件

---

## [1.5.1] - 2026-05-03

### 🐛 修复命令选项名冲突

- 将 MakeAddon.php 中的 `--version` 选项重命名为 `--plugin-version`
- 避免与 ThinkPHP 自带的 `--version` 选项冲突
- 更新 README.md 中的命令使用示例

---

## [1.5.0] - 2026-05-03

### ✨ 新增命令行工具

- 添加 `make:addon` 命令行工具
- 支持通过命令行快速创建插件
- 支持可选目录通过参数指定：--with-model、--with-validate、--with-public
- 支持传入插件信息：--title、--description、--author、--plugin-version
- 更新 Service.php，注册 MakeAddon 命令

---

## [1.4.1] - 2026-05-03

### 🐛 修复路径处理和类型安全问题

- 修复 AddonDiscovery.php，在 getAddonPath() 方法中添加 rtrim() 处理
- 在 getConfig() 方法中添加类型安全检查，当配置不是数组时返回空数组
- 修复 Generator.php，在 writeFile() 方法后添加缺失的 createdPaths 记录

---

## [1.4.0] - 2026-05-03

### 🔧 重构：添加 AddonDiscovery 服务类

- 新增 AddonDiscovery 服务类，统一插件发现逻辑
- 提供 getAddonNames()、getAddonPath()、exists()、getConfig()、isInstalled() 等方法
- 重构 Addons.php、Loader.php、Router.php，使用 AddonDiscovery 替代重复的扫描逻辑
- 消除代码重复（减少约 40-50 行）
- 移除 Addons 类中未使用的 App 依赖

---

## [1.3.0] - 2026-05-03

### ✨ 新增插件生成器和 addons_path 助手函数

- 添加 Generator 类，支持自动生成插件
- 添加 addons_path() 助手函数，类似 root_path()
- 支持可选目录生成：model、validate、public
- 插件生成支持回滚机制（创建失败自动清理已创建的文件）
- 所有类统一使用 addons_path() 获取插件目录
- 完善 README.md，添加插件生成器使用说明

---

## [1.2.2] - 2026-05-02

### 🐛 修复 Config 类文件缺失问题

- 完善 Config 类实现，添加 get()、set()、load()、getAll() 方法
- 添加类型声明和注释
- 新增默认配置文件 config.php

---

## [1.2.1] - 2026-05-02

### 🐛 修复 mergeConfigFrom() 不存在的问题

- 移除 Service.php 中不存在的 mergeConfigFrom() 方法调用

---

## [1.2.0] - 2026-05-02

### ✨ 新增配置支持、助手函数

- 添加 Config 类，提供配置管理功能
- 添加助手函数：addons()、addons_url()、addons_view()
- 增强 Service 类，支持配置加载和开关控制（auto_register、auto_load）

---

## [1.1.1] - 2026-05-02

### 🐛 修复方法名冲突问题

- 将 registerRoutes() 重命名为 registerPluginRoutes()，避免与父类冲突

---

## [1.1.0] - 2026-05-02

### ✨ 新增服务提供者、Facade

- 添加 ThinkPHP 服务提供者，自动加载和注册插件
- 添加 Addons 管理器类
- 添加 Facade 支持，更简洁的 API
- 增强插件信息扫描功能
- 更新 composer.json，配置 autoload、extra.think.services 等

---

## [1.0.1] - 2026-05-02

### 📝 完善文档和包信息

- 新增 LICENSE (Apache-2.0)
- 新增 README.md
- 新增 .gitignore
- 完善 composer.json 包信息
- 将 Route.php 重命名为 Router.php，避免命名冲突

---

## [1.0.0] - 2026-05-02

### ✨ 初始版本

- 从项目独立为 Composer 包
- BaseController - 插件基础控制器
- CommonController - 带登录验证的基础控制器
- PluginController - 插件管理控制器
- Loader - 插件加载器
- Router - 插件路由注册器
- 完整的插件系统架构

