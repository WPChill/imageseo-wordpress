<?php

namespace ImageSeoWP\Services;

if (!defined('ABSPATH')) {
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
        if (null === $apiKey) {
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
     *
     * @return array
     */
    public function getApiKeyOwner($apiKey = null)
    {
        $response = $this->getClient($apiKey)->getResource('Projects')->getOwner();
        if (!$response['success']) {
            return null;
        }

        return $response['result'];
    }

    /**
     * @return array
     */
    public function getLanguages()
    {
        $apiKey = $this->optionService->getOption('api_key');

        $response = $this->getClient($apiKey)->getResource('Languages')->getLanguages();
        if (!$response['success']) {
            return null;
        }

        return $response['result'];
    }

    /**
     * @param array $data
     *
     * @return Image
     */
    public function generateSocialMediaImage($data)
    {
        list($body) = $this->getClient()->getResource('SocialMedia')->generateSocialMediaImage($data);

        return $body;
    }
}
