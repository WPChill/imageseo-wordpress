<?php

namespace ImageSeoWP\Actions\Front;

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
        if (!imageseo_allowed()) {
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

        wp_enqueue_script('imageseo-admin-bar-js', IMAGESEO_URL_DIST . '/admin-bar.js', ['jquery']);
        wp_localize_script('imageseo-admin-bar-js', 'i18nImageSeo', [
            "alternative_text" => __("Alternative text", "imageseo")
        ]);
    }
}
