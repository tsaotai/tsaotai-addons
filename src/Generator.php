<?php
declare (strict_types=1);

namespace tsaotai\addons;

use think\App;

class Generator
{
    protected App $app;
    protected string $addonsPath;
    protected array $createdPaths = [];

    public function __construct(App $app)
    {
        $this->app = $app;
        $this->addonsPath = addons_path();
    }

    public function create(string $name, array $options = []): bool
    {
        $name = strtolower($name);
        
        if (!preg_match('/^[a-z][a-z0-9]*$/', $name)) {
            throw new \InvalidArgumentException('插件名称只能包含小写字母和数字，且必须以字母开头');
        }

        $pluginPath = $this->addonsPath . $name . DIRECTORY_SEPARATOR;
        
        if (is_dir($pluginPath)) {
            throw new \RuntimeException('插件已存在');
        }

        if (!is_writable($this->addonsPath)) {
            throw new \RuntimeException('插件目录不可写');
        }

        try {
            $this->createDirectoryStructure($pluginPath, $name, $options);
            $this->createPluginConfig($pluginPath, $name, $options);
            $this->createMainController($pluginPath, $name);
            $this->createPluginController($pluginPath, $name);
            $this->createRouteFile($pluginPath, $name);
            $this->createViewFiles($pluginPath, $name);
            $this->createReadme($pluginPath, $name, $options);
            $this->createGitignore($pluginPath);
            $this->createDataFiles($pluginPath);
            
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            throw $e;
        }
    }

    protected function rollback(): void
    {
        foreach (array_reverse($this->createdPaths) as $path) {
            if (is_file($path)) {
                unlink($path);
            } elseif (is_dir($path)) {
                rmdir($path);
            }
        }
        $this->createdPaths = [];
    }

