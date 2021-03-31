<?php

namespace ImageSeoWP\Actions\Admin\Ajax\Images;

if (!defined('ABSPATH')) {
    exit;
}

class CountImages
{
    public function __construct()
    {
        $this->altService = imageseo_get_service('Alt');
    }

    public function hooks()
    {
        add_action('wp_ajax_imageseo_get_count_images', [$this, 'get']);
    }

    public function get()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'code' => 'not_authorized',
            ]);
            exit;
        }

        $total = imageseo_get_service('QueryImages')->getTotalImages();
        $totalNoAlt = imageseo_get_service('QueryImages')->getNumberImageNonOptimizeAlt();

        wp_send_json_success([
            'total_images'        => $total,
            'total_images_no_alt' => $totalNoAlt,
        ]);
    }
}
