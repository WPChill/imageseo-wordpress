<?php

namespace ImageSeoWP\Actions\Admin\Notices;

if (!defined('ABSPATH')) {
    exit;
}

class Curl
{
    /**
     * @since 2.0
     */
    public function __construct()
    {
        $this->optionServices = imageseo_get_service('Option');
    }

    public function hooks()
    {
        if (!function_exists('curl_version')) {
            add_action('admin_notices', ['\ImageSeoWP\Notices\Curl', 'admin_notice']);
        }
    }
}
