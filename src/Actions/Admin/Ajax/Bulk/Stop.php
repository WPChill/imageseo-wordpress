<?php

namespace ImageSeoWP\Actions\Admin\Ajax\Bulk;

if (!defined('ABSPATH')) {
    exit;
}

class Stop
{
    public function hooks()
    {
        add_action('wp_ajax_imageseo_stop_bulk', [$this, 'process']);
    }

    public function process()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'code' => 'not_authorized',
            ]);
            exit;
        }

        $oldSettings = get_option('_imageseo_bulk_process_settings');
        as_unschedule_all_actions('action_bulk_image_process_action_scheduler', [], 'group_bulk_image');
        delete_option('_imageseo_bulk_process_settings');
        update_option('_imageseo_pause_bulk_process', $oldSettings);

        wp_send_json_success($oldSettings);
    }
}
