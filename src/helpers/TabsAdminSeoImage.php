<?php

namespace SeoImageWP\Helpers;

if (! defined('ABSPATH')) {
    exit;
}

abstract class TabsAdminSeoImage
{

    /**
     * @var string
     */
    const SETTINGS = 'settings-seoimage';

    const SETTINGS_ALT = 'alt';


    /**
     * Get tabs constant
     *
     * @since 2.0
     * @static
     * @return array
     */
    public static function getTabs()
    {
        return [
            self::SETTINGS,
            self::SETTINGS_ALT
        ];
    }

    /**
     * Get full tabs information
     * @static
     * @return array
     */
    public static function getFullTabs()
    {
        return [
            self::SETTINGS => [
                'title' => __('General settings', 'seoimage'),
                'url'   => get_admin_url(
                    null,
                    sprintf('admin.php?page=%s&tab=%s', TabsAdminSeoImage::SETTINGS, self::SETTINGS)
                ),
            ],
            self::SETTINGS_ALT => [
                'title' => __('Alt settings', 'seoimage'),
                'url'   => get_admin_url(
                    null,
                    sprintf('admin.php?page=%s&tab=%s', TabsAdminSeoImage::SETTINGS, self::SETTINGS_ALT)
                ),
            ],
        ];
    }
}
