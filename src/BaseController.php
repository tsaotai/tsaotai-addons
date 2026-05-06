<?php
declare (strict_types=1);

namespace tsaotai\addons;

use think\App;
use think\exception\ValidateException;
use think\Validate;
use think\facade\View;
use think\facade\Request;

/**
 * 插件通用基础控制器
 */
abstract class BaseController
{
    /**
     * Request实例
     * @var \think\Request
     */
    protected \think\Request $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected App $app;

    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];

    /**
     * 插件名称
     */
    protected string $addon = '';

    /**
     * 模板变量
     */
    protected array $vars = [];

    /**
     * 构造方法
     * @access public
     * @param App $app 应用对象
     */
    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $this->app->request;

        // 自动识别插件名
        $this->addon = explode('\\', get_class($this))[1] ?? '';

        // 自动绑定插件视图目录
        $this->initAddonView();

        // 控制器初始化
        $this->initialize();
    }

    /**
     * 初始化
     */
    protected function initialize(): void
    {

    }

    /**
     * 自动绑定插件视图路径
     */
    protected function initAddonView(): void
    {
        // 不使用全局 View::config，避免多插件冲突
    }

    /**
     * 验证数据
     */
    protected function validate(array $data, string|array $validate, array $message = [], bool $batch = false): bool
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                [$validate, $scene] = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v     = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        $v->message($message);

        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }

        return $v->failException(true)->check($data);
    }

    /**
     * 模板赋值
     */
    protected function assign(string|array $name, mixed $value = null): static
    {
        if (is_array($name)) {
            $this->vars = array_merge($this->vars, $name);
        } else {
            $this->vars[$name] = $value;
        }
        return $this;
    }

    /**
     * 获取插件视图路径
     */
    protected function getAddonViewPath(): string
    {
        return $this->app->getRootPath() . "addons/{$this->addon}/view/";
    }

    /**
     * 渲染模板 | 1:1 复刻 TP8 原生官方规则，无任何自定义
     */
    protected function fetch(string $template = ''): string
    {
        // 空模板时，严格按照 TP8 原生规则解析：控制器目录/控制器名(小写)/方法名
        if (empty($template)) {
            // 获取控制器命名空间（去除插件前缀）
            $controller = str_replace("addons\\{$this->addon}\\controller\\", '', get_class($this));
            // 转换为目录格式 + 小写（TP8 原生规范）
            $controller = str_replace('\\', '/', strtolower($controller));
            // 拼接方法名（TP8 原生规范）
            $template = $controller . '/' . $this->request->action();
        }

        // 直接使用完整文件路径渲染，避免全局配置冲突
        $viewPath = $this->getAddonViewPath();
        $fullTemplate = $viewPath . $template;
        
        return View::assign($this->vars)->fetch($fullTemplate);
    }
}
