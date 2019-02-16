<?php

namespace ImageSeoWP\Helpers;

if (! defined('ABSPATH')) {
    exit;
}

abstract class TabsAdmin
{
    /**
     * @var string
     */
    const SETTINGS = 'imageseo-settings';

    /**
     * @var string
     */
    const SETTINGS_ALT = 'alt';

    /**
     * @var string
     */
    const SETTINGS_RENAME_FILE = 'rename';


    /**
     * Get tabs constant
     * @static
     * @return array
     */
    public static function getTabs()
    {
        return [
            self::SETTINGS,
            self::SETTINGS_ALT,
            // self::SETTINGS_RENAME_FILE
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
                'title' => __('General settings', 'imageseo'),
                'url'   => get_admin_url(
                    null,
                    sprintf('admin.php?page=%s&tab=%s', TabsAdmin::SETTINGS, self::SETTINGS)
                ),
            ],
            // self::SETTINGS_ALT => [
            //     'title' => __('Alt settings', 'imageseo'),
            //     'url'   => get_admin_url(
            //         null,
            //         sprintf('admin.php?page=%s&tab=%s', TabsAdmin::SETTINGS, self::SETTINGS_ALT)
            //     ),
            // ],
            // self::SETTINGS_RENAME_FILE => [
            //     'title' => __('Rename media settings', 'imageseo'),
            //     'url'   => get_admin_url(
            //         null,
            //         sprintf('admin.php?page=%s&tab=%s', TabsAdmin::SETTINGS, self::SETTINGS_RENAME_FILE)
            //     ),
            // ],
        ];
    }
}
