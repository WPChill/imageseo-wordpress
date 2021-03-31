<?php

namespace ImageSeoWP\Actions\Admin\Ajax\Images;

if (!defined('ABSPATH')) {
    exit;
}

class OptimizedTimeEstimated
{
    public function __construct()
    {
        $this->altService = imageseo_get_service('Alt');
    }

    public function hooks()
    {
        add_action('wp_ajax_imageseo_get_optimized_time_estimated', [$this, 'get']);
    }

    public function get()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'code' => 'not_authorized',
            ]);
            exit;
        }

        $totalNoAlt = imageseo_get_service('QueryImages')->getNumberImageNonOptimizeAlt();
        $minutes = imageseo_get_service('ImageLibrary')->getEstimatedByImagesHuman($totalNoAlt);
        $stringTimeEstimated = imageseo_get_service('ImageLibrary')->getStringEstimatedImages($totalNoAlt);

        wp_send_json_success([
            'minutes_by_human'        => $minutes,
            'string_time_estimated'   => $stringTimeEstimated,
        ]);
    }
}
