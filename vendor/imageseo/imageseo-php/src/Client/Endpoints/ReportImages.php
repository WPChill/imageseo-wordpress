<?php

namespace ImageSeo\Client\Endpoints;

/**
 * @package ImageSeo\Client\Endpoints
 */
class ReportImages extends AbstractEndpoint
{
    const RESOURCE_NAME = "report_images";

    /**
     * @return string
     */
    protected function getPostRoute()
    {
        $options = $this->getOptions();

        return sprintf('/v1/projects/images');
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

        $infoFile = mime_content_type($filePath);

        $cFile = curl_file_create($filePath);
        $cFile->setMimeType($infoFile);

        $data['imageFile'] = $cFile;

        return $this->makeRequest('FILE', $this->getPostRoute(), $data, $query);
    }
}
