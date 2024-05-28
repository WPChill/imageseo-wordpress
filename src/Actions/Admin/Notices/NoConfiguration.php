<?php

namespace ImageSeoWP\Actions\Admin\Notices;

if (!defined('ABSPATH')) {
    exit;
}

class NoConfiguration
{
	public object $optionServices;
    public function __construct()
    {
        $this->optionServices = imageseo_get_service('Option');
    }

    public function hooks()
    {
        $apiKey = $this->optionServices->getOption('api_key');
        if (empty($apiKey)) {
            add_action('admin_notices', ['\ImageSeoWP\Notices\NoConfiguration', 'admin_notice']);
        }
    }
}
