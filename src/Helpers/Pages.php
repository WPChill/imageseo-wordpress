<?php

namespace ImageSeoWP\Helpers;

if (! defined('ABSPATH')) {
    exit;
}

abstract class Pages
{
    /**
     * @var string
     */
    const SETTINGS = 'imageseo-settings';


    /**
     * @static
     * @return array
     */
    public static function getPages()
    {
        return [
            self::SETTINGS => [
                'title' => __('General settings', 'imageseo'),
                'url'   => get_admin_url(
                    null,
                    sprintf('admin.php?page=%s', self::SETTINGS)
                ),
            ]
        ];
    }
}
