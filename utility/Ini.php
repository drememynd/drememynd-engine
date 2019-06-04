<?php
namespace Engine\Utility;

/**
 * Parse INI Files
 * Include parsing of complex array structures
 *
 * @author drememynd
 */
class Ini
{

    protected static $iniCache = [];

    public static function parse($path = '', $section = '')
    {
        $ini = [];

        if (!empty(self::$iniCache[$path])) {
            return self::sectionLoop(self::$iniCache[$path], $section);
        }

        if (empty($path)) {
            return $ini;
        }

        if (!is_file($path)) {
            return $ini;
        }


        $rawIni = parse_ini_file($path, true);
        
        if (empty($rawIni) || !is_array($rawIni)) {
            return $ini;
        }
        self::$iniCache[$path] = $rawIni;

        return self::sectionLoop($rawIni, $section);
    }

    private static function sectionLoop($rawIni, $section = '')
    {
        $config = [];

        foreach ($rawIni as $currentSection => $group) {

            if (!empty($section) && $currentSection != $section) {
                continue;
            }

            $config[$currentSection] = [];
            if (empty($group) || !is_array($group)) {
                continue;
            }

            foreach ($group as $string => $value) {
                $config[$currentSection] = self::makeSectionArrays($config[$currentSection], $string, $value);
            }
        }

        if (!empty($section) && isset($config[$section])) {
            return $config[$section];
        }

        return $config;
    }

    private static function makeSectionArrays($section, $string, $value)
    {
        $arr = explode('.', $string);
        $temp = &$section;
        foreach ($arr as $part) {
            if (!isset($temp[$part])) {
                $temp[$part] = [];
            }
            $temp = &$temp[$part];
        }
        $temp = $value;

        return $section;
    }
}
