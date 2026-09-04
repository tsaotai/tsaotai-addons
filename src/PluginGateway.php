<?php
declare (strict_types=1);

namespace tsaotai\addons;

use think\facade\Request;

/**
 * 单路由分发 plugin/:identifier/[:action]，替代每个插件 5 条管理路由。
 */
class PluginGateway
{
    public function dispatch(string $identifier, string $action = 'index')
    {
        $identifier = preg_replace('/[^a-zA-Z0-9_]/', '', $identifier) ?? '';
        $action = $action !== '' ? $action : 'index';
        $allowed = ['index', 'update', 'rule', 'install', 'uninstall'];
        if ($identifier === '' || !in_array($action, $allowed, true)) {
            abort(404, '插件管理路由不存在');
        }

        if (in_array($action, ['install', 'uninstall'], true) && !Request::isPost()) {
            return json(['code' => 0, 'msg' => '请使用 POST 提交']);
        }

        $class = "\\addons\\{$identifier}\\controller\\Plugin";
        if (!class_exists($class)) {
            abort(404, '插件未提供管理控制器');
        }

        $controller = app()->make($class);
        if (!method_exists($controller, $action)) {
            abort(404, '插件管理动作不存在');
        }

        $result = $controller->{$action}();
        if (in_array($action, ['install', 'uninstall'], true)) {
            Router::clearManifest();
            AddonDiscovery::clearPluginCache($identifier);
        }
        return $result;
    }
}
