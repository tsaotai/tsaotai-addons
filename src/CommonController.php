<?php
declare (strict_types=1);

namespace tsaotai\addons;

/**
 * 插件通用基础控制器（需要登录验证）
 * 继承 BaseController，仅重写 initialize() 增加登录检测
 */
abstract class CommonController extends BaseController
{
    /**
     * 初始化 - 执行登录检测
     */
    protected function initialize()
    {
        // 登录检测
        is_login();
    }
}
