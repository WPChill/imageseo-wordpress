<?php

namespace ImageSeoWP\Services;

if (!defined('ABSPATH')) {
    exit;
}

class QueryNextGen
{
    /**
     * @param int $id
     *
     * @return void
     */
    public function getPostIdByNextGenId($id)
    {
        global $wpdb;
        $sqlQuery = 'SELECT p.extras_post_id as id ';
        $sqlQuery .= "FROM {$wpdb->prefix}ngg_pictures p ";
        $sqlQuery .= 'WHERE 1=1 ';
        $sqlQuery .= 'AND p.pid = %d ';

        $images = $wpdb->get_results($wpdb->prepare($sqlQuery,
            $id,
        ), ARRAY_A);

        if (empty($images)) {
            return null;
        }

        return current($images)['id'];
    }

    /**
     * @param int $id
     *
     * @return void
     */
    public function getAlt($id)
    {
        $image_mapper = \C_Image_Mapper::get_instance();
        $image = $image_mapper->find($id);

        if (!$image) {
            return '';
        }

        return $image->alttext;
    }

    /**
     * @param int $id
     *
     * @return void
     */
    public function updateAlt($id, $alt)
    {
        $image_mapper = \C_Image_Mapper::get_instance();
        $image = $image_mapper->find($id);
        $image->alttext = $alt;

        $image_mapper->save($image);
    }
}
