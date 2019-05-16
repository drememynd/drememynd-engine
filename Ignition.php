<?php
namespace Engine;

use Engine\Router;
use Engine\Render;
use Engine\Application;
use Engine\Utility\CssPhp;

require_once 'configure.php';

/**
 * Description of Ignition
 *
 * @author drememynd
 */
class Ignition
{

    public static function start()
    {
        Application::setUp();
        self::setUpAutoload();

        CssPhp::createCss();

        $controller = Router::route();

        $html = Render::getHtml($controller);

        echo $html;
    }

    protected static function setUpAutoload()
    {
        Autoload::addPath(Application::$webRoot);
        Autoload::addPath(Application::$vendorRoot);
        Autoload::addPath(Application::$appRoot);
        Autoload::setup();
    }
}
