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
		// Create the email subject and body
	    $subject = __( 'Welcome to Image SEO', 'imageseo' );
	    $body    = __( 'Welcome to Image SEO', 'imageseo' ) . "\n\n" . __( 'You can now log in to your account with the following credentials:', 'imageseo' ) . "\n\n" . __( 'Email:', 'imageseo' ) . ' ' . $email . "\n" . __( 'Password:', 'imageseo' ) . ' ' . $password . "\n\n" . __( 'You can change your password in your account settings.', 'imageseo' ) . "\n\n";
		// Email the client with the credentials
	    wp_mail( $email, $subject, $body );

        wp_send_json_success([
            'user' => $newUser,
        ]);
    }
}
