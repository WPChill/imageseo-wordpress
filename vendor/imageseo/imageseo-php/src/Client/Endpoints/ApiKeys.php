<?php

namespace ImageSeo\Client\Endpoints;

/**
 * @package ImageSeo\Client\Endpoints
 */
class ApiKeys extends AbstractEndpoint
{
    const RESOURCE_NAME = "api_keys";

    /**
     * @param string $apiKey
     * @return array
     */
    public function getOwner($apiKey)
    {
        $url = sprintf('/v1/api_keys/%s/owner', $apiKey);
        return $this->makeRequest('GET', $url);
    }
}
