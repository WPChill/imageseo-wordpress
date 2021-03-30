<?php

namespace ImageSeoWP\Actions\Admin\Ajax;

if (!defined('ABSPATH')) {
    exit;
}

class Proxy
{
    public function hooks()
    {
        add_action('wp_ajax_imageseo_proxy', [$this, 'proxy']);
    }

    public function proxy()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'code' => 'not_authorized',
            ]);
            exit;
        }

        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'imageseo_proxy')) {
            wp_send_json_error([
                'code' => 'not_authorized',
            ]);
            exit;
        }

        $path = sanitize_text_field($_GET['path']);
        $query = isset($_GET['query']) ? $_GET['query'] : null;

        $data = imageseo_get_service('Proxy')->get($path, $query);

        wp_send_json_success($data);
    }
}
