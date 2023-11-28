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

    public function getHeaders($apiKey = null)
    {
        $headers = [
            'Content-Type' => 'application/json',
        ];

        if ($apiKey) {
            $headers['Authorization'] = $apiKey;
        } else {
            $headers['Authorization'] = $this->optionService->getOption('api_key');
        }

        return $headers;
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
    public function getOwnerByApiKey()
    {
        if (!$this->optionService->getOption('api_key')) {
            return null;
        }

        try {
            $response = wp_remote_get(IMAGESEO_API_URL . '/projects/owner', [
                'headers' => $this->getHeaders(),
                'timeout' => 50,
            ]);
            $body = json_decode(wp_remote_retrieve_body($response), true);
        } catch (\Exception $e) {
            return null;
        }

		if(!isset($body['user']) || !isset($body['project'])){
			return null;
		}

        return $body;
    }

    /**
     * @param string $apiKey
     *
     * @return array
     */
    public function validateApiKey($apiKey = null)
    {
        try {
            $headers = $this->getHeaders();
            $headers['Authorization'] = $apiKey;

            $response = wp_remote_get(IMAGESEO_API_URL . '/projects/owner', [
                'headers' => $headers,
                'timeout' => 50,
            ]);
            $body = json_decode(wp_remote_retrieve_body($response), true);
        } catch (\Exception $e) {
            return null;
        }
		
		if(!isset($body['user']) || !isset($body['project'])){
			return null;
		}

        return $body;
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
        if (!$this->optionService->getOption('api_key')) {
            return null;
        }

        list($body) = $this->getClient()->getResource('SocialMedia')->generateSocialMediaImage($data);

        return $body;
    }
}
