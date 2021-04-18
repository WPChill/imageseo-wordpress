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
        $posts = get_posts([
            'orderby'        => 'rand',
            'post_type'      => 'attachment',
            'post__in'       => isset($data['id_images_optimized']) ? $data['id_images_optimized'] : [],
            'posts_per_page' => 5,
        ]);

        foreach ($posts as $key => $post) {
            $report = get_post_meta($post->ID, '_imageseo_bulk_report', true);
            $images[] = [
                'attachment_id' => $post->ID,
                'url'           => wp_get_attachment_image_url($post->ID),
                'report'        => $report,
            ];
        }

        wp_send_json_success($images);
    }
}
