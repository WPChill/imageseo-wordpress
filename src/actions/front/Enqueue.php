<?php

namespace SeoImageWP\Actions\Front;

if (! defined('ABSPATH')) {
    exit;
}

class Enqueue
{


    /**
     * @return void
     */
    public function hooks()
    {
        if (!seoimage_allowed()) {
            return;
        }

        add_action('wp_enqueue_scripts', [ $this, 'enqueueScripts' ]);
    }



    /**
     * @return void
     */
    public function enqueueScripts()
    {
        if (! current_user_can('administrator')) {
            return;
        }

        wp_enqueue_script('seoimage-admin-bar-js', SEOIMAGE_URL_DIST . '/admin-bar.js', ['jquery']);
        wp_localize_script('seoimage-admin-bar-js', 'i18nSeoImage', [
            "alternative_text" => __("Alternative text", "seoimage")
        ]);
    }
}
