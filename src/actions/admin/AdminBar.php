<?php

namespace SeoImageWP\Actions\Admin;

if (! defined('ABSPATH')) {
    exit;
}

/**
 * @since 1.0.0
 */
class AdminBar
{

    /**
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->reportImageServices   = seoimage_get_service('ReportImage');
    }

    /**
     * @return void
     */
    public function hooks()
    {
        if (!seoimage_allowed()) {
            return;
        }
        add_action('admin_bar_menu', [$this, 'adminBarMenu'], 99);
        add_action('wp_ajax_get_info_user', [$this, 'getInfoUser']);
        add_action('wp_ajax_nopriv_get_info_user', [$this, 'getInfoUser']);
    }

    public function getInfoUser()
    {
        $html             = 'SeoImage - User';
        // Call API

        wp_send_json_success($html);
    }

    /**
     * @return void
     */
    public function adminBarMenu()
    {
        global $wp_admin_bar;

        $title = '<span class="ab-icon icon-seopress-seopress"></span> '.__('SEO', 'wp-seopress');
        $title = apply_filters('seopress_adminbar_icon', $title);

        $wp_admin_bar->add_menu(array(
            'parent'    => false,
            'id'    => 'seoimage',
            'title' => 'SeoImage',
            'href'  => admin_url('admin.php?page=seoimage-settings'),
        ));

        if (! is_admin()) {
            $wp_admin_bar->add_menu(array(
                'parent'    => 'seoimage',
                'id'    => 'seoimage-alts',
                'title'  => '<div id="wp-admin-bar-seoimage-loading-alts" class="hide-if-no-js">' . __('Loading count alts...', 'seoimage') . '</div><div id="wp-admin-bar-seoimage-content" class="hide-if-no-js"></div>',
            ));
        }


        // $wp_admin_bar->add_menu(array(
        //     'parent' => 'seoimage',
        //     'id'     => 'seoimage-profile',
        //     'title'  => '<div id="wp-admin-bar-seoimage-loading-profile" class="hide-if-no-js">' . __('Loading...', 'seoimage') . '</div><div id="wp-admin-bar-seoimage-content" class="hide-if-no-js"></div>',
        // ));
    }
}
