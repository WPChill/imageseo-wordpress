<?php

namespace ImageSeo\Client\Endpoints;

use ImageSeo\Util\FileMimeTypes;

/**
 * @package ImageSeo\Client\Endpoints
 */
class ImageReports extends AbstractEndpoint
{
    const RESOURCE_NAME = "ImageReports";

    /**
     * @return string
     */
    protected function getPostRoute()
    {
        $options = $this->getOptions();

        return sprintf('/v1/external/projects/images');
    }

    /**
     * @param array $data
     * @param array $query Query parameters
     * @return array
     */
    public function generateReportFromUrl($data, $query = null)
    {
        if (! isset($data['url'])) {
            throw new \Exception("Miss URL params");
        }

        return $this->makeRequest('POST', $this->getPostRoute(), $data, $query);
    }

    /**
     * @param array $data
     * @param array $query Query parameters
     * @return array
     */
    public function generateReportFromFile($data, $query = null)
    {
        if (! isset($data['filePath'])) {
            throw new \Exception("Miss filePath params");
        }

        $filePath = $data['filePath'];
        unset($data['filePath']);

        $fileMimeTypes = new FileMimeTypes($filePath);

        $mimeType = $fileMimeTypes->getMimeTypeForFile();

        $cFile = curl_file_create($filePath);
        $cFile->setMimeType($mimeType);

        $data['imageFile'] = $cFile;

        return $this->makeRequest('FILE', $this->getPostRoute(), $data, $query);
    }
}
