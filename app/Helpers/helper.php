<?php

if (! function_exists('strtosnake')) {
    function strtosnake($value)
    {
        $value = preg_replace('/\s+/u', '', ucwords($value));

        return strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1'.'_', $value));
    }
}

if (! function_exists('mask_string')) {
    function mask_string($string, $visibleStart = 2, $visibleEnd = 2, $maskChar = '*', $type = null)
    {
        // type email
        if ($type === 'email') {
            [$name, $domain] = explode('@', $string, 2);
            $len = strlen($name);

            if ($len <= ($visibleStart + $visibleEnd)) {
                // jika nama terlalu pendek, sensor semua kecuali 1 huruf awal
                return substr($name, 0, 1)
                    .str_repeat($maskChar, max($len - 1, 0))
                    .'@'.$domain;
            }

            $masked = substr($name, 0, $visibleStart)
                .str_repeat($maskChar, $len - $visibleStart - $visibleEnd)
                .substr($name, -$visibleEnd);

            return $masked.'@'.$domain;
        }

        // default
        $len = strlen($string);
        if ($len <= ($visibleStart + $visibleEnd)) {
            return substr($string, 0, $visibleStart)
                .str_repeat($maskChar, max($len - $visibleStart, 0));
        }

        return substr($string, 0, $visibleStart)
            .str_repeat($maskChar, $len - $visibleStart - $visibleEnd)
            .substr($string, -$visibleEnd);
    }
}
