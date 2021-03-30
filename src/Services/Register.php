<?php

namespace ImageSeoWP\Services;

if (!defined('ABSPATH')) {
    exit;
}

class Register
{
    public function __construct()
    {
        $this->optionServices = imageseo_get_service('Option');
    }

    public function register($email, $password, $options = [])
    {
        $response = wp_remote_post(IMAGESEO_API_URL . '/v1/customers/create', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode([
                'firstname'       => isset($options['firstname']) ? $options['firstname'] : '',
                'lastname'        => isset($options['lastname']) ? $options['lastname'] : '',
                'newsletters'     => isset($options['newsletters']) ? $options['newsletters'] : false,
                'email'           => $email,
                'password'        => $password,
                'wp_url'          => site_url(),
                'withProject'     => true,
                'name'            => get_bloginfo('name'),
                'optins'          => 'terms',
            ]),
        ]);

        if (is_wp_error($response)) {
            return null;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (!$body['success']) {
            return null;
        }

        $user = $body['result'];

        $options = $this->optionServices->getOptions();

        $options['api_key'] = $user['project_create']['api_key'];
        $options['allowed'] = true;
        $this->optionServices->setOptions($options);

        return $user;
    }
}
