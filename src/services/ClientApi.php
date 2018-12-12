<?php

namespace SeoImageWP\Services;

if (! defined('ABSPATH')) {
    exit;
}

use SeoImage\Client\Client;

class ClientApi
{
    protected $client = null;

    public function __construct()
    {
        $this->optionService = seoimage_get_service('Option');
    }

    /**
     * @return SeoImage\Client\Client
     */
    public function getClient($apiKey = null)
    {
        if ($apiKey === null) {
            $apiKey = $this->optionService->getOption('api_key');
        }

        $options = [];
        if (SEOIMAGE_API_LOCAL) {
            $options['host'] = 'https://api-staging.seoimage.io';
        }

        if ($this->client) {
            return $this->client;
        }

        $this->client = new Client($apiKey, $options);

        return $this->client;
    }

    /**
     * @param string $apiKey
     * @return array
     */
    public function getApiKeyOwner($apiKey)
    {
        return $this->getClient($apiKey)->getResource('api_keys')->getOwner($apiKey);
    }
}
