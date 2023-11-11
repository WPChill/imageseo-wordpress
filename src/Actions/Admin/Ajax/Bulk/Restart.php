<?php

namespace ImageSeoWP\Actions\Admin\Ajax\Bulk;

if (!defined('ABSPATH')) {
    exit;
}

class Restart
{
    public function hooks()
    {
        add_action('wp_ajax_imageseo_restart_bulk', [$this, 'start']);
    }

    public function start()
    {
	    check_ajax_referer('imageseo_ajax_nonce', '_wpnonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'code' => 'not_authorized',
            ]);
            exit;
        }

        $limitExcedeed = imageseo_get_service('UserInfo')->hasLimitExcedeed();
        $settings = get_option('_imageseo_pause_bulk_process');
        if ($limitExcedeed) {
            wp_send_json_error($settings);
        }

        update_option('_imageseo_bulk_process_settings', $settings);
        delete_option('_imageseo_pause_bulk_process');

        as_schedule_single_action(time(), 'action_bulk_image_process_action_scheduler', [], 'group_bulk_image');

        wp_send_json_success($settings);
    }
}
