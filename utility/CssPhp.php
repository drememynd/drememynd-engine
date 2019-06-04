<?php
namespace Engine\Utility;

use Engine\Engine\Application;
use Engine\Utility\Ini;
use Autoprefixer;

require_once 'autoprefixer/Autoprefixer.php';
require_once 'autoprefixer/AutoprefixerException.php';

/**
 * Description of CssPhp
 *
 * @author drememynd
 */
class CssPhp
{

    protected static $cssDir = '';
    protected static $engDir = '';
    protected static $appDir = '';
    protected static $engValsTime = 0;
    protected static $appValsTime = 0;
    protected static $iniVars = [];

    /**
     * Create CSS files from the ones in the cssphp directory, if any.
     * @return null
     */
    public static function createCss()
    {
        if (!self::setPaths()) {
            return;
        }

        /* no content files to read from */
        $files = self::findFiles();
        if (empty($files)) {
            return;
        }

        self::setIniVariables();

        foreach ($files as $fileInfo) {
            if (self::weShouldWrite($fileInfo['name'], $fileInfo['dir'], $fileInfo['origin'])) {
                self::writeCss($fileInfo['name'], $fileInfo['dir'], $fileInfo['origin']);
            }
        }
    }

    /**
     * Finds the paths and detects if they are adequate to proceed.
     *
     * @return boolean true if enough paths exist
     */
    protected static function setPaths()
    {
        $okToRun = true;

        $engDir = realpath(Application::$engineRoot . _DS . 'cssphp');
        if (!empty($engDir)) {
            self::$engDir = $engDir;
        }

        $appDir = realpath(Application::$appRoot . _DS . 'cssphp');
        if (!empty($appDir)) {
            self::$appDir = $appDir;
        }

        $okToRun &= (!empty($appDir) || !empty($engDir));

        /* nowhere to write to */
        $cssDir = realpath(Application::$webRoot . _DS . 'css');
        if (!empty($cssDir)) {
            self::$cssDir = $cssDir;
        }

        $okToRun &= (!empty($cssDir));

        return $okToRun;
    }

    /**
     * Find the files to process
     *
     * @return string[] [$fileName => [path => directory, origin => sourceCssPath]
     */
    protected static function findFiles()
    {
        $files = [];

        if (!empty(self::$engDir)) {
            $list = self::globRecursive(self::$engDir . _DS . '*.css');
            foreach ($list as $filePath) {
                $info = self::makeFileInfo($filePath, self::$engDir);
                $files[$info['name']] = $info;
            }
        }

        if (!empty(self::$appDir)) {
            $list = self::globRecursive(self::$appDir . _DS . '*.css');
            foreach ($list as $filePath) {
                $info = self::makeFileInfo($filePath, self::$appDir);
                $files[$info['name']] = $info;
            }
        }

        return $files;
    }

    protected static function makeFileInfo($filePath, $orginPath)
    {
        $info = [];

        $file = basename($filePath);
        $path = dirname($filePath);
        $dir = str_replace($orginPath, '', $path);

        $info['name'] = $file;
        $info['dir'] = $dir;
        $info['origin'] = $orginPath;

        return $info;
    }

    protected static function setIniVariables()
    {
        $engVars = [];
        $engPath = self::$engDir . _DS . 'css_php_vars.ini';
        if (is_file($engPath)) {
            self::$engValsTime = self::getFileModTime($engPath);
            $engVars = self::getIniVars($engPath);
        }

        $appVars = [];
        $appPath = self::$appDir . _DS . 'css_php_vars.ini';
        if (is_file($appPath)) {
            self::$appValsTime = self::getFileModTime($appPath);
            $appVars = self::getIniVars($appPath);
        }
        
        self::$iniVars = array_merge($engVars, $appVars);
    }

    /**
     * Check the timestamps to see if the target file has expired
     * @param string $sourceName
     * @param string $sourceDir
     * @param string $sourcePath
     * @return boolean $goodToGo
     */
    protected static function weShouldWrite($sourceName, $sourceDir, $sourcePath)
    {
        $goodToGo = false;

        $sourceTime = self::getFileModTime($sourcePath . _DS . $sourceDir . _DS . $sourceName);

        $resultTime = self::getFileModTime(self::$cssDir . $sourceDir . _DS . $sourceName);

        /* source or ini are newer than result */
        if ($sourceTime > $resultTime || self::$appValsTime > $resultTime || self::$engValsTime > $resultTime) {
            $goodToGo = true;
        }

        return $goodToGo;
    }

    /**
     * Actually write the CSS
     * @param string $sourceName path to source document
     * @param string $sourcePath path to source document
     * @return null
     */
    protected static function writeCss($sourceName, $sourceDir, $sourcePath)
    {
        $originFilePath = $sourcePath . $sourceDir . _DS . $sourceName;
        $resultFilePath = self::$cssDir . $sourceDir . _DS . $sourceName;

        $content = file_get_contents($originFilePath);

        if (!empty(self::$iniVars)) {
            foreach (self::$iniVars as $name => $value) {
                $replace = '{' . $name . '}';
                $content = str_replace($replace, $value, $content);
            }
        }

        try {
            $ap = new Autoprefixer();
            $prefixed = $ap->compile($content);
        } catch (\Exception $ex) {
            self::printExceptionContent($ex, $originFilePath);
            throw $ex;
        }

        file_put_contents($resultFilePath, $prefixed);
    }

    protected static function printExceptionContent($ex, $filePath)
    {
        $trace = $ex->getTrace();
        $css = '<pre><br>FILE:<br>' .$filePath.'<br></pre>';
        
        if (!empty($trace[0]['args'][0][0])) {
            $css .= '<pre><br>CSS:<br>' .$trace[0]['args'][0][0].'</pre>';
        }
        
        echo $css;
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

    protected static function globRecursive($pattern, $flags = 0)
    {
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
            $files = array_merge($files, self::globRecursive($dir . '/' . basename($pattern), $flags));
        }
        return $files;
    }
}
