<?php
namespace Engine\Utility;

/**
 * Description of Strings
 *
 * @author drememynd
 */
class Strings
{

    /**
     *
     * @param string $url
     * @return string[]
     */
    public static function urlToArray($url)
    {
        $clean = self::cleanSeparatorsTo($url, '-');
        $lower = strtolower($clean);
        $pure = strstr($lower, '?', true);
        if ($pure === false) {
            $pure = $lower;
        }
        $cleaner = preg_replace('~[^a-z-'._US.']~','',$pure);
        $trimmed = self::makeArray($cleaner, _US);
        
        return $trimmed;
    }

    public static function urlPartToNsPart($part)
    {
        $camel = self::snakeToCamel($part, false);
        return $camel;
    }

    public static function urlPartToMethod($part)
    {
        $camel = self::snakeToCamel($part, true);
        return $camel;
    }

    public static function nameSpaceToPath($nameSpace)
    {
        $url = self::cleanSlashesTo($nameSpace, _US);
        $snake = self::camelToSnake($url, '-');
        $array = self::makeArray($snake, _US, '-');
        $path = implode(_US, $array);
        return $path;
    }
    
    public static function pathToNameSpace($path) {
        $words = self::cleanSlashesTo($path,' ');
        $upper = ucwords($words);
        $done = str_replace(' ', _NS, $upper);
        
        return $done;
    }

    /**
     * will respect separator as words
     *
     * @param string $snake must be snake case
     * @param boolean $lcfirst should the first letter be lower case?
     * @return string in CamelCase or camelCase
     */
    public static function snakeToCamel($snake, $lcfirst = false)
    {
        $lower = strtolower($snake);
        $camel = self::wordsToCamel($lower, $lcfirst);

        return $camel;
    }

    /**
     * will respect separator as words
     *
     * @param string $snake must be snake case
     * @return string in CamelCase or camelCase
     */
    public static function snakeToWords($snake)
    {
        $spaces = self::cleanSeparatorsTo($snake, ' ');
        $words = ucwords($spaces);

        return $words;
    }

    /**
     * will create new words if separated
     *
     * @param string $words words
     * @param type $lcfirst
     * @return type
     */
    protected static function wordsToCamel($words, $lcfirst = false)
    {
        $spaces = self::cleanSeparatorsTo($words, ' ');
        $caps = ucwords($spaces);
        $camel = str_replace(' ', '', $caps);
        if ($lcfirst) {
            $camel = lcfirst($camel);
        }

        return $camel;
    }

    public static function camelToSnake($camel, $separator = '_')
    {
        $chars = str_split($camel);

        $snake = strtolower(array_shift($chars));
        foreach ($chars as $c) {
            $lc = strtolower($c);
            if ($c !== $lc) {
                $snake .= $separator;
                $snake .= $lc;
            } else {
                $snake .= $c;
            }
        }

        return $snake;
    }

    /**
     * will create new words if separated
     *
     * @param string $words
     * @return string
     */
    protected static function wordsToSnake($words, $separator = '_')
    {
        $camel = self::wordsToCamel($words);
        $snake = self::camelToSnake($camel, $separator);

        return $snake;
    }

    /**
     *
     * @param string $string
     */
    protected static function makeArray($string, $separator, $trim = '-')
    {
        $strings = explode($separator, $string);
        $trimmed = [];
        foreach ($strings as $word) {
            $pure = trim($word);
            $cleaner = trim($word, $trim);
            if (!empty($cleaner)) {
                $trimmed[] = $cleaner;
            }
        }
        return $trimmed;
    }

    protected static function cleanSeparatorsTo($string, $clean)
    {
        $replace = ['-', '%20', '_', ' '];
        $done = str_replace($replace, $clean, $string);
        return $done;
    }

    protected static function cleanSlashesTo($string, $clean)
    {
        $replace = [_DS, _NS, _US];
        $done = str_replace($replace, $clean, $string);
        return $done;
    }
}
