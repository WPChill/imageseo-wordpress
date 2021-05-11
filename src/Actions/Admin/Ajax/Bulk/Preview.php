<?php

namespace ImageSeoWP\Actions\Admin\Ajax\Bulk;

if (!defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Helpers\Bulk\AltSpecification;

class Preview
{
    public function hooks()
    {
        add_action('wp_ajax_imageseo_get_preview_bulk', [$this, 'process']);
    }

    protected function getImagesDefault($data)
    {
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

        return $images;
    }

    protected function getImagesNextGen($data)
    {
        $images = [];

        global $wpdb;
        $sqlQuery = 'SELECT pic.extras_post_id as postId, pic.pid as id ';
        $sqlQuery .= "FROM {$wpdb->prefix}ngg_pictures pic ";
        $sqlQuery .= 'WHERE 1=1 ';
        $sqlQuery .= sprintf("AND pic.pid IN ('%s') ", implode("','", $data['id_images_optimized']));
        $sqlQuery .= 'ORDER BY RAND() ';
        $sqlQuery .= 'LIMIT 5 ';

        $posts = $wpdb->get_results($wpdb->prepare($sqlQuery), ARRAY_A);

        foreach ($posts as $key => $post) {
            $report = get_post_meta($post['postId'], '_imageseo_bulk_report', true);
            $images[] = [
                'attachment_id' => $post['postId'],
                'url'           => imageseo_get_service('QueryNextGen')->getUrl($post['id']),
                'report'        => $report,
            ];
        }

        return $images;
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

        if (AltSpecification::NEXTGEN_GALLERY === $data['settings']['altFilter']) {
            $images = $this->getImagesNextGen($data);
        } else {
            $images = $this->getImagesDefault($data);
        }

        wp_send_json_success($images);
    }
}
