<?php

namespace ImageSeoWP\Actions\Admin;

if (! defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Helpers\TabsAdmin;

/**
 * @since 1.0.0
 */
class PageAdmin
{
    public function __construct()
    {
        $this->optionServices     = imageseo_get_service('Option');
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
            'ImageSEO',
            'ImageSEO',
            'manage_options',
            TabsAdmin::SETTINGS,
            [ $this, 'pluginSettingsPage' ],
            IMAGESEO_URL_DIST . '/images/favicon.png'
        );

        add_submenu_page(
            TabsAdmin::SETTINGS,
            __('Settings', 'imageseo'),
            __('Settings', 'imageseo'),
            'manage_options',
            'imageseo-options',
            [$this, 'pluginSettingsPage']
        );
        add_submenu_page(
            TabsAdmin::SETTINGS,
            __('Bulk Optimization', 'imageseo'),
            __('Bulk Optimization', 'imageseo'),
            'manage_options',
            'imageseo-optimization',
            [$this, 'optimizationPage']
        );
    }

    /**
    * @return void
    */
    public function menuOrderCount()
    {
        global $submenu;
        if (isset($submenu[TabsAdmin::SETTINGS])) {
            unset($submenu[TabsAdmin::SETTINGS][0]);
        }
    }

    /**
     * Page settings
     * @return void
     */
    public function pluginSettingsPage()
    {
        $this->tabs       = TabsAdmin::getFullTabs();
        $this->tab_active = TabsAdmin::SETTINGS;

        if (isset($_GET['tab'])) { // phpcs:ignore
            $this->tab_active = sanitize_text_field(wp_unslash($_GET['tab'])); // phpcs:ignore
        }

        $this->options = $this->optionServices->getOptions();
        include_once IMAGESEO_TEMPLATES_ADMIN_PAGES . '/settings.php';
    }

    public function optimizationPage()
    {
        include_once IMAGESEO_TEMPLATES_ADMIN_PAGES . '/optimization.php';
    }
}
