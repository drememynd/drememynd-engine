<?php
namespace Engine;

use Engine\Control;
use Engine\Application;
use Engine\Utility\Strings;

/**
 * Description of Render
 *
 * @author drememynd
 */
class Render
{

    /**
     *
     * @param Control $controller
     * @param mixed[] $params parameters
     * @return string the rendered HTML
     */
    public static function getHtml($controller, $params = [])
    {
        $page = $controller->page;
        $action = $controller->action;

        $vars = $controller->$action($params);
        $vars['view'] = $controller;
        $vars['pages'] = Application::$pageList;

        $pageFile = self::getPage($page);
        $layoutFile = self::getLayout();
        $layoutExtras = self::getLayoutExtras();

        $vars['content'] = self::render($pageFile, $vars);

        foreach ($layoutExtras as $name => $path) {
            $vars[$name] = self::render($path, $vars);
        }

        $html = self::render($layoutFile, $vars);

        return $html;
    }

    protected static function getPage($page)
    {
        $pagePath = Application::$viewRoot . _DS . $page . '.php';

        if (!is_file($pagePath)) {
            $pagePath = Application::$viewRoot . _DS . 'layout' . _DS . $page . '.php';
        }

        if (!is_file($pagePath)) {
            $pagePath = Application::$defaultView;
        }

        return $pagePath;
    }

    protected static function getLayout($vars = [])
    {
        $layout = Application::$viewRoot . _DS . 'layout' . _DS . 'layout.php';

        if (!is_file($layout)) {
            $layout = Application::$viewRoot . _DS . 'layout.php';
        }

        if (!is_file($layout)) {
            $layout = Application::$engineRoot . _DS . 'view' . _DS . 'layout' . _DS . 'layout.php';
        }

        if (!is_file($layout)) {
            $layout = Application::$engineRoot . _DS . 'view' . _DS . 'layout.php';
        }

        return $layout;
    }

    protected static function getLayoutExtras()
    {
        $extras = [];

        $layoutDir = Application::$viewRoot . _DS . 'layout';
        $engLayoutDir = Application::$engineRoot . _DS . 'view' . _DS . 'layout';

        if (!is_dir($layoutDir)) {
            $layoutDir = $engLayoutDir;
        }

        if (!is_dir($layoutDir)) {
            return $extras;
        }
        if ($layoutDir != $engLayoutDir) {
            $extras = self::getExtraList($layoutDir);
            $extraLayouts = self::getExtraList($layoutDir, true);
        }
        $more = self::getExtraList($engLayoutDir);
        $moreLayouts = self::getExtraList($engLayoutDir, true);

        $result = array_merge($more, $extras, $moreLayouts, $extraLayouts);

        return $result;
    }

    protected static function getExtraList($dir, $layouts = false)
    {
        $extras = [];
        $files = glob($dir . _DS . '*.php');

        if (!empty($files)) {
            foreach ($files as $filePath) {
                $file = basename($filePath, '.php');
                $hasLayout = (strpos($file, 'layout') === false) ? false : true;
                $varName = Strings::snakeToCamel($file, true);
                if ((($layouts && $hasLayout && $file != 'layout') || (!$layouts && !$hasLayout))) {
                    $extras[$varName] = $filePath;
                }
            }
        }

        return $extras;
    }

    /**
     * renders the view with the data
     *
     * @param string $path path to the view
     * @param array $vars mixed information required for view
     * @return string the rendered html
     */
    protected static function render($path, $vars = [])
    {
        extract($vars);
        unset($vars);

        ob_start();

        if (file_exists($path)) {
            include $path;
        }

        return ob_get_clean();
    }
}
