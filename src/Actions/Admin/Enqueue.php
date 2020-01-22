<?php

namespace ImageSeoWP\Actions\Admin;

if (!defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Helpers\Pages;

class Enqueue
{
    public function hooks()
    {
        add_action('admin_enqueue_scripts', [$this, 'adminEnqueueScripts']);
        add_action('admin_enqueue_scripts', [$this, 'adminEnqueueCSS']);
    }

    public function adminEnqueueCSS($page)
    {
        wp_enqueue_style('imageseo-admin-global-css', IMAGESEO_URL_DIST . '/css/admin-global-css.css', [], IMAGESEO_VERSION);

        if (!in_array($page, ['edit.php', 'toplevel_page_' . Pages::SETTINGS, 'imageseo_page_imageseo-optimization', 'upload.php', 'post.php', 'imageseo_page_imageseo-options', 'imageseo_page_imageseo-social-media'], true)) {
            return;
        }

        wp_enqueue_style('imageseo-admin-css', IMAGESEO_URL_DIST . '/css/admin-css.css', [], IMAGESEO_VERSION);
    }

    /**
     * @see admin_enqueue_scripts
     *
     * @param string $page
     */
    public function adminEnqueueScripts($page)
    {
        if (!in_array($page, ['toplevel_page_' . Pages::SETTINGS, 'imageseo_page_imageseo-optimization', 'upload.php', 'post.php', 'imageseo_page_imageseo-options', 'imageseo_page_imageseo-social-media'], true)) {
            return;
        }

        if (in_array($page, ['upload.php'], true)) {
            wp_enqueue_script('imageseo-admin-js', IMAGESEO_URL_DIST . '/media-upload.js', ['jquery']);
        }

        if (in_array($page, ['imageseo_page_imageseo-optimization', 'imageseo_page_imageseo-social-media'], true)) {
            wp_enqueue_script('imageseo-admin-js', IMAGESEO_URL_DIST . '/bulk.js', ['jquery'], IMAGESEO_VERSION, true);
        }

        if (in_array($page, ['imageseo_page_imageseo-options', 'toplevel_page_' . Pages::SETTINGS], true)) {
            wp_enqueue_script('imageseo-admin-register-js', IMAGESEO_URL_DIST . '/register.js', ['jquery'], IMAGESEO_VERSION, true);
            wp_enqueue_script('imageseo-admin-api-key-js', IMAGESEO_URL_DIST . '/api-key.js', ['jquery'], IMAGESEO_VERSION, true);
        }
    }
}
