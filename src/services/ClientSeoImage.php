<?php

namespace SeoImageWP\Services;

if (! defined('ABSPATH')) {
    exit;
}

use SeoImage\Client\Client;

class ClientSeoImage
{
    public function __construct()
    {
        $this->optionService = seoimage_get_service('OptionSeoImage');
    }

    public function getClient()
    {
        $apiKey = $this->optionService->getOption('api_key');

        $options = [];
        if (SEOIMAGE_API_LOCAL) {
            $options['host'] = 'https://api-staging.seoimage.io';
        }

        $client = new Client($apiKey, $options);
        return $client;
    }
}
