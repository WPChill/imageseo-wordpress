<?php

namespace ImageSeo\Client\Endpoints;

/**
 * @package ImageSeo\Client\Endpoints
 */
abstract class AbstractEndpoint
{
    public function __construct($client, $options)
    {
        $this->client = $client;
        $this->options = $options;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function getOptions()
    {
        return $this->options;
    }


    /**
     * Make the API call and return the response.
     *
     * @param string $method    Method to use for given endpoint
     * @param string $endpoint  Endpoint to hit on API
     * @param array $body       Body content of the request as array
     * @param bool $asArray     To know if we return an array or ResponseInterface
     * @return array|ResponseInterface
     * @throws ApiError
     */
    public function makeRequest($method, $endpoint, $body = [], $query = [], $asArray = true)
    {
        try {
            list($rawBody, $httpStatusCode, $httpHeader) = $this->getClient()->request(
                $method,
                $this->makeAbsUrl($endpoint),
                $query,
                $body
            );

            $array = json_decode($rawBody, true);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        if ($asArray) {
            return $array;
        }
        return [$rawBody, $httpStatusCode, $httpHeader];
    }

    /**
     * @param string $endpoint
     * @return string
     */
    protected function makeAbsUrl($endpoint)
    {
        return $this->options['host'] . $endpoint;
    }
}
