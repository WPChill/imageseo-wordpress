<?php

namespace ImageSeo\Client\Endpoints;

/**
 * @package ImageSeo\Client\Endpoints
 */
class Projects extends AbstractEndpoint
{
    const RESOURCE_NAME = "projects";

    /**
     * @return array
     */
    public function getOwner()
    {
        $url = sprintf('/v1/projects/owner');
        return $this->makeRequest('GET', $url);
    }
}
