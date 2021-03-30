<?php

namespace ImageSeoWP\Actions\Admin\Ajax;

if (!defined('ABSPATH')) {
    exit;
}

class Login
{
    public function hooks()
    {
        add_action('wp_ajax_imageseo_login', [$this, 'login']);
    }

    public function register()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'code' => 'not_authorized',
            ]);
            exit;
        }

        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'imageseo_login')) {
            wp_send_json_error([
                'code' => 'not_authorized',
            ]);
            exit;
        }

        if (!isset($_POST['email']) || !isset($_POST['password'])) {
            wp_send_json_error([
                'code' => 'missing_parameters',
            ]);
            exit;
        }

        $email = sanitize_email($_POST['email']);
        $password = sanitize_text_field($_POST['password']);

        try {
            $newUser = $this->registerService->login($email, $password);
        } catch (\Exception $e) {
            wp_send_json_error([
                'code' => 'unknown_error',
            ]);
            exit;
        }

        if (null === $newUser) {
            wp_send_json_error([
                'code' => 'no_user',
            ]);
        }

        wp_send_json_success([
            'user' => $newUser,
        ]);
    }
}
