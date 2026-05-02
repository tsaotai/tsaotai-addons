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
            $this->createDirectoryStructure($pluginPath, $options);
            $this->createPluginConfig($pluginPath, $name, $options);
            $this->createIndexController($pluginPath, $name);
            $this->createPluginController($pluginPath, $name);
            $this->createViewFile($pluginPath, $name);
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

    protected function createDirectoryStructure(string $path, array $options = []): void
    {
        $dirs = [
            $path,
            $path . 'controller',
            $path . 'view',
            $path . 'view' . DIRECTORY_SEPARATOR . 'index',
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
        $config = [
            'identifier'    => $name,
            'title'         => $options['title'] ?? ucfirst($name) . ' 插件',
            'description'   => $options['description'] ?? '这是一个自动生成的插件',
            'version'       => $options['version'] ?? '1.0.0',
            'author'        => $options['author'] ?? '',
            'create'        => date('Y-m-d'),
            'update'        => date('Y-m-d'),
            'icon'          => $options['icon'] ?? 'puzzle',
            'category'      => $options['category'] ?? 'tool',
            'sort'          => 0,
            'state'         => 'enable',
            'config'        => false,
            'install'       => true,
            'clean'         => false,
            'rely'          => '',
            'entry'         => 'addons/' . $name,
            'scope'         => $options['scope'] ?? 'admin',
            'classify'      => $options['classify'] ?? 'free',
            'domain'        => '',
            'license'       => 'Apache-2.0',
            'remark'        => ''
        ];

        $content = "<?php\nreturn " . var_export($config, true) . ";\n";
        $this->writeFile($path . 'plugin.php', $content);
    }

    protected function createIndexController(string $path, string $name): void
    {
        $namespace = 'addons\\' . $name . '\\controller';
        $content = <<<PHP
<?php
declare (strict_types=1);

namespace {$namespace};

use tsaotai\\addons\\BaseController;

class Index extends BaseController
{
    public function index()
    {
        \$this->assign('name', '{$name}');
        return \$this->fetch();
    }
    
    public function hello(\$name = 'World')
    {
        return json(['code' => 1, 'msg' => 'Hello ' . \$name]);
    }
}
PHP;
        $this->writeFile($path . 'controller' . DIRECTORY_SEPARATOR . 'Index.php', $content);
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

    protected function createViewFile(string $path, string $name): void
    {
        $content = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$name}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: white;
            padding: 3rem;
            border-radius: 1rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            text-align: center;
        }
        h1 {
            color: #333;
            margin-bottom: 1rem;
        }
        p {
            color: #666;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Hello, {\$name}!</h1>
        <p>欢迎使用 TsaoTai 插件系统！</p>
    </div>
</body>
</html>
HTML;
        $this->writeFile($path . 'view' . DIRECTORY_SEPARATOR . 'index' . DIRECTORY_SEPARATOR . 'index.html', $content);
    }

    protected function createReadme(string $path, string $name, array $options): void
    {
        $title = $options['title'] ?? ucfirst($name) . ' 插件';
        $date = date('Y-m-d');
        
        $dirStructure = "{$name}/\n";
        $dirStructure .= "├── controller/       # 控制器\n";
        $dirStructure .= "│   ├── Index.php    # 前台控制器\n";
        $dirStructure .= "│   └── Plugin.php   # 插件管理控制器\n";
        
        if (!empty($options['with_model'])) {
            $dirStructure .= "├── model/           # 模型\n";
        }
        
        if (!empty($options['with_validate'])) {
            $dirStructure .= "├── validate/        # 验证器\n";
        }
        
        $dirStructure .= "├── view/            # 视图\n";
        $dirStructure .= "├── data/            # 数据文件\n";
        
        if (!empty($options['with_public'])) {
            $dirStructure .= "├── public/          # 公共资源\n";
        }
        
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
        $updateContent = <<<MD
# 更新日志

## v1.0.0 ({$date})

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
