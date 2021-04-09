<?php

namespace ImageSeoWP\Actions\Admin;

if (!defined('ABSPATH')) {
    exit;
}

class AdminBar
{
    public function __construct()
    {
        $this->reportImageServices = imageseo_get_service('ReportImage');
    }

    public function hooks()
    {
        if (!imageseo_allowed() || is_admin()) {
            return;
        }

        add_action('admin_bar_menu', [$this, 'adminBarMenu'], 99);
    }

    public function adminBarMenu()
    {
        global $wp_admin_bar;

        $wp_admin_bar->add_menu([
            'parent'    => false,
            'id'        => 'imageseo',
            'title'     => 'ImageSEO',
            'href'      => admin_url('admin.php?page=imageseo-settings'),
        ]);

        if (!is_admin()) {
            $wp_admin_bar->add_menu([
                'parent'    => 'imageseo',
                'id'        => 'imageseo-alts',
                'title'     => '<div id="wp-admin-bar-imageseo-loading-alts" class="hide-if-no-js">' . __('Loading count alts...', 'imageseo') . '</div><div id="wp-admin-bar-imageseo-content" class="hide-if-no-js"></div>',
            ]);
        }
    }
}
