<?php

namespace ImageSeoWP\Actions\Admin\Ajax\Bulk;

if (!defined('ABSPATH')) {
    exit;
}

class Preview
{
    public function hooks()
    {
        add_action('wp_ajax_imageseo_get_preview_bulk', [$this, 'process']);
    }

    public function process()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'code' => 'not_authorized',
            ]);
            exit;
        }

        $data = get_option('_imageseo_bulk_process_settings');

        if (!$data) {
            $data = get_option('_imageseo_finish_bulk_process');
        }

        $images = [];
        $i = 0;
        $max = 5;
        do {
            if (!isset($data['id_images_optimized'][$i])) {
                ++$i;
                continue;
            }
            $attachmentId = $data['id_images_optimized'][$i];
            $report = get_post_meta($attachmentId, '_imageseo_bulk_report', true);
            $images[] = [
                'attachment_id' => $attachmentId,
                'url'           => wp_get_attachment_image_url($attachmentId),
                'report'        => $report,
            ];
            ++$i;
        } while ($i < 5);

        wp_send_json_success($images);
    }
}
