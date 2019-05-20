<?php
namespace Engine;

use Engine\Router;
use Engine\Render;
use Engine\Application;

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
        self::developmentConfiguration();       

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
    
    protected static function developmentConfiguration()
    {
        if(is_file(Application::$webRoot._DS.'dev_config.php')) {
            include_once Application::$webRoot._DS.'dev_config.php';
        }
    }
}
