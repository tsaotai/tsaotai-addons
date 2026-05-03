# Changelog

所有重要的变更都会记录在此文件中。

---

## [1.6.1] - 2026-05-03 (0727953)

### fix: 修复 createDirectoryStructure() 方法缺少 $name 参数的 bug

- **修复 Generator.php**
  - 在 `createDirectoryStructure()` 方法签名中添加 `$name` 参数
  - 更新 `create()` 方法中的调用，传入 `$name`
- 共修改 1 个文件，新增 2 行，删除 2 行

---

## [1.6.0] - 2026-05-03 (e0bdb97)

### 优化插件生成器，添加命令别名

- **重构 Generator.php** - 完全重写插件生成器，使其生成的结构与 demo 一致
  - 主控制器类名现在与插件名一致（如 Demo 插件生成 Demo 控制器，而非 Index 控制器）
  - 使用 CommonController 替代 BaseController
  - 视图目录结构调整为 view/demo/ 和 view/plugin/
  - 补充完整的视图文件：view/plugin/index.html、view/plugin/rule.html、view/plugin/update.html
  - 新增 data/ 目录，包含 data/plugin/readme.md、data/plugin/update.md、data/plugin/rule.md
  - 自动生成插件的 README.md 和 .gitignore
  - 修复 plugin.php 配置文件中 $date 变量未定义的问题
- **优化 MakeAddon.php**
  - 添加命令别名 `addon:make` 和 `plugin:make`，现在可以用三个命令创建插件
  - 优化执行输出，成功后显示插件路径和使用提示
  - 保持原有的 `make:addon` 命令可用
- **完善 README.md** - 大幅更新文档
  - 添加目录导航
  - 新增完整的「开发插件指南」章节
  - 更新命令使用示例
  - 完善升级指南
- **更新 CHANGELOG.md** - 添加 v1.6.0 版本记录
- 共修改 4 个文件，新增 518 行，删除 212 行

---

## [1.5.1] - 2026-05-03 (2bad84b)

### docs: update CHANGELOG for v1.5.1

- 更新 CHANGELOG.md，添加 v1.5.1 版本记录
- 共修改 1 个文件，新增 8 行

---

## [1.5.1] - 2026-05-03 (434c517)

### fix: rename version option to plugin-version to avoid conflict

- **修复命令选项冲突**
  - 将 MakeAddon.php 中的 `--version` 选项重命名为 `--plugin-version`
  - 避免与 ThinkPHP 自带的 `--version` 选项冲突
  - 更新 README.md 中的命令使用示例
- 共修改 2 个文件，新增 4 行，删除 4 行

---

## [1.5.0] - 2026-05-03 (48502b7)

### feat: add make:addon console command

- **新增命令行工具**
  - 创建 src/command/MakeAddon.php，提供 `make:addon` 命令
  - 支持通过命令行快速创建插件
  - 支持可选目录通过参数指定：--with-model、--with-validate、--with-public
  - 支持传入插件信息：--title、--description、--author、--version
- **更新 Service.php** - 注册 MakeAddon 命令
- **更新文档** - 更新 README.md 和 CHANGELOG.md
- 共修改 4 个文件，新增 115 行，删除 1 行

---

## [1.4.1] - 2026-05-03 (90f4894)

### fix: fix path handling and type safety issues

- **修复 AddonDiscovery.php**
  - 在 getAddonPath() 方法中添加 rtrim() 处理，确保路径格式一致
  - 在 getConfig() 方法中添加类型安全检查，当配置不是数组时返回空数组
- **修复 Generator.php**
  - 在 writeFile() 方法后添加缺失的 createdPaths 记录
- 共修改 2 个文件，新增 4 行，删除 2 行

---

## [1.4.0] - 2026-05-03 (1aef7fe, b1ac05f, 8d7980a, 355f614)

### 1aef7fe - docs: clean up duplicate separators in CHANGELOG

- 清理 CHANGELOG.md 中重复的分隔线
- 共修改 1 个文件，删除 4 行

---

### b1ac05f - docs: update CHANGELOG for v1.4.0 release

- 更新 CHANGELOG.md，添加 v1.4.0 版本记录
- 共修改 1 个文件，新增 3 行

---

### 8d7980a - refactor: remove unused App dependency from Addons class

- **重构 Addons.php**
  - 移除未使用的 App 依赖
  - 简化类构造函数
- **更新 Service.php** - 相应调整依赖注入
- 共修改 2 个文件，新增 2 行，删除 6 行

---

### 355f614 - refactor: add AddonDiscovery service to unify plugin discovery

- **新增 AddonDiscovery 服务类**
  - 创建 src/AddonDiscovery.php，统一插件发现逻辑
  - 提供 getAddonNames()、getAddonPath()、exists()、getConfig()、isInstalled() 等方法
  - 消除代码重复
