<?php

namespace ImageSeoWP\Services;

if (!defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Helpers\AttachmentMeta;

class QueryImages
{
    public function getPostByAttachmentId($attachmentId)
    {
        global $wpdb;

        $sqlQuery = "SELECT {$wpdb->posts}.ID
            FROM {$wpdb->posts} 
            INNER JOIN {$wpdb->postmeta} ON ( {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id AND  {$wpdb->postmeta}.meta_key = '_thumbnail_id' ) 
            WHERE 1=1 
            AND {$wpdb->postmeta}.meta_value = '{$attachmentId}'";

        $ids = $wpdb->get_results($sqlQuery);
        if (empty($ids)) {
            return null;
        }

        return $ids[0]->ID;
    }

    public function getQueryArgsDefault()
    {
        return [
            'post_type'      => 'attachment',
            'posts_per_page' => -1,
            'post_status'    => ['publish', 'pending', 'future', 'private', 'inherit'],
            'post_mime_type' => 'image/jpeg,image/gif,image/jpg,image/png',
        ];
    }

    public function getQueryArgsIdsAttachmentOptimized()
    {
        return  array_merge($this->getQueryArgsDefault(), [
            'meta_key'       => AttachmentMeta::REPORT,
            'meta_compare'   => 'EXISTS',
            'fields'         => 'ids',
            'orderby'        => 'id',
            'order'          => 'ASC',
        ]);
    }

    public function getIdsAttachmentOptimized()
    {
        $args = $this->getQueryArgsIdsAttachmentOptimized();

        return  new \WP_Query($args);
    }

    public function getAllIdsAttachment()
    {
        $args = array_merge($this->getQueryArgsDefault(), [
            'fields'         => 'ids',
            'orderby'        => 'id',
            'order'          => 'ASC',
        ]);

        return  new \WP_Query($args);
    }

    public function getIdsAttachmentWithAltEmpty()
    {
        $args = array_merge($this->getQueryArgsDefault(), [
            'fields'         => 'ids',
            'orderby'        => 'id',
            'order'          => 'ASC',
            'meta_query'     => [
                'relation' => 'OR',
                [
                    'key'     => '_wp_attachment_image_alt',
                    'value'   => '',
                    'compare' => '=',
                ],
                [
                    'key'     => '_wp_attachment_image_alt',
                    'compare' => 'NOT EXISTS',
                ],
            ],
        ]);

        return  new \WP_Query($args);
    }

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
                'relation' => 'OR',
                [
                    'key'     => '_wp_attachment_image_alt',
                    'value'   => '',
                    'compare' => '=',
                ],
                [
                    'key'     => '_wp_attachment_image_alt',
                    'compare' => 'NOT EXISTS',
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
}
