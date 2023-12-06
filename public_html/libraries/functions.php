<?php

namespace libraries;

function print_array($arr): void
{
    echo "<pre>";
    print_r($arr);
    echo "</pre>";
}

if (!function_exists('mb_str_replace')) {

    /**
     * @param string $needle
     * @param string $str_replace
     * @param string $haystack
     * @return string
     */
    function mb_str_replace(string $needle, string $str_replace, string $haystack): string
    {
        return implode($str_replace, explode($needle, $haystack));
    }

}