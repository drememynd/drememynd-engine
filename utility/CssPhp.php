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

    /**
     * Create CSS files from the ones in the cssphp directory, if any.
     * @return null
     */
    public static function createCss()
    {
        /* nowhere to get content from */
        $dir = realpath(Application::$appRoot . _DS . 'cssphp');
        if (empty($dir)) {
            return;
        }

        /* nowhere to write to */
        $cssPath = Application::$webRoot . _DS . 'css';
        if (!is_dir($cssPath)) {
            return;
        }

        $files = glob($dir . _DS . '*.css');

        /* no content files to read from */
        if (empty($files)) {
            return;
        }

        $iniPath = $dir . _DS . 'css_php_vars.ini';
        $iniTime = self::getFileModTime($iniPath);
        $vars = self::getIniVars($iniPath);

        foreach ($files as $sourcePath) {
            if (self::weShouldWrite($sourcePath, $iniTime)) {
                self::writeCss($sourcePath, $vars);
            }
        }
    }

    /**
     * Check the timestamps to see if the file has expired
     * @param string $sourcePath
     * @param int $iniTime UNIX time stamp
     * @return boolean $goodToGo
     */
    protected static function weShouldWrite($sourcePath, $iniTime)
    {
        $goodToGo = false;

        $sourceTime = self::getFileModTime($sourcePath);
        $sourceName = basename($sourcePath);

        $cssPath = Application::$webRoot . _DS . 'css';
        $resultPath = $cssPath . _DS . $sourceName;
        $resultTime = self::getFileModTime($resultPath);

        /* source or ini are newer than result */
        if ($sourceTime > $resultTime || $iniTime > $resultTime) {
            $goodToGo = true;
        }

        return $goodToGo;
    }

    /**
     * Actually write the CSS
     * @param string $sourcePath path to source document
     * @param string[] $vars substitution variables
     * @return null
     */
    protected static function writeCss($sourcePath, $vars)
    {
        $cssPath = Application::$webRoot . _DS . 'css';
        $sourceName = basename($sourcePath);
        $resultPath = $cssPath . _DS . $sourceName;

        $content = file_get_contents($sourcePath);
        foreach ($vars as $name => $value) {
            $replace = '{$' . $name . '}';
            $content = str_replace($replace, $value, $content);
        }

        file_put_contents($resultPath, $content);
    }

    /**
     * Get the variables for substitution in the CSS
     * @param type $fileName path to INI file
     * @return array
     */
    protected static function getIniVars($fileName)
    {
        $vars = [];

        if (!is_file($fileName)) {
            return $vars;
        }

        $ini = Ini::parse($fileName);
        if (empty($ini)) {
            return $vars;
        }

        foreach ($ini as $section) {
            $vars = array_merge($vars, $section);
        }

        return $vars;
    }

    /**
     * Returns 0 if the file doesn't exist
     * @param string $filePath path to file
     * @return int UNIX time stamp
     */
    protected static function getFileModTime($filePath)
    {
        $time = 0;

        if (is_file($filePath)) {
            $time = filemtime($filePath);
        }

        return $time;
    }
}
