<?php

namespace ImageSeoWP\Actions\Admin;

if (! defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Helpers\TabsAdmin;

class Enqueue
{


    /**
     * @return void
     */
    public function hooks()
    {
        add_action('admin_enqueue_scripts', [ $this, 'adminEnqueueScripts' ]);
    }



    /**
     * Register CSS and JS
     *
     * @see admin_enqueue_scripts
     * @param string $page
     * @return void
     */
    public function adminEnqueueScripts($page)
    {
        if (! in_array($page, [ 'toplevel_page_' . TabsAdmin::SETTINGS, 'imageseo_page_imageseo-optimization', 'upload.php', 'post.php', 'imageseo_page_imageseo-options' ], true)) {
            return;
        }

        wp_enqueue_style('imageseo-admin-css', IMAGESEO_URL_DIST . '/css/admin-css.css', [], IMAGESEO_VERSION);

        if (in_array($page, [ 'upload.php' ], true)) {
            wp_enqueue_script('imageseo-admin-js', IMAGESEO_URL_DIST . '/media-upload.js', ['jquery']);
        }

        if (in_array($page, [ 'imageseo_page_imageseo-optimization' ], true)) {
            wp_enqueue_script('imageseo-admin-js', IMAGESEO_URL_DIST . '/bulk-optimization.js', ['jquery']);
        }
    }
}