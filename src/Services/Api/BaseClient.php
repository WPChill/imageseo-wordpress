<?php

namespace ImageSeoWP\Services\Api;

if (!defined('ABSPATH')) {
    exit;
}

abstract class BaseClient
{
    public function getHeaders($apiKey = null)
    {
        $headers = [
            'Content-Type' => 'application/json',
        ];

        if ($apiKey) {
            $headers['Authorization'] = $apiKey;
        } else {
            $headers['Authorization'] = imageseo_get_option('api_key');
        }

        return $headers;
    }
}
