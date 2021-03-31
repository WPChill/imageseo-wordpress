<?php

namespace ImageSeoWP\Actions\Admin\Ajax\Images;

if (!defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Helpers\Bulk\AltSpecification;

class QueryImagesBulk
{
    public function __construct()
    {
        $this->queryImages = imageseo_get_service('QueryImages');
    }

    public function hooks()
    {
        add_action('wp_ajax_imageseo_query_images', [$this, 'query']);
    }

    public function buildSqlQuery($options)
    {
        global $wpdb;
        $sqlQuery = "SELECT {$wpdb->posts}.ID ";
        $sqlQuery .= "FROM {$wpdb->posts} ";

        // == INNER JOIN
        switch ($options['alt_filter']) {
            case AltSpecification::FEATURED_IMAGE:
                $sqlQuery .= "INNER JOIN {$wpdb->postmeta} ON ( {$wpdb->posts}.ID = {$wpdb->postmeta}.meta_value AND  {$wpdb->postmeta}.meta_key = '_thumbnail_id' ) ";
                break;
        }

        switch ($options['alt_fill']) {
            case AltSpecification::FILL_ONLY_EMPTY:
                $sqlQuery .= "LEFT JOIN {$wpdb->postmeta} AS pmOnlyEmpty ON ( {$wpdb->posts}.ID = pmOnlyEmpty.post_id ) ";
                $sqlQuery .= "LEFT JOIN {$wpdb->postmeta} AS pmOnlyEmpty2 ON ({$wpdb->posts}.ID = pmOnlyEmpty2.post_id AND pmOnlyEmpty2.meta_key = '_wp_attachment_image_alt' ) ";

                break;
        }

        if ($options['only_optimized']) {
            $sqlQuery .= "INNER JOIN {$wpdb->postmeta} AS pmOptimized ON ( {$wpdb->posts}.ID = pmOptimized.post_id ) ";
        }

        // == WHERE
        $sqlQuery .= 'WHERE 1=1 ';
        if ($options['only_optimized']) {
            $sqlQuery .= "AND ( pmOptimized.meta_key = '_imageseo_report' ) ";
        }

        switch ($options['alt_fill']) {
            case AltSpecification::FILL_ONLY_EMPTY:
                $sqlQuery .= "AND (
                    ( pmOnlyEmpty.meta_key = '_wp_attachment_image_alt' AND pmOnlyEmpty.meta_value = '' )
                    OR
                    pmOnlyEmpty2.post_id IS NULL
                  )  ";
                break;
        }

        $sqlQuery .= "AND ({$wpdb->posts}.post_mime_type = 'image/jpeg' OR {$wpdb->posts}.post_mime_type = 'image/gif' OR {$wpdb->posts}.post_mime_type = 'image/jpg' OR {$wpdb->posts}.post_mime_type = 'image/png') ";
        $sqlQuery .= "AND {$wpdb->posts}.post_type = 'attachment' ";
        $sqlQuery .= "AND (({$wpdb->posts}.post_status = 'publish' OR {$wpdb->posts}.post_status = 'future' OR {$wpdb->posts}.post_status = 'pending' OR {$wpdb->posts}.post_status = 'inherit' OR {$wpdb->posts}.post_status = 'private')) ";
        $sqlQuery .= "GROUP BY {$wpdb->posts}.ID ORDER BY {$wpdb->posts}.post_date ASC ";

        return $sqlQuery;
    }

    public function buildSqlQueryWooCommerce($options)
    {
        global $wpdb;
        $sqlQuery = 'SELECT pm2.post_id as ID ';
        $sqlQuery .= "FROM {$wpdb->posts} p ";

        if ($options['only_optimized']) {
            $sqlQuery .= "INNER JOIN {$wpdb->postmeta} AS pmOptimized ON ( p.ID = pmOptimized.post_id ) ";
        }

        $sqlQuery .= "LEFT JOIN {$wpdb->postmeta} pm ON (
            pm.post_id = p.ID
            AND pm.meta_value IS NOT NULL
            AND pm.meta_key = '_thumbnail_id'
        ) ";
        $sqlQuery .= "LEFT JOIN {$wpdb->postmeta} pm2 ON (
            pm.meta_value = pm2.post_id
            AND pm2.meta_key = '_wp_attached_file'
            AND pm2.meta_value IS NOT NULL
        ) ";

        switch ($options['alt_fill']) {
            case AltSpecification::FILL_ONLY_EMPTY:
                $sqlQuery .= "LEFT JOIN {$wpdb->postmeta} AS pmOnlyEmpty2 ON (pm2.post_id = pmOnlyEmpty2.post_id AND pmOnlyEmpty2.meta_key = '_wp_attachment_image_alt' ) ";

                break;
        }

        $sqlQuery .= 'WHERE 1=1 ';
        $sqlQuery .= "AND p.post_status='publish' AND p.post_type='product' ";

        switch ($options['alt_fill']) {
            case AltSpecification::FILL_ONLY_EMPTY:
                $sqlQuery .= "AND (
                    ( pmOnlyEmpty2.meta_key = '_wp_attachment_image_alt' AND pmOnlyEmpty2.meta_value = '' )
                    OR
                    pmOnlyEmpty2.post_id IS NULL
                  )  ";
                break;
        }

        if ($options['only_optimized']) {
            $sqlQuery .= "AND ( pmOptimized.meta_key = '_imageseo_report' ) ";
        }

        $sqlQuery .= 'GROUP BY p.ID ';

        return $sqlQuery;
    }

    public function query()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'code' => 'not_authorized',
            ]);
            exit;
        }

        $filters = [];
        try {
            $filters = (isset($_POST['filters'])) ? json_decode(stripslashes($_POST['filters']), true) : [];
        } catch (\Exception $e) {
            $filters = [];
        }

        global $wpdb;

        if (AltSpecification::WOO_PRODUCT_IMAGE === $filters['alt_filter']) {
            $query = $this->buildSqlQueryWooCommerce(array_merge($filters, ['only_optimized' => false]));
            $ids = $wpdb->get_results($query, ARRAY_N);
            if (!empty($ids)) {
                $ids = call_user_func_array('array_merge', $ids);
            }

            $ids = array_merge($ids, $this->queryImages->getWooCommerceIdsGallery($filters));

            $query = $this->buildSqlQueryWooCommerce(array_merge($filters, ['only_optimized' => true]));
            $idsOptimized = $wpdb->get_results($query, ARRAY_N);
            if (!empty($idsOptimized)) {
                $idsOptimized = call_user_func_array('array_merge', $idsOptimized);
            }
        } else {
            $query = $this->buildSqlQuery(array_merge($filters, ['only_optimized' => false]));
            $ids = $wpdb->get_results($query, ARRAY_N);
            if (!empty($ids)) {
                $ids = call_user_func_array('array_merge', $ids);
            }

            $query = $this->buildSqlQuery(array_merge($filters, ['only_optimized' => true]));
            $idsOptimized = $wpdb->get_results($query, ARRAY_N);
            if (!empty($idsOptimized)) {
                $idsOptimized = call_user_func_array('array_merge', $idsOptimized);
            }
        }

        wp_send_json_success([
            'ids'           => array_values(array_filter($ids)),
            'ids_optimized' => array_values(array_filter($idsOptimized)),
        ]);
    }
}
