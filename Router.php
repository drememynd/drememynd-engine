<?php
namespace Engine;

use Engine\Utility\Strings;
use Engine\Control;
use Engine\Application;

/**
 * Description of Router
 *
 * @author drememynd
 */
class Router
{

    public static $page = '';
    public static $action = '';
    protected static $url = '';
    protected static $host = '';
    protected static $pages = [];
    protected static $initilized = false;

    public static function route()
    {
        self::init();

        $path = Strings::urlToArray(self::$url);

        $page = self::getPage($path);
        $controller = Control::getController($page);

        $action = self::getAction($path, $controller);
        $controller->action = $action;

        $newRoute = self::makeUrl($page, $action);

        if (self::$url != $newRoute) {
            self::redirect($newRoute);
        }

        self::$page = $page;
        self::$action = $action;

        return $controller;
    }

    protected static function getPage($path)
    {
        $page = Application::$defaultPage;
        

        if (!empty($path)) {
            $route = reset($path);

            if (!empty(Application::$extraPageList[$route])) {
                $page = $route;
            }

            if (!empty(Application::$pageList[$route])) {
                $page = $route;
            }
        }

        return $page;
    }

    protected static function makeUrl($page, $action)
    {
        $newRoute = '';

        if ($page !== 'index') {
            $newRoute .= _US . $page;
        }

        if (!empty($action) && $action !== 'index') {
            $newRoute .= _US . $action;
        }

        return $newRoute;
    }

    /**
     *
     * @param string $path
     * @param Control $controller
     * @return string
     */
    protected static function getAction($path, $controller)
    {
        $action = 'index';

        if (count($path) < 2) {
            return $action;
        }

        array_shift($path);
        $route = reset($path);
        if (empty($route)) {
            return $action;
        }

        if ($controller->isValidAction($route)) {
            $action = $route;
        }

        return $action;
    }

    protected static function init()
    {
        if (self::$initilized) {
            return;
        }

        self::$host = filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_STRING);
        self::$url = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_STRING);

        self::$initilized = true;
    }

    protected static function redirect($url = '')
    {
        header('Location: ' . $url);
    }
}
