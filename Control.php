<?php
namespace Engine;

use Engine\Utility\Strings;
use Engine\Application;

/**
 * Description of Control
 *
 * @author drememynd
 */
class Control
{

    public $page = '';
    public $action = '';
    public $pageName = '';

    public function __construct($page)
    {
        $this->page = $page;

        $this->pageName = $page;
        if (!empty(Application::$pageList[$page])) {
            $this->pageName = Application::$pageList[$page];
        }

        $this->init();
    }

    public static function getController($page)
    {
        $class = Application::getControllerClassName($page);
        $controller = new $class($page);

        return $controller;
    }

    protected function init()
    {
        
    }

    protected function before($params = [])
    {
        return $params;
    }

    protected function index($params = [])
    {
        return $params;
    }

    protected function after($params = [])
    {
        return $params;
    }

    /**
     *
     * @param string $action
     * @return boolean
     */
    public function isValidAction($action)
    {

        if (in_array($action, ['init', 'before', 'after'])) {
            return false;
        }

        $method = Strings::snakeToCamel($action, true);
        if (method_exists($this, $method)) {
            return true;
        }

        return false;
    }

    public function __call($action, $params = [])
    {
        if (in_array($action, ['init', 'before', 'after'])) {
            return;
        }

        $method = Strings::snakeToCamel($action, true);

        $before = $this->before($params);
        if (method_exists($this, $method)) {
            $result = $this->$method($before);
        } else {
            $result = $this->index($before);
        }
        $result = $this->after($result);

        return $result;
    }
}
