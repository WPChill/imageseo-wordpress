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
	public $optionServices;
	public $clientServices;

	public function __construct()
    {
        $this->optionServices = imageseo_get_service('Option');
        $this->clientServices = imageseo_get_service('ClientApi');
    }

    public function hooks()
    {
        add_action('admin_head', [$this, 'menuOrderCount']);
    }

    public function menuOrderCount()
    {
        global $submenu;
        if (isset($submenu[Pages::SETTINGS])) {
            unset($submenu[Pages::SETTINGS][0]);
        }
    }
}