    protected function createDirectoryStructure(string $path, string $name, array $options = []): void
    {
        $path = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $dirs = [
            $path,
            $path . 'controller',
            $path . 'view',
            $path . 'view' . DIRECTORY_SEPARATOR . $name,
            $path . 'view' . DIRECTORY_SEPARATOR . 'plugin',
            $path . 'data',
            $path . 'data' . DIRECTORY_SEPARATOR . 'plugin',
        ];

        if (!empty($options['with_model'])) {
            $dirs[] = $path . 'model';
        }

        if (!empty($options['with_validate'])) {
            $dirs[] = $path . 'validate';
        }

        if (!empty($options['with_public'])) {
            $dirs[] = $path . 'public';
            $dirs[] = $path . 'public' . DIRECTORY_SEPARATOR . 'css';
            $dirs[] = $path . 'public' . DIRECTORY_SEPARATOR . 'js';
            $dirs[] = $path . 'public' . DIRECTORY_SEPARATOR . 'images';
        }

        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
                $this->createdPaths[] = $dir;
            }
        }
    }

    protected function writeFile(string $path, string $content): void
    {
        file_put_contents($path, $content, LOCK_EX);
        $this->createdPaths[] = $path;
    }

    protected function createPluginConfig(string $path, string $name, array $options): void
    {
        $title = $options['title'] ?? ucfirst($name) . ' 插件';
        $description = $options['description'] ?? '这是一个自动生成的插件';
        $author = $options['author'] ?? '教员';
        $version = $options['plugin_version'] ?? '2026.1.1';
        $icon = $options['icon'] ?? 'tools';
        $category = $options['category'] ?? 'tool';
        $scope = $options['scope'] ?? 'admin';
        $classify = $options['classify'] ?? 'free';
        $date = date('Y-m-d');
        
        $content = <<<PHP
<?php
/**
 * 插件统一配置文件
 * 命名规范：纯独立英文原单词、无缩写、无拼接、无下划线
 * 编写规范：字段用途、取值范围、约束规则全部注释标注
 */
return [
    // 【核心唯一标识】
    // 约束：必须与插件目录文件夹名称完全一致，小写字母，不可重复、不可修改
    'identifier' => '{$name}',

    // 【插件展示名称】
    // 约束：后台列表、插件页头部正式显示名称，支持中文
    'title' => '{$title}',

    // 【插件完整描述】
    // 约束：详细说明插件作用、能力、使用场景，禁止过短，便于后期查阅
    'description' => '{$description}',

    // 【当前版本号】
    // 约束：语义化版本格式 主版本.次版本.修订号，例：1.0.0
    'version' => '{$version}',

    // 【开发作者】
    // 约束：填写开发负责人/团队名称，用于版权与溯源
    'author' => '{$author}',

    // 【创建时间】
    // 约束：固定格式 YYYY-MM-DD，版本迭代同步更新
    'create' => '{$date}',

    // 【最后更新日期】
    // 约束：固定格式 YYYY-MM-DD，版本迭代同步更新
    'update' => '{$date}',

    // 【图标标识】
    // 约束：BootstrapIcons 纯图标名，只写单体单词/短横线名，不带 bi- 前缀
    // 常用参考：puzzle、tools、gear、box、shield、database、terminal
    'icon' => '{$icon}',

    // 【功能分类】
    // 约束：固定取值 tool=工具类 / function=功能类 / business=业务类
    'category' => '{$category}',

    // 【排序权重】
    // 约束：纯数字，数值越大，后台插件列表展示越靠前
    'sort' => 0,

    // 【运行状态】
    // 约束：固定取值 enable=默认启用 / disable=默认禁用
    'state' => 'enable',

    // 【独立配置页】
    // 约束：布尔值 true=存在单独配置页面 / false=无额外配置
    'config' => false,

    // 【安装流程】
    // 约束：布尔值 true=需要执行安装逻辑（建表/初始化数据） / false=直接启用
    'install' => true,

    // 【卸载清理】
    // 约束：布尔值 true=卸载同步删除业务数据 / false=保留数据，防止误删丢失
    'clean' => false,

    // 【依赖插件】
    // 约束：多个依赖逗号分隔，无依赖留空；填写目标插件 identifier 标识
    'rely' => '',

    // 【后台访问入口】
    // 约束：插件独立管理页面路由地址，用于菜单点击跳转
    'entry' => 'addons/{$name}',

    // 【适用范围】
    // 约束：admin=仅后台使用 / index=仅前台使用 / all=全模块通用
    'scope' => '{$scope}',

    // 【插件品类】
    // 约束：free=免费版 / basic=基础版 / pro=专业付费版
    'classify' => '{$classify}',

    // 【文档地址】
    // 约束：填写文档/知识库链接，无文档留空
    'domain' => '',

    // 【开源协议】
    // 约束：标注版权协议，内部项目可自定义填写内部专用
    'licence' => 'Apache-2.0',

    // 【补充备注】
    // 约束：填写使用注意事项、特殊说明、限制条件，运营/维护查看
    'remark' => '标准模板插件，安装即可正常使用，卸载默认保留数据，避免误操作丢失内容。'
];
PHP;

        $this->writeFile($path . 'plugin.php', $content);
    }

    protected function createMainController(string $path, string $name): void
    {
        $className = ucfirst($name);
        $namespace = 'addons\\' . $name . '\\controller';
        $content = <<<PHP
<?php
declare (strict_types=1);

namespace {$namespace};

use tsaotai\\addons\\BaseController;

class {$className} extends BaseController
{
    // 插件首页
    public function index()
    {
        return \$this->fetch();
    }
}
PHP;

        $this->writeFile($path . 'controller' . DIRECTORY_SEPARATOR . $className . '.php', $content);
    }

    protected function createPluginController(string $path, string $name): void
    {
        $namespace = 'addons\\' . $name . '\\controller';
        $content = <<<PHP
<?php
declare (strict_types=1);

namespace {$namespace};

use tsaotai\\addons\\PluginController;

class Plugin extends PluginController
{
    
}
PHP;

        $this->writeFile($path . 'controller' . DIRECTORY_SEPARATOR . 'Plugin.php', $content);
    }

    protected function createRouteFile(string $path, string $name): void
    {
        $content = <<<PHP
<?php
use think\\facade\\Route;

// {$name} 插件自定义路由（可在此添加 {$name} 插件所需的额外路由）
// 插件标准 Plugin 路由已由 route/addons.php 统一自动注册，无需在此重复定义
PHP;

        $this->writeFile($path . 'route.php', $content);
    }

    protected function createViewFiles(string $path, string $name): void
    {
        // 主视图
        $content = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$name}</title>
</head>
<body>
    <div class="container">
        <h1>Hello, {$name}!</h1>
        <p>欢迎使用 TsaoTai 插件系统！</p>
    </div>
</body>
</html>
HTML;

        $this->writeFile($path . 'view' . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'index.html', $content);

        // 插件管理视图 - index
        $pluginIndexContent = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>插件管理</title>
