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

    public function getImage($id)
    {
        $image_mapper = \C_Image_Mapper::get_instance();
        $image = $image_mapper->find($id);

        if (!$image) {
            return null;
        }

        return $image;
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
     * @param int    $id
     * @param string $size
     *
     * @return string|null
     */
    public function getFilename($id, $size = 'full')
    {
        $image = $this->getImage($id);

        if (!$image) {
            return '';
        }

        if ('full' === $size && isset($image->meta_data['full']['filename'])) {
            return $image->meta_data['full']['filename'];
        } elseif ('full') {
            return $image->filename;
        }

        if (isset($image->meta_data['full']['filename'])) {
            return $image->meta_data['thumbnail']['filename'];
        }

        return sprintf('thumbs_%s', $image->filename);
    }

    /**
     * @param int    $id
     * @param string $size
     *
     * @return string|null
     */
    public function getFilepath($id, $size = 'full')
    {
        $image = $this->getImage($id);

        $storage = \C_Gallery_Storage::get_instance();

        if (!$image) {
            return '';
        }

        if ('thumbnail' === $size) {
            return $storage->get_image_abspath($image, 'thumbs');
        }

        return $storage->get_image_abspath($image);
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
