<?php

namespace ImageSeo\Client;

use ImageSeo\Client\HttpClient\ClientInterface;
use ImageSeo\Client\HttpClient\CurlClient;
use ImageSeo\Client\Resources;

/**
 * @package ImageSeo\Client
 */
class Client
{
    use Resources;

    /**
     * ImageSeo API Key
     *
     * @var string
     */
    protected $apiKey;

    /**
     * Options for client
     *
     * @var array
     */
    protected $options;

    /**
     * Http Client
     *
     * @var ClientInterface
     */
    protected $httpClient;

    /**
     * Client constructor.
     * @param string    $apiKey     your ImageSeo API key
     * @param array     $options    an array of options, currently only "host" is implemented
     */
    public function __construct($apiKey, $options = [])
    {
        $this->apiKey = $apiKey;
        $options['apiKey'] = $apiKey;
        $this
            ->setHttpClient(null, [
                'Authorization:' . $this->apiKey
            ])
            ->setOptions($options)
            ->loadResources();
    }

    /**
     * Default options values
     *
     * @return array
     */
    public function defaultOptions()
    {
        return [
            'host'  => 'https://api.imageseo.io'
        ];
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions($options)
    {
        // merging default options with user options
        $this->options = array_merge($this->defaultOptions(), $options);
        return $this;
    }

    /**
     * @param null|ClientInterface $httpClient
     * @param null|string $customHeader
     * @return $this
     */
    public function setHttpClient($httpClient = null, $defaultHeaders = [])
    {
        if ($httpClient === null) {
            $httpClient = new CurlClient([], $defaultHeaders);
        }

        if ($httpClient instanceof ClientInterface) {
            $this->httpClient = $httpClient;
        }

        return $this;
    }

    /**
     * @return ClientInterface
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }
}
