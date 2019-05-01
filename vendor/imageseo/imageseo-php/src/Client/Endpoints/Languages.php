<?php

namespace ImageSeo\Client\Endpoints;

use ImageSeo\Util\FileMimeTypes;

/**
 * @package ImageSeo\Client\Endpoints
 */
class Languages extends AbstractEndpoint
{
    const RESOURCE_NAME = "Languages";

    /**
     * @return array
     */
    public function getLanguages()
    {
        return $this->makeRequest('GET', '/v1/public/langs');
    }
}
