<?php

namespace ImageSeoWP\Services;

if (! defined('ABSPATH')) {
    exit;
}

use ImageSeo\Client\Client;

class ClientApi
{
    protected $client = null;

    public function __construct()
    {
        $this->optionService = imageseo_get_service('Option');
    }

    /**
     * @return ImageSeo\Client\Client
     */
    public function getClient($apiKey = null)
    {
        if ($apiKey === null) {
            $apiKey = $this->optionService->getOption('api_key');
        }

        $options = [];
        if (defined('IMAGESEO_API_URL')) {
            $options['host'] = IMAGESEO_API_URL;
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
