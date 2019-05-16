<?php
namespace Engine\Utility;

use Engine\Application;
use Engine\Render;
use Engine\Utility\Ini;

/**
 * Description of CssPhp
 *
 * @author drememynd
 */
class CssPhp
{

    public static function createCss()
    {

        $dir = realpath(Application::$appRoot . _DS . 'cssphp');
        if (empty($dir)) {
            return false;
        }

        $files = glob($dir . _DS . '*.css');

        if (empty($files)) {
            return false;
        }

        $vars = self::getIniVars($dir);

        foreach ($files as $filePath) {
            if (self::checkModTime($filePath)) {
                self::writeCss($filePath, $vars);
            }
        }
    }

    protected static function writeCss($filePath, $vars)
    {
        $content = file_get_contents($filePath);
        foreach($vars as $name => $value) {
            $replace = '{$'.$name.'}';
            $content = str_replace($replace,$value, $content);
        }
        
        $cssPath = Application::$webRoot . _DS . 'css';

        if (is_dir($cssPath)) {
            $name = basename($filePath);
            file_put_contents($cssPath . _DS . $name, $content);
        }
    }

    protected static function checkModTime($filePath)
    {
        $name = basename($filePath, '.css');
        $time = filemtime($filePath);

        $times = self::getModTimes();

        if (!empty($times[$name])) {
            if ($times[$name] >= $time) {
                return false;
            }
        }

        $times[$name] = $time;

        self::writeModTimes($times);

        return true;
    }

    protected static function getModTimes()
    {
        $times = [];

        $dataFile = self::getDataFileName();

        $content = file($dataFile);

        if (empty($content)) {
            return $times;
        }

        foreach ($content as $line) {
            $pair = explode(':', $line);
            if (count($pair) == 2) {
                $times[$pair[0]] = $pair[1];
            }
        }

        return $times;
    }

    protected static function writeModTimes($times)
    {
        $contents = '';

        $lines = [];
        if (!empty($times)) {
            foreach ($times as $name => $time) {
                $lines[] = $name . ':' . $time;
            }
            $contents = implode("\n", $lines);
        }

        $dataFile = self::getDataFileName();
        file_put_contents($dataFile, $contents);
    }

    protected static function getDataFileName()
    {
        $filePath = __DIR__ . _DS . 'css_php.dat';
        if (!is_file($filePath)) {
            file_put_contents($filePath, '');
        }
        return $filePath;
    }

    protected static function getIniVars($dir)
    {
        $vars = [];

        if (!is_file($dir . _DS . 'css_php_vars.ini')) {
            return $vars;
        }

        $ini = Ini::parse($dir . _DS . 'css_php_vars.ini');
        if (empty($ini)) {
            return $vars;
        }

        foreach ($ini as $section) {
            $vars = array_merge($vars, $section);
        }

        return $vars;
    }
}
