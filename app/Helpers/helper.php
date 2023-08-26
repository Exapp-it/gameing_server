<?php

if (!function_exists('realIp')) {
    function realIp(): string
    {
        $ip_keys = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($ip_keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip_list = explode(',', $_SERVER[$key]);
                foreach ($ip_list as $ip) {
                    $ip = trim($ip);

                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                        return $ip;
                    }
                }
            }
        }

        return request()->ip();
    }
}


if (!function_exists('toArray')) {
    function toArray($jsonData): array
    {
        return json_decode($jsonData, true);
    }
}


if (!function_exists('getCurrentMethodName')) {
    function getCurrentMethodName(): string
    {
        return debug_backtrace()[1]['function'];
    }
}


if (!function_exists('formatBalance')) {
    function formatBalance($balance, $decimals = 2)
    {
        return (float) number_format($balance, $decimals, ".", "");
    }
}


if (!function_exists('isMinusValue')) {
    function isMinusValue($number)
    {
        return $number < 0;
    }
}
