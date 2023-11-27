<?php

namespace ImageSeoWP\Actions\Admin\Ajax;

if (!defined('ABSPATH')) {
    exit;
}

class Register
{
    public function hooks()
    {
        add_action('wp_ajax_imageseo_register', [$this, 'register']);
    }

    public function register()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'code' => 'not_authorized',
            ]);
            exit;
        }

	    check_ajax_referer( IMAGESEO_OPTION_GROUP . '-options', '_wpnonce' );

        if (!isset($_POST['email'], $_POST['password'], $_POST['lastname'], $_POST['firstname'])) {
            wp_send_json_error([
                'code' => 'missing_parameters',
            ]);
            exit;
        }

        $email = sanitize_email($_POST['email']);
        $password = (string) $_POST['password'];

        try {
            $newUser = imageseo_get_service('Register')->register($email, $password, [
                'firstname'    => sanitize_text_field($_POST['firstname']),
                'lastname'     => sanitize_text_field($_POST['lastname']),
                'newsletters'  => isset($_POST['newsletters']) && 'true' === $_POST['newsletters'],
            ]);
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
