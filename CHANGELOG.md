# Changelog

所有重要的变更都会记录在此文件中。

---

## [1.2.0] - 2026-05-02

### ✨ 新功能

- 添加 Config 类，支持包配置
- 添加默认配置文件 `config.php`
- 添加助手函数文件 `helper.php`
- 增强 Service 类，支持配置加载和开关控制

### 🔧 优化

- Service 支持 `auto_register` 和 `auto_load` 配置
- Config 支持 `get()`、`set()`、`load()` 方法
- 添加助手函数：`addons()`、`addons_url()`、`addons_view()`

---

## [1.1.1] - 2026-05-02

### 🐛 修复

- 修复 Service 类 `registerRoutes` 方法名与父类冲突问题

---

## [1.1.0] - 2026-05-02

### ✨ 新功能

- 添加 ThinkPHP 服务提供者，自动加载和注册插件
- 添加 Addons 管理器类
- 添加 Facade 支持，更简洁的 API
- 增强插件信息扫描功能

### 📝 文档

- 更新 README.md，添加服务提供者使用说明
- 添加 CHANGELOG.md

---

## [1.0.0] - 2026-05-02

### ✨ 新功能

- 从项目独立为 Composer 包
- BaseController - 插件基础控制器
- CommonController - 带登录验证的基础控制器
- PluginController - 插件管理控制器
- Loader - 插件加载器
- Router - 插件路由注册器

### 📝 文档

- 添加 README.md
- 添加 LICENSE (Apache-2.0)
