<?php

namespace ImageSeoWP\Services;

if (!defined('ABSPATH')) {
    exit;
}

class ImageLibrary
{
    /**
     * @return int
     */
    public function getNumberImageNonOptimizeAlt()
    {
        $args = [
            'post_type'      => 'attachment',
            'posts_per_page' => -1,
            'post_status'    => ['publish', 'pending', 'future', 'private', 'inherit'],
            'post_mime_type' => 'image/jpeg,image/gif,image/jpg,image/png',
            'meta_query'     => [
                [
                    'key'     => '_wp_attachment_image_alt',
                    'value'   => '',
                    'compare' => '=',
                ],
            ],
        ];
        $query = new \WP_Query($args);

        return $query->found_posts;
    }

    /**
     * @return int
     */
    public function getTotalImages()
    {
        $args = [
            'post_type'      => 'attachment',
            'posts_per_page' => -1,
            'post_status'    => ['publish', 'pending', 'future', 'private', 'inherit'],
            'post_mime_type' => 'image/jpeg,image/gif,image/jpg,image/png',
        ];
        $query = new \WP_Query($args);

        return $query->found_posts;
    }

    /**
     * @return int
     */
    public function getNumberImageOptimizeAlt()
    {
        $args = [
            'post_type'      => 'attachment',
            'posts_per_page' => -1,
            'post_status'    => ['publish', 'pending', 'future', 'private', 'inherit'],
            'post_mime_type' => 'image/jpeg,image/gif,image/jpg,image/png',
            'meta_query'     => [
                [
                    'key'     => '_wp_attachment_image_alt',
                    'value'   => '',
                    'compare' => '!=',
                ],
            ],
        ];
        $query = new \WP_Query($args);

        return $query->found_posts;
    }

    /**
     * @param int $numberImagesNonOptimize
     * @param int $base
     *
     * @return int
     */
    public function getPercentLooseTraffic($numberImagesNonOptimize, $base = 100)
    {
        $trafficImages = $base / 5;

        $percent = ($numberImagesNonOptimize * 100) / $trafficImages;

        return round($percent, 2);
    }

    /**
     * @return int
     */
    public function getImagesNeedByMonth()
    {
        return round($this->getTotalImages() / 12);
    }
}
