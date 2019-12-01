<?php

namespace ImageSeoWP\Actions\Admin;

if (!defined('ABSPATH')) {
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
        $this->optionServices = imageseo_get_service('Option');
        $this->clientServices = imageseo_get_service('ClientApi');
    }

    public function hooks()
    {
        add_action('admin_menu', [$this, 'pluginMenu']);
        add_action('admin_head', [$this, 'menuOrderCount']);
    }

    /**
     * Add menu and sub pages.
     *
     * @see admin_menu
     */
    public function pluginMenu()
    {
        add_menu_page(
            'ImageSEO',
            'ImageSEO',
            'manage_options',
            TabsAdmin::SETTINGS,
            [$this, 'pluginSettingsPage'],
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

    public function menuOrderCount()
    {
        global $submenu;
        if (isset($submenu[TabsAdmin::SETTINGS])) {
            unset($submenu[TabsAdmin::SETTINGS][0]);
        }
    }

    /**
     * Page settings.
     */
    public function pluginSettingsPage()
    {
        $this->tabs = TabsAdmin::getFullTabs();
        $this->tab_active = TabsAdmin::SETTINGS;
        $this->owner = $this->clientServices->getApiKeyOwner();

        if (isset($_GET['tab'])) { // phpcs:ignore
            $this->tab_active = sanitize_text_field(wp_unslash($_GET['tab'])); // phpcs:ignore
        }

        $this->options = $this->optionServices->getOptions();
        $this->languages = $this->clientServices->getLanguages();
        include_once IMAGESEO_TEMPLATES_ADMIN_PAGES . '/settings.php';
    }

    public function optimizationPage()
    {
        $this->owner = $this->clientServices->getApiKeyOwner();
        include_once IMAGESEO_TEMPLATES_ADMIN_PAGES . '/optimization.php';
    }
}
