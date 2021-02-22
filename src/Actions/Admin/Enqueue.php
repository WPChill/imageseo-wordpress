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
        add_action('admin_enqueue_scripts', [$this, 'pluginPage']);
    }

    public function pluginPage($page)
    {
        if ('plugins.php' !== $page) {
            return;
        }

        wp_enqueue_script('imageseo-deactivate', IMAGESEO_URL_DIST . '/deactivate-intent.js', ['jquery'], IMAGESEO_VERSION, true);
        wp_localize_script('imageseo-deactivate', 'IMAGESEO_DATA', [
            'IMAGESEO_URL_DIST'     => IMAGESEO_URL_DIST,
            'ADMIN_AJAX_URL'        => wp_nonce_url(admin_url('admin-ajax.php'), 'imageseo_deactivate_plugin'),
        ]);
        wp_localize_script('imageseo-deactivate', 'imageseo_i18n', [
            'modal_title' => __("Can we get some info on why you're disabling?", 'imageseo'),
            'reasons'     => [
                'deactivate_temporary'        => __("It's a temporary deactivation. I'm just debugging a problem.", 'imageseo'),
                'bad_support'                 => __('Support / Customer service was unsatisfactory', 'imageseo'),
                'bad_support_helper'          => __("We're sorry about that. Is there anything we could do to improve your experience?", 'imageseo'),
                'plugin_complicated'          => __('The plugin is too complicated to configure.', 'imageseo'),
                'plugin_complicated_helper'   => __('Our goal is to keep the plugin as simple as possible. If you need help or support, we can help you: support@imageseo.io', 'imageseo'),
                'lack_feature'                => __('Lack of feature or functionnality', 'imageseo'),
                'lack_feature_helper'         => __("We're sorry about that. Is there anything we could do to improve your experience?", 'imageseo'),
            ],
            'button_submit' => __('Send & Deactivate', 'imageseo'),
            'cancel'        => __('Cancel', 'imageseo'),
            'skip'          => __('Skip & Deactivate', 'imageseo'),
        ]);
    }

    public function adminEnqueueCSS($page)
    {
        wp_enqueue_style('imageseo-admin-global-css', IMAGESEO_URL_DIST . '/css/admin-global.css', [], IMAGESEO_VERSION);

        if (!in_array($page, ['edit.php', 'toplevel_page_' . Pages::SETTINGS, 'image-seo_page_imageseo-optimization', 'upload.php', 'post.php', 'image-seo_page_imageseo-options', 'image-seo_page_imageseo-social-media'], true)) {
            return;
        }

        wp_enqueue_style('imageseo-admin-css', IMAGESEO_URL_DIST . '/css/admin.css', [], IMAGESEO_VERSION);
    }

    /**
     * @see admin_enqueue_scripts
     *
     * @param string $page
     */
    public function adminEnqueueScripts($page)
    {
        if (!in_array($page, ['toplevel_page_' . Pages::SETTINGS, 'image-seo_page_imageseo-optimization', 'upload.php', 'post.php', 'image-seo_page_imageseo-options', 'image-seo_page_imageseo-settings', 'image-seo_page_imageseo-social-media'], true)) {
            return;
        }

        if (in_array($page, ['upload.php'], true)) {
            wp_enqueue_script('imageseo-admin-js', IMAGESEO_URL_DIST . '/media-upload.js', ['jquery']);
        }

        if (in_array($page, ['image-seo_page_imageseo-optimization', 'image-seo_page_imageseo-social-media'], true)) {
            wp_enqueue_script('imageseo-admin-js', IMAGESEO_URL_DIST . '/bulk.js', ['jquery', 'wp-i18n'], IMAGESEO_VERSION, true);
        }

        if (in_array($page, ['image-seo_page_imageseo-options', 'image-seo_page_imageseo-settings', 'toplevel_page_' . Pages::SETTINGS], true)) {
            wp_enqueue_script('imageseo-admin-register-js', IMAGESEO_URL_DIST . '/register.js', ['jquery'], IMAGESEO_VERSION, true);
            wp_enqueue_script('imageseo-admin-api-key-js', IMAGESEO_URL_DIST . '/api-key.js', ['jquery'], IMAGESEO_VERSION, true);
        }

        if (in_array($page, ['post.php'], true)) {
            wp_enqueue_script('imageseo-admin-generate-social-media-js', IMAGESEO_URL_DIST . '/generate-social-media.js', ['jquery'], IMAGESEO_VERSION, true);
        }
    }
}
