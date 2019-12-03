<?php

namespace ImageSeoWP\Helpers;

if (! defined('ABSPATH')) {
    exit;
}

abstract class ServerSoftware
{

    public static function isApache()
    {
        $soft = $_SERVER['SERVER_SOFTWARE'];
        return strpos(strtolower($soft), 'apache') !== false;
    }


    public static function isNginx(){
        $soft = $_SERVER['SERVER_SOFTWARE'];
        return strpos(strtolower($soft), 'nginx') !== false;
    }
}