- **重构 Addons.php** - 使用 AddonDiscovery 替代重复的扫描逻辑
- **重构 Loader.php** - 使用 AddonDiscovery 替代重复的扫描逻辑
- **重构 Router.php** - 使用 AddonDiscovery 替代重复的扫描逻辑
- **更新文档** - 更新 CHANGELOG.md 和 README.md
- 共修改 6 个文件，新增 145 行，删除 56 行

---

## [1.3.0] - 2026-05-03 (689be60, f97b6a9)

### 689be60 - docs: update README and CHANGELOG for v1.3.0

- 更新 README.md，添加插件生成器使用说明
- 更新 CHANGELOG.md，添加 v1.3.0 版本记录
- 共修改 2 个文件，新增 60 行

---

### f97b6a9 - feat: add addons_path helper and plugin generator

- **新增插件生成器**
  - 创建 src/Generator.php，提供 create() 方法
  - 支持可选目录生成：model、validate、public
  - 支持回滚机制（创建失败自动清理已创建的文件）
  - 生成 plugin.php、controller/Index.php、controller/Plugin.php、view/index/index.html
- **新增 addons_path() 助手函数** - 在 src/helper.php 中添加，类似 root_path()
- **重构 Addons.php** - 添加 create() 方法，统一使用 addons_path()
- **重构 Loader.php** - 统一使用 addons_path()
- **重构 Router.php** - 统一使用 addons_path()
- **重构 Service.php** - 相应调整
- **更新 Facade** - 添加 create() 方法
- 共修改 7 个文件，新增 380 行，删除 15 行

---

## [1.2.2] - 2026-05-02 (cd20978)

### v1.2.2: 修复 Config 类文件缺失问题

- **修复 Config.php**
  - 完善 Config 类实现，添加 get()、set()、load()、getAll() 方法
  - 添加类型声明和注释
- **新增默认配置** - 创建根目录 config.php 文件
- 共修改 2 个文件，新增 44 行，删除 8 行

---

## [1.2.1] - 2026-05-02 (d7c531a)

### v1.2.1: 修复 mergeConfigFrom() 不存在的问题

- **修复 Service.php** - 移除不存在的 mergeConfigFrom() 方法调用
- **更新文档** - 更新 CHANGELOG.md 和 README.md
- 共修改 3 个文件，新增 22 行，删除 4 行

---

## [1.2.0] - 2026-05-02 (7dede7b)

### v1.2.0: 添加配置支持、助手函数、增强功能

- **新增 Config 类** - 创建 src/Config.php，提供配置管理功能
- **新增助手函数** - 创建 src/helper.php，包含 addons()、addons_url()、addons_view()
- **增强 Service 类** - 支持配置加载和开关控制（auto_register、auto_load）
- **更新 composer.json** - 配置自动加载
- **更新文档** - 更新 CHANGELOG.md 和 README.md
- 共修改 6 个文件，新增 169 行，删除 4 行

---

## [1.1.1] - 2026-05-02 (2a60b5f)

### fix: 修复 registerRoutes 方法名与父类冲突问题

- **修复 Service.php** - 将 registerRoutes() 重命名为 registerPluginRoutes()
- 共修改 1 个文件，新增 2 行，删除 2 行

---

## [1.1.0] - 2026-05-02 (1b9c9db)

### v1.1.0: 添加服务提供者、Facade、增强功能

- **新增 Service 类** - 创建 ThinkPHP 服务提供者，自动加载和注册插件
- **新增 Addons 类** - 创建插件管理器
- **新增 Facade** - 创建 src/facade/Addons.php
- **增强功能** - 增强插件信息扫描
- **更新文档** - 更新 CHANGELOG.md 和 README.md
- **更新 composer.json** - 配置 autoload、extra.think.services 等
- 共修改 6 个文件，新增 248 行，删除 13 行

---

## [1.0.1] - 2026-05-02 (1c4df4f)

### 完善文档和包信息

- **新增 LICENSE** - 添加 Apache-2.0 许可证
- **新增 README.md** - 添加完整文档
- **新增 .gitignore** - 添加 Git 忽略文件
- **更新 composer.json** - 完善包信息
- 共修改 4 个文件，新增 365 行，删除 3 行

---

## [1.0.1] - 2026-05-02 (ba9e992)

### Fix: Rename Route to Router to avoid naming conflict

- **重命名文件** - 将 Route.php 重命名为 Router.php，避免与 ThinkPHP 类名冲突
- 共修改 1 个文件，新增 1 行，删除 1 行

---

## [1.0.0] - 2026-05-02 (4c52ece)

### Initial commit

- 从项目独立为 Composer 包
- **新增 BaseController.php** - 插件基础控制器
- **新增 CommonController.php** - 带登录验证的基础控制器
- **新增 PluginController.php** - 插件管理控制器
- **新增 Loader.php** - 插件加载器
- **新增 Route.php** - 插件路由注册器
- **新增 composer.json** - 包配置文件
- 共修改 6 个文件，新增 406 行
