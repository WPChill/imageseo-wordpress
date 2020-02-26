<?php

namespace ImageSeoWP\Services;

if (!defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Helpers\AttachmentMeta;

class QueryImages
{
    protected $numberImageNonOptimizeAlt = 0;

    protected $totalImages = 0;

    public function getWooCommerceIdsGallery()
    {
        global $wpdb;
        $sqlQuery = "SELECT pm.meta_value
            FROM {$wpdb->posts} p 
            INNER JOIN {$wpdb->postmeta} pm ON (
                pm.post_id = p.id
                AND pm.meta_value IS NOT NULL
                AND pm.meta_key = '_product_image_gallery'
        )";

        $result = $wpdb->get_results($sqlQuery, ARRAY_N);
        if (empty($result)) {
            return [];
        }

        $ids = [];
        foreach ($result as $item) {
            $idsGallery = array_filter(explode(',', $item[0]));
            $ids = array_merge($ids, $idsGallery);
        }

        return array_unique($ids);
    }

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
    public function getNumberImageNonOptimizeAlt($options = ['withCache' => true, 'forceQuery' => false])
    {
        if (!isset($options['withCache'])) {
            $options['withCache'] = true;
        }

        if (!isset($options['forceQuery'])) {
            $options['forceQuery'] = false;
        }

        if ($this->numberImageNonOptimizeAlt && $options['withCache']) {
            return $this->numberImageNonOptimizeAlt;
        }

        if (!$options['forceQuery']) {
            $total = get_option('imageseo_get_number_image_non_optimize_alt');
            if ($total) {
                $this->numberImageNonOptimizeAlt = $total;

                return $total;
            }
        }

        global $wpdb;
        $sqlQuery = "SELECT COUNT(DISTINCT {$wpdb->posts}.ID) 
            FROM {$wpdb->posts} 
            LEFT JOIN {$wpdb->postmeta} ON ( {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id ) 
            LEFT JOIN {$wpdb->postmeta} AS mt1 ON ({$wpdb->posts}.ID = mt1.post_id AND mt1.meta_key = '_wp_attachment_image_alt' ) 
            WHERE 1=1 
            AND ({$wpdb->posts}.post_mime_type = 'image/jpeg' OR {$wpdb->posts}.post_mime_type = 'image/gif' OR {$wpdb->posts}.post_mime_type = 'image/jpg' OR {$wpdb->posts}.post_mime_type = 'image/png') 
            AND ( ( {$wpdb->postmeta}.meta_key = '_wp_attachment_image_alt' AND {$wpdb->postmeta}.meta_value = '' ) OR mt1.post_id IS NULL ) 
            AND {$wpdb->posts}.post_type = 'attachment' 
            AND (({$wpdb->posts}.post_status = 'publish' OR {$wpdb->posts}.post_status = 'inherit'))";

        $this->numberImageNonOptimizeAlt = (int) $wpdb->get_var(apply_filters('imageseo_sql_query_number_image_non_optimize_alt', $sqlQuery));

        update_option('imageseo_get_number_image_non_optimize_alt', $this->numberImageNonOptimizeAlt, false);

        return $this->numberImageNonOptimizeAlt;
    }

    /**
     * @return int
     */
    public function getTotalImages($options = ['withCache' => true, 'forceQuery' => false])
    {
        if (!isset($options['withCache'])) {
            $options['withCache'] = true;
        }

        if (!isset($options['forceQuery'])) {
            $options['forceQuery'] = false;
        }

        if ($this->totalImages && $options['withCache']) {
            return $this->totalImages;
        }

        if (!$options['forceQuery']) {
            $total = get_option('imageseo_get_total_images');
            if ($total) {
                $this->totalImages = $total;

                return $total;
            }
        }

        global $wpdb;

        $sqlQuery = "SELECT COUNT(DISTINCT {$wpdb->posts}.ID)  
            FROM {$wpdb->posts} 
            WHERE 1=1 
            AND ({$wpdb->posts}.post_mime_type = 'image/jpeg' OR {$wpdb->posts}.post_mime_type = 'image/gif' OR {$wpdb->posts}.post_mime_type = 'image/jpg' OR {$wpdb->posts}.post_mime_type = 'image/png') 
            AND {$wpdb->posts}.post_type = 'attachment' 
            AND (({$wpdb->posts}.post_status = 'publish' OR {$wpdb->posts}.post_status = 'inherit' ))";

        $this->totalImages = (int) $wpdb->get_var(apply_filters('imageseo_sql_query_total_images', $sqlQuery));

        update_option('imageseo_get_total_images', $this->totalImages, false);

        return $this->totalImages;
    }
}
