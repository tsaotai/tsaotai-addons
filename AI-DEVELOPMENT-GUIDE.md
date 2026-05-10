# AI 插件开发指南

> 🤖 本指南专门为使用 AI（如 Claude、GPT 等）辅助开发 TsaoTai 插件而设计

---

## 目录

- [快速开始](#快速开始)
- [目录结构](#目录结构)
- [创建第一个 AI 插件](#创建第一个-ai-插件)
- [常见开发模式](#常见开发模式)
- [AI 提示词模板](#ai-提示词模板)
- [最佳实践](#最佳实践)
- [问题排查](#问题排查)

---

## 快速开始

### 告诉 AI 你在做什么

开始开发前，先给 AI 提供上下文：

```
我正在使用 TsaoTai Addons (ThinkPHP 8 插件系统) 开发一个插件。

项目信息：
- 版本：2026.1.4
- 插件目录：addons/
- 主控制器继承：插件自己的 Base（继承 addons\common\AuthBase）
- 管理控制器继承：tsaotai\addons\PluginController

请帮我开发一个 [插件功能描述] 的插件。
```

### 让 AI 生成完整插件

使用这个提示词让 AI 一次性创建完整插件：

```
请帮我创建一个 TsaoTai 插件，要求：

1. 插件名称：myplugin
2. 功能：[描述你的功能]
3. 包含控制器、视图、配置

请提供完整的文件结构和代码。
```

---

## 命令行快速参考

### 完整选项列表

```bash
php think addon:make [插件名] [选项]
```

### 基本信息选项

| 选项 | 说明 |
|------|------|
| `--title` | 插件标题 |
| `--description` | 插件描述 |
| `--author` | 插件作者 |
| `--plugin-version` | 插件版本号 |

### 可选功能选项

| 选项 | 说明 | AI 使用场景 |
|------|------|-------------|
| `--with-base` | 创建插件 Base 控制器（默认启用） | ✅ 总是使用 |
| `--with-model` | 创建 model 目录 | 需要数据库操作时 |
| `--with-validate` | 创建 validate 目录 | 需要表单验证时 |
| `--with-public` | 创建 public 资源目录 | 需要 CSS/JS/图片时 |
| `--with-config` | 创建 config.php | 需要插件配置时 |
| `--with-common` | 创建 common.php | 需要公共函数时 |
| `--with-service` | 创建 service.php | 需要服务定义时 |
| `--with-provider` | 创建 provider.php | 需要服务容器绑定时 |
| `--with-event` | 创建 event.php | 需要事件监听时 |
| `--with-middleware` | 创建 middleware.php | 需要中间件时 |

### 常见组合

```bash
# 简单展示插件
php think addon:make myplugin --title="我的插件"

# 管理型插件（带模型和验证）
php think addon:make adminplugin --title="管理插件" --with-model --with-validate

# 完整功能插件
php think addon:make fullplugin --title="完整插件" --with-model --with-validate --with-public --with-config --with-common
```

---

## 目录结构

### 标准插件结构

```
addons/
└── myplugin/
    ├── controller/
    │   ├── Myplugin.php       # 主控制器（必选）
    │   └── Plugin.php         # 管理控制器（必选）
    ├── view/
    │   ├── myplugin/          # 前台视图
    │   │   └── index.html
    │   └── plugin/            # 管理视图
    │       └── index.html
    ├── data/
    │   └── plugin/
    │       ├── readme.md
    │       ├── rule.md
    │       └── update.md
    ├── plugin.php             # 插件配置（必选）
    └── README.md
```

### 可选目录

```
addons/myplugin/
├── model/                   # 模型
├── validate/                # 验证器
├── public/                  # 静态资源
│   ├── css/
│   ├── js/
│   └── images/
├── common.php               # 公共函数
└── route.php                # 自定义路由
```

---

## 创建第一个 AI 插件

### 步骤 1：使用命令行创建基础结构

```bash
php think addon:make aihelper --title="AI 助手插件" --description="使用 AI 辅助开发的示例插件" --author="开发者" --plugin-version="2026.1.1"
```

### 步骤 2：让 AI 帮你写代码

把这个需求给 AI：

```
我已经创建了一个名为 "aihelper" 的 TsaoTai 插件，目录在 addons/aihelper/。

请帮我实现以下功能：
1. 主控制器有一个 index 方法，显示欢迎页面
2. 页面显示当前时间
3. 有一个表单可以提交消息
4. 提交后显示消息

请提供需要修改/创建的文件的完整代码。
```

### 步骤 3：AI 可能会生成的代码

AI 会生成类似这样的代码：

**主控制器 (controller/Aihelper.php)：**

```php
<?php
declare (strict_types=1);

namespace addons\aihelper\controller;

use tsaotai\addons\BaseController;

class Aihelper extends BaseController
{
    public function index()
    {
        $this->assign('time', date('Y-m-d H:i:s'));
        $this->assign('message', $this->request->param('message', ''));
        return $this->fetch();
    }
}
```

**视图 (view/aihelper/index.html)：**

```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>AI 助手</title>
</head>
<body>
    <h1>🤖 AI 助手插件</h1>
    <p>当前时间：{$time}</p>
    
    <form method="get">
        <input type="text" name="message" placeholder="输入消息...">
        <button type="submit">发送</button>
    </form>
    
    {if $message}
    <p>你说：{$message}</p>
    {/if}
</body>
</html>
```

---

## 常见开发模式

### 模式 1：纯展示型插件

**需求：** 显示一些信息

**AI 提示词：**
```
帮我创建一个展示型插件，显示 [数据内容]。
需要：
- 从数据库/API 获取数据
- 在视图中展示
- 有简单的样式
```

**代码示例：**

```php
// 控制器
public function index()
{
    $data = Db::name('table')->select();
    $this->assign('data', $data);
    return $this->fetch();
}
```

### 模式 2：表单处理插件

**需求：** 提交表单，处理数据

**AI 提示词：**
```
帮我创建一个表单插件，功能包括：
1. 显示表单
2. 验证输入
3. 保存到数据库
4. 显示成功/错误提示
```

**代码示例：**

```php
public function index()
{
    if ($this->request->isPost()) {
        $data = $this->request->post();
        // 验证和保存
        $this->success('保存成功');
    }
    return $this->fetch();
}
```

### 模式 3：API 接口插件

**需求：** 提供 JSON API

**AI 提示词：**
```
帮我创建一个 API 插件，提供以下接口：
- GET  /api/list    - 获取列表
- POST /api/save    - 保存数据
- GET  /api/info/:id - 获取详情
```

**代码示例：**

```php
public function list()
{
    $data = Db::name('table')->select();
    return json(['code' => 1, 'data' => $data]);
}
```

### 模式 4：管理型插件

**需求：** 后台管理功能

**AI 提示词：**
```
帮我创建一个管理插件，需要：
1. 管理控制器继承 PluginController
2. 有列表、添加、编辑、删除功能
3. 使用数据库表
```

**代码示例：**

```php
class Plugin extends PluginController
{
    public function index()
    {
        $list = Db::name('table')->paginate();
        $this->assign('list', $list);
        return $this->fetch();
    }
}
```

---

## AI 提示词模板

### 插件开发模板

```
我正在开发 TsaoTai 插件，请帮我：

【插件信息】
- 名称：[插件名]
- 目录：addons/[插件名]/
- 功能描述：[详细描述]

【技术要求】
- ThinkPHP 8
- 继承 tsaotai\addons\BaseController
- 遵循 Psr-4 规范

【需要实现】
1. [功能点 1]
2. [功能点 2]
3. [功能点 3]

请提供完整的代码实现，包括：
- 控制器代码
- 视图代码（如果需要）
- 数据库表结构（如果需要）
- 插件配置（plugin.php）
```

### 修改现有插件模板

```
我有一个现有的 TsaoTai 插件在 addons/[插件名]/，请帮我修改：

【当前问题/需求】
[描述需要修改的地方]

【相关文件】
- controller/[控制器名].php
- view/[目录]/[文件].html

请提供修改后的完整代码。
```

### 调试问题模板

```
我的 TsaoTai 插件遇到问题，帮我排查：

【问题描述】
[详细描述问题]

【错误信息】
[粘贴错误信息]

【相关代码】
```php
[粘贴相关代码]
```

【环境信息】
- ThinkPHP 版本：
- TsaoTai Addons 版本：2026.1.1
- PHP 版本：

请帮我找出问题并提供解决方案。
```

---

## 最佳实践

### ✅ 应该做的

1. **先创建基础结构再让 AI 填充**
   ```bash
   php think addon:make myplugin
   ```
   然后让 AI 在这个基础上开发

2. **提供完整上下文**
   告诉 AI：
   - 使用的框架版本
   - 继承的基类
   - 插件目录结构

3. **让 AI 一次只做一件事**
   ```
   第一步：先创建控制器
   第二步：再创建视图
   第三步：最后调整样式
   ```

4. **使用英文变量名，中文注释**
   ```php
   // 获取用户列表
   $userList = Db::name('user')->select();
   ```

### ❌ 不应该做的

1. **不要让 AI 猜测目录结构**
   明确告诉 AI：`控制器在 addons/myplugin/controller/`

2. **不要省略继承关系**
   必须明确：`继承 tsaotai\addons\BaseController`

3. **不要一次性要求太多功能**
   分步骤实现，每步验证

4. **不要忘记更新 plugin.php**
   修改插件信息后记得更新配置

---

## 问题排查

### 常见问题 1：找不到类

**错误：** `Class 'addons\myplugin\controller\Myplugin' not found`

**排查：**
1. 检查文件名是否正确：`Myplugin.php`
2. 检查命名空间：`namespace addons\myplugin\controller;`
3. 检查类名：`class Myplugin extends BaseController`

### 常见问题 2：视图找不到

**错误：** `template not exists`

**排查：**
1. 视图文件位置：`addons/myplugin/view/myplugin/index.html`
2. 控制器中调用：`$this->fetch()`

### 常见问题 3：路由不工作

**检查：**
1. 插件 `state` 是否为 `enable`
2. 访问 URL：`/addons/myplugin/index`

### 常见问题 4：旧插件不兼容

**如果遇到 CommonController 问题：**

旧插件代码继续使用，无需修改：
```php
// 旧插件继续有效
use addons\common\CommonController;
class MyPlugin extends CommonController {}
```

新插件推荐使用：
```php
use tsaotai\addons\BaseController;
class MyPlugin extends BaseController {}
```

---

## 完整 AI 开发示例

### 让我们创建一个「待办事项」插件

#### 1. 创建基础结构

```bash
php think addon:make todo --title="待办事项" --description="简单的待办事项管理插件" --author="AI" --plugin-version="2026.1.1"
```

#### 2. 给 AI 发需求

```
我创建了一个 todo 插件，现在请帮我完善它：

需求：
1. 创建数据库表：todo (id, title, status, create_time)
2. 主控制器实现：列表、添加、完成、删除
3. 视图显示待办列表，有添加表单
4. 使用 AJAX 交互

请提供：
1. 数据库 SQL
2. 完整的控制器代码
3. 完整的视图代码
```

#### 3. AI 生成的代码

**SQL：**
```sql
CREATE TABLE `todo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `status` tinyint(1) DEFAULT 0,
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
);
```

**控制器 (controller/Todo.php)：**
```php
<?php
declare (strict_types=1);

namespace addons\todo\controller;

use tsaotai\addons\BaseController;
use think\facade\Db;

class Todo extends BaseController
{
    public function index()
    {
        $list = Db::name('todo')->order('id desc')->select();
        $this->assign('list', $list);
        return $this->fetch();
    }
    
    public function add()
    {
        $title = $this->request->post('title');
        Db::name('todo')->insert([
            'title' => $title,
            'status' => 0,
            'create_time' => time()
        ]);
        return json(['code' => 1, 'msg' => '添加成功']);
    }
    
    public function complete()
    {
        $id = $this->request->post('id');
        Db::name('todo')->where('id', $id)->update(['status' => 1]);
        return json(['code' => 1, 'msg' => '已完成']);
    }
    
    public function delete()
    {
        $id = $this->request->post('id');
        Db::name('todo')->where('id', $id)->delete();
        return json(['code' => 1, 'msg' => '已删除']);
    }
}
```

**视图 (view/todo/index.html)：**
```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>待办事项</title>
    <style>
        .done { text-decoration: line-through; color: #999; }
    </style>
</head>
<body>
    <h1>📋 待办事项</h1>
    
    <form id="addForm">
        <input type="text" id="title" placeholder="添加新待办...">
        <button type="submit">添加</button>
    </form>
    
    <ul id="todoList">
        {foreach $list as $item}
        <li data-id="{$item.id}">
            <span class="{$item.status ? 'done' : ''}">{$item.title}</span>
            <button onclick="complete({$item.id})">完成</button>
            <button onclick="deleteItem({$item.id})">删除</button>
        </li>
        {/foreach}
    </ul>
    
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script>
        $('#addForm').submit(function(e) {
            e.preventDefault();
            $.post('/addons/todo/add', {title: $('#title').val()}, function() {
                location.reload();
            });
        });
        
        function complete(id) {
            $.post('/addons/todo/complete', {id: id}, function() {
                location.reload();
            });
        }
        
        function deleteItem(id) {
            $.post('/addons/todo/delete', {id: id}, function() {
                location.reload();
            });
        }
    </script>
</body>
</html>
```

---

## 总结

### AI 开发工作流

1. **创建骨架** → `php think addon:make`
2. **描述需求** → 用清晰的自然语言告诉 AI
3. **AI 生成** → 复制粘贴 AI 生成的代码
4. **测试验证** → 浏览器测试功能
5. **迭代调整** → 有问题继续问 AI

### 关键要点

- ✅ 提供准确的上下文和目录结构
- ✅ 分步骤开发，一次一个功能
- ✅ 使用英文变量，中文注释
- ✅ 验证后再继续下一步
- ✅ 旧插件继续兼容 CommonController

---

祝你使用 AI 开发愉快！🎉
