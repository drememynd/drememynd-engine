<?php
namespace Engine\Engine;

/**
 * Sets Up the Autoload and Defines the Callback
 */
class Autoload
{

    /** @var type */
    private static $paths = [];

    /**
     * Add a path to the list to autoload
     * @param type $path
     */
    public static function addPath($path)
    {
        if (!in_array($path, self::$paths)) {
            self::$paths[$path] = $path;
        }
    }

    /**
     *  Build directory list and register callback
     */
    public static function setup()
    {
        foreach (self::$paths as $path) {
            self::setUpIncludePath($path);
        }

        spl_autoload_register(array(__CLASS__, 'findClass'), true, true);
    }

    /**
     * if the file exists, require it
     *
     * @param string $class the name of the class
     */
    public static function findClass($class)
    {
        $file = self::getClassPath($class);

        foreach (self::$paths as $path) {
            $full = $path . _DS . $file;

            if (is_file($full)) {
                require_once $path . _DS . $file;
                break;
            }
        }
    }

    /**
     *
     * @param string $class class name with namespace
     */
    public static function getClassPath($class)
    {
        $path = str_replace(_NS, _DS, $class);
        $file = basename($path) . '.php';
        $dir = strtolower(dirname($path));

        return $dir . _DS . $file;
    }

    protected static function setUpIncludePath($path)
    {
        $clean = realpath($path);
        if (empty($path)) {
            return;
        }

        $current = get_include_path();
        $new = $current . _PS . $clean;
        set_include_path($new);
    }
}
