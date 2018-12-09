<?php

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Compatibility WP < 4.7.0
 */
if (! function_exists('wp_doing_ajax')) {
    function wp_doing_ajax()
    {
        /**
         * Filters whether the current request is a WordPress Ajax request.
         *
         * @since 4.7.0
         *
         * @param bool $wp_doing_ajax Whether the current request is a WordPress Ajax request.
         */
        return apply_filters('wp_doing_ajax', defined('DOING_AJAX') && DOING_AJAX);
    }
}
