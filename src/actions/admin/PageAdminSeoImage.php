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
        add_action('admin_head', [ $this, 'menuOrderCount' ]);
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

        add_submenu_page(
            TabsAdminSeoImage::SETTINGS,
            __('Settings', 'seoimage'),
            __('Settings', 'seoimage'),
            'manage_options',
            'seoimage-settings',
            [$this, 'pluginSettingsPage']
        );
        add_submenu_page(
            TabsAdminSeoImage::SETTINGS,
            __('Bulk Optimization', 'seoimage'),
            __('Bulk Optimization', 'seoimage'),
            'manage_options',
            'seoimage-optimization',
            [$this, 'optimizationPage']
        );
    }

    /**
    * @return void
    */
    public function menuOrderCount()
    {
        global $submenu;
        if (isset($submenu[TabsAdminSeoImage::SETTINGS])) {
            unset($submenu[TabsAdminSeoImage::SETTINGS][0]);
        }
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

    public function optimizationPage()
    {
        include_once SEOIMAGE_TEMPLATES_ADMIN_PAGES . '/optimization.php';
    }
}
