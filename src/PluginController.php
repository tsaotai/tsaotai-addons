<?php
declare (strict_types=1);

namespace tsaotai\addons;

/**
 * 插件管理页基础控制器
 * 所有插件的 Plugin.php 继承此类，统一提供：
 * - 插件信息加载
 * - 安装/卸载逻辑
 * - update/rule markdown 渲染
 */
abstract class PluginController extends CommonController
{
    protected string $addonName;
    protected array $addonInfo;
    protected string $lockFile;
    protected string $pluginDir;

    protected function initialize(): void
    {
        parent::initialize();

        // 通过反射自动获取当前插件目录（兼容所有插件）
        $ref = new \ReflectionClass($this);
        $this->pluginDir = dirname(dirname($ref->getFileName()));
        $this->addonName = basename($this->pluginDir);

        $configFile = $this->pluginDir . '/plugin.php';
        $this->addonInfo = is_file($configFile) ? include $configFile : [
            'title'       => '未知插件',
            'version'     => '1.0.0',
            'author'      => '未知作者',
            'update'      => date('Y-m-d'),
            'description' => '暂无插件描述',
            'icon'        => 'puzzle',
            'identifier'  => $this->addonName
        ];

        $this->lockFile = $this->pluginDir . '/install.lock';
    }

    protected function getMarkdownContent(string $file): string
    {
        return is_file($file) ? file_get_contents($file) : '暂无内容';
    }

    // 插件首页
    public function index(): string
    {
        $baseUrl = "/plugin/{$this->addonName}";
        $this->assign([
            'addon'         => $this->addonInfo,
            'installed'     => is_file($this->lockFile),
            'install_url'   => "/plugin/{$this->addonName}/install",
            'uninstall_url' => "/plugin/{$this->addonName}/uninstall",
            'index_url'     => $baseUrl,
            'update_url'    => $baseUrl . '/update',
            'rule_url'      => $baseUrl . '/rule',
        ]);
        return $this->fetch();
    }

    // 更新日志
    public function update(): string
    {
        $baseUrl    = "/plugin/{$this->addonName}";
        $updateFile = $this->pluginDir . '/data/plugin/update.md';
        $this->assign([
            'addon'      => $this->addonInfo,
            'index_url'  => $baseUrl,
            'update_url' => $baseUrl . '/update',
            'rule_url'   => $baseUrl . '/rule',
            'content'    => $this->getMarkdownContent($updateFile),
        ]);
        return $this->fetch();
    }

    // 编写规范
    public function rule(): string
    {
        $baseUrl  = "/plugin/{$this->addonName}";
        $ruleFile = $this->pluginDir . '/data/plugin/rule.md';
        $this->assign([
            'addon'      => $this->addonInfo,
            'index_url'  => $baseUrl,
            'update_url' => $baseUrl . '/update',
            'rule_url'   => $baseUrl . '/rule',
            'content'    => $this->getMarkdownContent($ruleFile),
        ]);
        return $this->fetch();
    }

    // 安装
    public function install(): \think\response\Json
    {
        if (is_file($this->lockFile)) {
            return json(['code' => 0, 'msg' => '插件已安装']);
        }
        file_put_contents($this->lockFile, (string)time());
        return json(['code' => 1, 'msg' => '插件安装成功']);
    }

    // 卸载
    public function uninstall(): \think\response\Json
    {
        if (!is_file($this->lockFile)) {
            return json(['code' => 0, 'msg' => '插件未安装']);
        }
        unlink($this->lockFile);
        return json(['code' => 1, 'msg' => '插件卸载成功']);
    }
}
