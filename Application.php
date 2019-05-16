<?php
namespace Engine;

use Engine\Utility\Ini;
use Engine\Utility\Strings;

/**
 * Description of Application
 *
 * @author drememynd
 */
class Application
{
    /* application paths */

    public static $webRoot = '';
    public static $vendorRoot = '';
    public static $engineRoot = '';
    public static $appRoot = '';
    public static $viewRoot = '';
    public static $controlRoot = '';
    /* application directories */
    public static $engineDir = '';
    public static $appDir = '';
    public static $viewDir = '';
    public static $controlDir = '';
    /* application data */
    public static $pageList = '';
    public static $defaultPage = '';
    public static $defaultAction = '';
    public static $defaultView = '';

    public static function setUp()
    {
        self::$webRoot = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT', FILTER_SANITIZE_STRING);
        self::$engineRoot = __DIR__;
        self::$engineDir = basename(__DIR__);
        self::$vendorRoot = dirname(__DIR__);

        $ini = Ini::parse(self::$webRoot . _DS . 'app_config.ini');

        self::$appRoot = self::getAppRoot($ini);
        self::$viewRoot = self::getViewRoot($ini);
        self::$controlRoot = self::getControlRoot($ini);

        self::$appDir = self::getAppDirectory($ini);
        self::$viewDir = self::getViewDirectory($ini);
        self::$controlDir = self::getControlDirectory($ini);

        self::$defaultView = self::getDefaultView($ini);
        self::$pageList = self::getPageList($ini);
        self::$defaultPage = self::getDefaultPage($ini);
        self::$defaultAction = self::getDefaultAction($ini);
    }

    public static function getControllerClassName($page)
    {
        $name = Strings::snakeToCamel($page);

        $nsPath = self::$appDir . _DS . self::$controlDir;
        $filePath = self::$controlRoot . _DS . $name . '.php';

        if (!is_file($filePath)) {
            $nsPath = self::$engineDir . _DS . 'control';
            $filePath = self::$engineRoot . _DS . 'control' . _DS . $name . '.php';
        }

        if (!is_file($filePath)) {
            $nsPath = self::$engineDir;
            $filePath = self::$engineRoot . _DS . $name . '.php';
        }

        if (!is_file($filePath)) {
            $nsPath = self::$engineDir;
            $name = 'Control';
            $filePath = self::$engineRoot . _DS . 'Control.php';
        }

        $nameSpace = Strings::pathToNameSpace($nsPath);
        $class = $nameSpace . _NS . $name;

        return $class;
    }

    protected static function getAppRoot($ini)
    {
        $appRoot = self::$engineRoot;

        if (!empty($ini['paths']['app_dir'])) {
            $path = realpath(self::$webRoot . _DS . $ini['paths']['app_dir']);
            if (!empty($path)) {
                $appRoot = $path;
            }
        }

        return $appRoot;
    }

    protected static function getAppDirectory($ini)
    {
        $appDir = 'engine';

        if (!empty($ini['paths']['app_dir'])) {
            $path = realpath(self::$webRoot . _DS . $ini['paths']['app_dir']);
            if (!empty($path)) {
                $appDir = $ini['paths']['app_dir'];
            }
        }

        return $appDir;
    }

    protected static function getViewRoot($ini)
    {
        $viewPath = '';

        if (!empty($ini['paths']['view_dir'])) {
            $viewPath = realpath(self::$appRoot . _DS . $ini['paths']['view_dir']);
        }
        if (empty($viewPath)) {
            $viewPath = realpath(self::$appRoot . _DS . 'view');
        }
        if (empty($viewPath)) {
            $viewPath = realpath(self::$engineRoot . _DS . 'view');
        }

        return $viewPath;
    }

    protected static function getViewDirectory($ini)
    {
        $viewDir = 'view';

        if (!empty($ini['paths']['view_dir'])) {
            $path = realpath(self::$appRoot . _DS . $ini['paths']['view_dir']);
            if (!empty($path)) {
                $viewDir = $ini['paths']['view_dir'];
            }
        }

        return $viewDir;
    }

    protected static function getControlRoot($ini)
    {
        $controlPath = '';

        if (!empty($ini['paths']['control_dir'])) {
            $controlPath = realpath(self::$appRoot . _DS . $ini['paths']['control_dir']);
        }
        if (empty($controlPath)) {
            $controlPath = realpath(self::$appRoot . _DS . 'control');
        }
        if (empty($controlPath)) {
            $controlPath = realpath(self::$engineRoot . _DS . 'control');
        }

        return $controlPath;
    }

    protected static function getControlDirectory($ini)
    {
        $controlDir = 'control';

        if (!empty($ini['paths']['control_dir'])) {
            $path = realpath(self::$appRoot . _DS . $ini['paths']['control_dir']);
            if (!empty($path)) {
                $controlDir = $ini['paths']['control_dir'];
            }
        }

        return $controlDir;
    }

    protected static function getDefaultPage($ini)
    {
        $defaultPage = 'home';

        if (!empty($ini['defaults']['page'])) {
            $defaultPage = $ini['defaults']['page'];
        }

        if (empty(self::$pageList[$defaultPage])) {
            self::$pageList[$defaultPage] = "Default Page Not In Pages List";
        }

        return $defaultPage;
    }

    protected static function getDefaultAction($ini)
    {
        $defaultAction = 'index';

        if (!empty($ini['defaults']['action'])) {
            $defaultAction = $ini['defaults']['action'];
        }

        return $defaultAction;
    }

    protected static function getDefaultView($ini)
    {
        $defaultView = '';

        if (!empty($ini['defaults']['view'])) {
            $defaultView = self::$viewRoot . _DS . $ini['defaults']['view'] . '.php';
        }
        if (!is_file($defaultView)) {
            $defaultView = self::$viewRoot . _DS . 'view.php';
        }
        if (!is_file($defaultView)) {
            $defaultView = self::$engineRoot . _DS . 'view' . _DS . 'view.php';
        }

        return $defaultView;
    }

    protected static function getPageList($ini)
    {
        $default = ['home' => 'No Pages Configured'];
        $iniList = [];

        if (!empty($ini['pages'])) {
            $iniList = $ini['pages'];
        }

        $views = glob(self::$appRoot . _DS . self::$viewDir . _DS . '*.php');

        $names = [];
        foreach ($views as $view) {
            if ($view == self::$defaultView) {
                continue;
            }
            $name = basename($view, '.php');
            $fancy = Strings::snakeToWords($name);
            $names[$name] = $fancy;
        }

        $start = array_merge($iniList, $names);
        $list = array_merge($start, $iniList);

        if (empty($list)) {
            $list = $default;
        }

        return $list;
    }
}
