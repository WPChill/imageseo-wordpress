<?php

namespace ImageSeoWP\Actions\Admin\Ajax;

if (!defined('ABSPATH')) {
    exit;
}

class Register
{
    public function hooks()
    {
        add_action('wp_ajax_imageseo_regoster', [$this, 'register']);
    }

    public function register()
    {
        if (!isset($_POST['email']) || !isset($_POST['password'])) {
            wp_send_json_error([
                'code' => 'missing_parameters',
            ]);
            exit;
        }

        $email = sanitize_email($_POST['email']);
        $password = (string) $_POST['password'];

        try {
            $newUser = imageseo_get_service('Register')->register($email, $password);
        } catch (\Exception $e) {
            wp_send_json_error([
                'code' => 'unknown_error',
            ]);
            exit;
        }

        if (null === $newUser) {
            wp_send_json_error([
                'code' => 'unknown_error',
            ]);
        }

        wp_send_json_success([
            'user' => $newUser,
        ]);
    }
}
