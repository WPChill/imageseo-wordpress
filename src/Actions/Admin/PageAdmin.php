<?php

namespace ImageSeoWP\Actions\Admin;

if (!defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Helpers\Pages;

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
            'Image SEO',
            'Image SEO',
            'manage_options',
            Pages::SETTINGS,
            [$this, 'pluginSettingsPage'],
           'dashicons-imageseo-logo'
        );
    }

    public function menuOrderCount()
    {
        global $submenu;
        if (isset($submenu[Pages::SETTINGS])) {
            unset($submenu[Pages::SETTINGS][0]);
        }
    }

    /**
     * Page settings.
     */
    public function pluginSettingsPage()
    {
        include_once IMAGESEO_TEMPLATES_ADMIN_PAGES . '/settings.php';
    }
}
