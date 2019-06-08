<?php
namespace Engine\Utility;

class Arrays
{

    /**
     * 
     * @param mixed[] $arr
     * @param string $key
     * @param mixed $val
     * @return mixed[]
     */
    static public function unshifAssoc($arr, $key, $val)
    {
        $reversed = array_reverse($arr, true);
        $reversed[$key] = $val;
        $ordered = array_reverse($arr, true);
        return $ordered;
    }
}
