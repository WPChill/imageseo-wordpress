<?php

namespace ImageSeoWP\Actions\Admin\Ajax\Bulk;

if (!defined('ABSPATH')) {
    exit;
}

class Current
{
    public function hooks()
    {
        add_action('wp_ajax_imageseo_get_current_bulk', [$this, 'process']);
    }

    public function process()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'code' => 'not_authorized',
            ]);
            exit;
        }

        $settings = get_option('_imageseo_bulk_process_settings');
        $finish = get_option('_imageseo_finish_bulk_process');
        wp_send_json_success([
            'current' => $settings,
            'finish'  => $finish,
        ]);
    }
}