</head>
<body>
    <div class="container">
        <h1>插件管理</h1>
        <p>在这里管理 {$name} 插件</p>
    </div>
</body>
</html>
HTML;

        $this->writeFile($path . 'view' . DIRECTORY_SEPARATOR . 'plugin' . DIRECTORY_SEPARATOR . 'index.html', $pluginIndexContent);

        // 插件管理视图 - rule
        $ruleContent = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>规则说明</title>
</head>
<body>
    <div class="container">
        <h1>规则说明</h1>
        <p>在这里查看 {$name} 插件的使用规则</p>
    </div>
</body>
</html>
HTML;

        $this->writeFile($path . 'view' . DIRECTORY_SEPARATOR . 'plugin' . DIRECTORY_SEPARATOR . 'rule.html', $ruleContent);

        // 插件管理视图 - update
        $updateContent = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>更新说明</title>
</head>
<body>
    <div class="container">
        <h1>更新说明</h1>
        <p>在这里查看 {$name} 插件的更新日志</p>
    </div>
</body>
</html>
HTML;

        $this->writeFile($path . 'view' . DIRECTORY_SEPARATOR . 'plugin' . DIRECTORY_SEPARATOR . 'update.html', $updateContent);
    }

    protected function createReadme(string $path, string $name, array $options): void
    {
        $title = $options['title'] ?? ucfirst($name) . ' 插件';
        $date = date('Y-m-d');
        
        $dirStructure = "{$name}/\n";
        $dirStructure .= "├── controller/       # 控制器\n";
        $dirStructure .= "│   ├── " . ucfirst($name) . ".php    # 主控制器\n";
        $dirStructure .= "│   └── Plugin.php   # 插件管理控制器\n";
        
        if (!empty($options['with_model'])) {
            $dirStructure .= "├── model/           # 模型\n";
        }
        
        if (!empty($options['with_validate'])) {
            $dirStructure .= "├── validate/        # 验证器\n";
        }
        
        $dirStructure .= "├── view/            # 视图\n";
        $dirStructure .= "│   ├── {$name}/      # 插件视图\n";
        $dirStructure .= "│   └── plugin/     # 管理视图\n";
        $dirStructure .= "├── data/            # 数据文件\n";
        
        if (!empty($options['with_public'])) {
            $dirStructure .= "├── public/          # 公共资源\n";
        }
        
        $dirStructure .= "├── route.php        # 插件路由\n";
        $dirStructure .= "├── plugin.php       # 插件配置\n";
        $dirStructure .= "└── README.md        # 说明文档\n";
        
        $content = <<<MD
# {$title}

这是一个由 TsaoTai 插件系统自动生成的插件。

## 安装

1. 将插件放置在 `addons/{$name}` 目录
2. 访问后台插件管理页面
3. 点击安装

## 使用

访问 `addons/{$name}` 即可使用本插件。

## 目录结构

```
{$dirStructure}```

## 许可证

Apache-2.0
MD;

        $this->writeFile($path . 'README.md', $content);
    }

    protected function createGitignore(string $path): void
    {
        $content = <<<GITIGNORE
# Data files
data/*.json
data/*.lock
data/*/

# Log files
*.log
logs/

# Temporary files
*.tmp
*.temp
.cache/

# IDE
.idea/
.vscode/
*.swp
*.swo
*~

# OS
.DS_Store
Thumbs.db
GITIGNORE;

        $this->writeFile($path . '.gitignore', $content);
    }

    protected function createDataFiles(string $path): void
    {
        $date = date('Y-m-d');
        
        $readmeContent = <<<MD
# 插件数据

存放插件数据文件。
MD;

        $this->writeFile($path . 'data' . DIRECTORY_SEPARATOR . 'plugin' . DIRECTORY_SEPARATOR . 'readme.md', $readmeContent);

        $updateContent = <<<MD
# 更新日志

## 2026.1.1 ({$date})

- 初始版本发布
- 基本功能实现
MD;

        $this->writeFile($path . 'data' . DIRECTORY_SEPARATOR . 'plugin' . DIRECTORY_SEPARATOR . 'update.md', $updateContent);

        $ruleContent = <<<MD
# 插件规则

本插件遵循 TsaoTai 插件开发规范。
MD;

        $this->writeFile($path . 'data' . DIRECTORY_SEPARATOR . 'plugin' . DIRECTORY_SEPARATOR . 'rule.md', $ruleContent);
    }
}
