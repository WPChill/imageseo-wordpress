<?php

namespace SeoImageWP\Actions\Admin;

if (! defined('ABSPATH')) {
    exit;
}

use SeoImageWP\Helpers\TabsAdminSeoImage;

/**
 * @since 1.0.0
 */
class PageAdminSeoImage
{
    public function __construct()
    {
        $this->optionServices     = seoimage_get_service('OptionSeoImage');
    }

    /**
     * @return void
     */
    public function hooks()
    {
        add_action('admin_menu', [ $this, 'pluginMenu' ]);
    }

    /**
     * Add menu and sub pages
     * @see admin_menu
     * @return void
     */
    public function pluginMenu()
    {
        add_menu_page(
            'SeoImage',
            'SeoImage',
            'manage_options',
            TabsAdminSeoImage::SETTINGS,
            [ $this, 'pluginSettingsPage' ],
            SEOIMAGE_URL_DIST . '/images/favicon.png'
        );
    }

    /**
     * Page settings
     * @return void
     */
    public function pluginSettingsPage()
    {
        $this->tabs       = TabsAdminSeoImage::getFullTabs();
        $this->tab_active = TabsAdminSeoImage::SETTINGS;

        if (isset($_GET['tab'])) { // phpcs:ignore
            $this->tab_active = sanitize_text_field(wp_unslash($_GET['tab'])); // phpcs:ignore
        }

        $this->options = $this->optionServices->getOptions();
        include_once SEOIMAGE_TEMPLATES_ADMIN_PAGES . '/settings.php';
    }
}
