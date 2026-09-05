# 插件怎么写

本包不管脚手架。在产品仓 `addons/{id}/` 手写，规则以产品 `AGENTS.md` 为准。

- `plugin.php`：`identifier` 等于目录名；`state` 为 `enable` 才会被加载
- `route.php`：自己声明 URL，包不会再注册 `/plugin/:id`
- 控制器继承插件自己的 `Base`（最终到 `tsaotai\addons\BaseController`）
- 视图 `{extend name="../view/smartadmin/base.html"}`，不要在插件里复制 `base.html`
- 界面文案 `translate('中文')`；三件套 `plugin.php` / `README.md` / `CHANGELOG.md`

开通、权限、租户勾选在产品应用中心 / control，不在本包。
