<?php

namespace SeoImageWP\Services;

if (! defined('ABSPATH')) {
    exit;
}

use SeoImage\Client\Client;

class ReportImageSeoImage
{
    public function __construct()
    {
        $this->optionService = seoimage_get_service('OptionSeoImage');
        $this->clientService = seoimage_get_service('ClientSeoImage');
    }

    public function getReportForImage($file)
    {
        $options = [];
        if (SEOIMAGE_API_LOCAL) {
            $options['host'] = 'http://localhost:3001';
        }

        $reportImages = $this->clientService->getClient()->getResource('report_images');
        $reportImages->generate($file);
    }
}
