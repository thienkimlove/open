<?php

/**
 * Version of array_merge_recursive without overwriting numeric keys
 *
 * @param  array $array1 Initial array to merge.
 * @param  array ...     Variable list of arrays to recursively merge.
 *
 * @return array
 * @link   http://www.php.net/manual/en/function.array-merge-recursive.php#106985
 * @author Martyniuk Vasyl <martyniuk.vasyl@gmail.com>
 */
if (!function_exists('array_merge_recursive_new')) {
    function array_merge_recursive_new()
    {
        $arrays = func_get_args();

        $base = array_shift($arrays);

        foreach ($arrays as $array) {
            reset($base);

            while (list($key, $value) = @each($array)) {
                if (is_array($value) && @is_array($base[$key])) {
                    $base[$key] = array_merge_recursive_new($base[$key], $value);
                } else {
                    $base[$key] = $value;
                }
            }
        }

        return $base;
    }
}

if (! function_exists('array_sort_desc')) {
    // DangNh
    function array_sort_desc($array, $value)
    {
        $sort = array();

        foreach($array as $k=>$v) {
            $sort[$value][$k] = $v[$value];
        }

        array_multisort($sort[$value], SORT_DESC, $array);

        return $array;
    }
}