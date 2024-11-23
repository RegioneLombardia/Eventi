<?php

namespace open20\amos\events\utility;

class OrderElementsUtility
{

    /**
     * Move the element in the x position up
     * @param $array
     * @param $x
     * @return array
     */
    public static function up($array, $x)
    {
        if ($x > 0 and $x < count($array)) {
            $b = array_slice($array, 0, ($x - 1), true);
            $b[] = $array[$x];
            $b[] = $array[$x - 1];
            $b += array_slice($array, ($x + 1), count($array), true);
            return ($b);
        } else {
            return $array;
        }
    }

    /** Move the element in the x position down
     * @param $array
     * @param $x
     * @return array
     */
    public static function down($array, $x)
    {
        if (count($array) - 1 > $x) {
            $b = array_slice($array, 0, $x, true);
            $b[] = $array[$x + 1];
            $b[] = $array[$x];
            $b += array_slice($array, $x + 2, count($array), true);
            return ($b);
        } else {
            return $array;
        }
    }
}