<?php

namespace ImageSeoWP\Models;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Abstract class for manage admin notices.
 *
 * @abstract
 */
abstract class AbstractNotice
{
    /**
     * Get template file for admin notice.
     *
     * @static
     *
     * @return string
     */
    public static function get_template_file()
    {
        return '';
    }

    /**
     * Callback for admin_notice hook.
     *
     * @static
     *
     * @return string
     */
    public static function admin_notice()
    {
        $screen = get_current_screen();
        if ('toplevel_page_imageseo-settings' === $screen->id) {
            return;
        }
        $class_call = get_called_class();
        if (!file_exists($class_call::get_template_file())) {
            return;
        }

        include_once $class_call::get_template_file();
    }
}
