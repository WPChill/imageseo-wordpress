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

        $limitExcedeed = imageseo_get_service('UserInfo')->hasLimitExcedeed();

        $scheduled = as_next_scheduled_action('action_bulk_image_process_action_scheduler', [], 'group_bulk_image');

        wp_send_json_success([
            'current'        => $settings,
            'finish'         => $finish,
            'limit_excedeed' => $limitExcedeed,
            'scheduled'      => $scheduled,
        ]);
    }
}