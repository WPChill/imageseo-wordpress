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

        add_submenu_page(
            Pages::SETTINGS,
            __('Settings', 'imageseo'),
            __('Settings', 'imageseo'),
            'manage_options',
            'imageseo-options',
            [$this, 'pluginSettingsPage']
        );
        add_submenu_page(
            Pages::SETTINGS,
            __('Alt & Name Optimizer', 'imageseo'),
            __('Alt & Name Optimizer', 'imageseo'),
            'manage_options',
            'imageseo-optimization',
            [$this, 'optimizationPage']
        );
        add_submenu_page(
            Pages::SETTINGS,
            __('Social Card Optimizer', 'imageseo'),
            __('Social Card Optimizer', 'imageseo'),
            'manage_options',
            'imageseo-social-media',
            [$this, 'socialMedia']
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
        $this->owner = $this->clientServices->getOwnerByApiKey();

        if (isset($_GET['tab'])) { // phpcs:ignore
            $this->tab_active = sanitize_text_field(wp_unslash($_GET['tab'])); // phpcs:ignore
        }

        $this->options = $this->optionServices->getOptions();
        $this->languages = $this->clientServices->getLanguages();
        include_once IMAGESEO_TEMPLATES_ADMIN_PAGES . '/settings.php';
    }

    public function optimizationPage()
    {
        $this->owner = $this->clientServices->getOwnerByApiKey();
        $this->languages = $this->clientServices->getLanguages();
        $this->options = $this->optionServices->getOptions();
        include_once IMAGESEO_TEMPLATES_ADMIN_PAGES . '/optimization.php';
    }

    public function socialMedia()
    {
        $this->owner = $this->clientServices->getOwnerByApiKey();
        $this->options = $this->optionServices->getOptions();
        include_once IMAGESEO_TEMPLATES_ADMIN_PAGES . '/social-media.php';
    }
}
