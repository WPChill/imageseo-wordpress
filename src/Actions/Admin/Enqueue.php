<?php

namespace ImageSeoWP\Actions\Admin;

if (!defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Helpers\AltFormat;
use ImageSeoWP\Helpers\Bulk\AltSpecification;
use ImageSeoWP\Helpers\Pages;

class Enqueue
{
    public function hooks()
    {
        add_action('admin_enqueue_scripts', [$this, 'adminEnqueueScripts']);
        add_action('admin_enqueue_scripts', [$this, 'adminEnqueueCSS']);
        add_action('admin_enqueue_scripts', [$this, 'pluginPage']);
        add_action('admin_enqueue_scripts', [$this, 'application']);
        add_action('admin_enqueue_scripts', [$this, 'wizard']);
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

        wp_enqueue_style('imageseo-admin-css', IMAGESEO_URL_DIST . '/css/imageseo-tw.css', [], IMAGESEO_VERSION);
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
            wp_enqueue_script('imageseo-admin-js', IMAGESEO_URL_DIST . '/media-upload.js', ['jquery', 'wp-i18n']);
        }

        if (in_array($page, ['post.php'], true)) {
            wp_enqueue_script('imageseo-admin-generate-social-media-js', IMAGESEO_URL_DIST . '/generate-social-media.js', ['jquery'], IMAGESEO_VERSION, true);
        }
    }

    public function application($page)
    {
        if (!in_array($page, ['image-seo_page_imageseo-options', 'image-seo_page_imageseo-settings', 'toplevel_page_' . Pages::SETTINGS], true)) {
            return;
        }

        if (isset($_GET['wizard'])) {
            return;
        }

        $owner = imageseo_get_service('ClientApi')->getOwnerByApiKey();
        $options = imageseo_get_service('Option')->getOptions();
        $languages = imageseo_get_service('ClientApi')->getLanguages();

        $totalAltNoOptimize = imageseo_get_service('QueryImages')->getNumberImageNonOptimizeAlt();
        $percentLoose = imageseo_get_service('ImageLibrary')->getPercentLooseTraffic($totalAltNoOptimize);
        $currentProcessed = get_option('_imageseo_bulk_process_settings');
        $lastBulkProcess = get_option('_imageseo_pause_bulk_process');

        $limitImages = 10;
        if (null !== $owner && isset($owner['plan']['limit_images'])) {
            $limitImages = $owner['plan']['limit_images'] + $owner['bonus_stock_images'];
        }

        $scheduled = as_next_scheduled_action('action_bulk_image_process_action_scheduler', [], 'group_bulk_image');
        $getScheduled = as_get_scheduled_actions([
            'hooks' => 'action_bulk_image_process_action_scheduler',
            'group' => 'group_bulk_image',
        ]);

        $urlRecount = admin_url('admin-post.php?action=imageseo_recount_images');
        $urlRecount = wp_nonce_url($urlRecount, 'imageseo_recount_images');

        $data = [
            'API_KEY'                        => imageseo_get_option('api_key'),
            'API_URL'                        => IMAGESEO_API_URL,
            'APP_URL'                        => IMAGESEO_APP_URL,
            'ADMIN_URL_PAGE_MONITORS'        => admin_url('options-general.php?page=' . Pages::SETTINGS),
            'URL_DIST'                       => IMAGESEO_URL_DIST,
            'LIBRARY_URL'                    => admin_url('upload.php?mode=list'),
            'SITE_URL'                       => IMAGESEO_SITE_URL,
            'ADMIN_AJAX'                     => admin_url('admin-ajax.php'),
            'USER'                           => $owner,
            'OPTIONS'                        => $options,
            'LANGUAGES'                      => $languages,
            'NEXT_SCHEDULED'                 => $scheduled ? $scheduled : false,
            'LIMIT_EXCEDEED'                 => imageseo_get_service('UserInfo')->hasLimitExcedeed() ? true : false,
            'CURRENT_PROCESSED'              => $currentProcessed ? $currentProcessed : null,
            'IS_FINISH'                      => false !== get_option('_imageseo_bulk_is_finish') ? true : false,
            'LAST_PROCESSED'                 => $lastBulkProcess ? $lastBulkProcess : null,
            'TOTAL_ALT_NO_OPTIMIZE'          => $totalAltNoOptimize,
            'PERCENT_TRAFFIC_LOOSE'          => $percentLoose,
            'LIMIT_IMAGES'                   => $limitImages,
            'ALT_FORMATS'                    => AltFormat::getFormats(),
            'ALT_SPECIFICATION'              => AltSpecification::getMetas(),
            'ALT_FILL_TYPE'                  => AltSpecification::getFillType(),
            'PAGE_BUILDERS'                  => imageseo_get_service('WordPressData')->getPageBuilders(),
            'URL_RECOUNT'                    => $urlRecount,
            'SOCIAL_POST_TYPES'              => imageseo_get_service('WordPressData')->getAllPostTypesSocialMedia(),
        ];

        wp_register_script('imageseo-application', IMAGESEO_URL_DIST . '/application.js', ['wp-i18n'], IMAGESEO_VERSION, true);
        wp_enqueue_script('imageseo-application');

        if (function_exists('wp_set_script_translations')) {
            wp_set_script_translations('imageseo-application', 'imageseo', IMAGESEO_LANGUAGES);
        }

        wp_localize_script('imageseo-application', 'IMAGESEO_DATA', $data);
    }

    public function wizard($page)
    {
        if (!in_array($page, ['image-seo_page_imageseo-options', 'image-seo_page_imageseo-settings', 'toplevel_page_' . Pages::SETTINGS], true)) {
            return;
        }

        if (!isset($_GET['wizard'])) {
            return;
        }

        $totalAltNoOptimize = imageseo_get_service('QueryImages')->getNumberImageNonOptimizeAlt();
        $percentLoose = imageseo_get_service('ImageLibrary')->getPercentLooseTraffic($totalAltNoOptimize);

        $data = [
            'API_KEY'                        => imageseo_get_option('api_key'),
            'API_URL'                        => IMAGESEO_API_URL,
            'APP_URL'                        => IMAGESEO_APP_URL,
            'URL_DIST'                       => IMAGESEO_URL_DIST,
            'SITE_URL'                       => IMAGESEO_SITE_URL,
            'ADMIN_AJAX'                     => admin_url('admin-ajax.php'),
            'TOTAL_ALT_NO_OPTIMIZE'          => $totalAltNoOptimize,
            'PERCENT_TRAFFIC_LOOSE'          => $percentLoose,
            'PAGE_BUILDERS'                  => imageseo_get_service('WordPressData')->getPageBuilders(),
        ];

        wp_register_script('imageseo-wizard', IMAGESEO_URL_DIST . '/wizard.js', ['wp-i18n'], IMAGESEO_VERSION, true);
        wp_enqueue_script('imageseo-wizard');

        if (function_exists('wp_set_script_translations')) {
            wp_set_script_translations('imageseo-wizard', 'imageseo', IMAGESEO_LANGUAGES);
        }

        wp_localize_script('imageseo-wizard', 'IMAGESEO_DATA', $data);
    }
}
