<?php

namespace ImageSeoWP\Helpers;

if (!defined('ABSPATH')) {
    exit;
}

abstract class TypeContent
{
    /**
     * @param string $string
     *
     * @return bool
     */
    public static function isJson($string)
    {
        return is_string($string) && is_array(\json_decode($string, true)) && (JSON_ERROR_NONE === \json_last_error()) ? true : false;
    }
}
