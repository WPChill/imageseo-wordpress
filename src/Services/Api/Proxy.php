<?php

namespace ImageSeoWP\Services\Api;

if (!defined('ABSPATH')) {
    exit;
}

class Proxy extends BaseClient
{
    /**
     * @param string     $path
     * @param array|null $query
     *
     * @return object|null
     */
    public function get($path, $query = null)
    {
        if (!imageseo_get_api_key()) {
            return null;
        }

        try {
            $url = IMAGESEO_API_URL . $path;

            if (null !== $query) {
                $url = add_query_arg($query, $url);
            }

            $response = wp_remote_get($url, [
                'headers' => $this->getHeaders(),
                'timeout' => 50,
            ]);
        } catch (\Exception $e) {
            return null;
        }

        $total = wp_remote_retrieve_header($response, 'X-Total');
        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (!$total || empty($total)) {
            return $body;
        }

        return [
            'total' => $total,
            'items' => $body,
        ];
    }

    /**
     * @return array
     */
    public function callApi($method, $path, $query = [], $data = [])
    {
        switch ($method) {
            case 'GET':
                return $this->get($path, $query);
                break;
        }
    }
}
