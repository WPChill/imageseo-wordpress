<?php

namespace ImageSeoWP\Services;

if (! defined('ABSPATH')) {
    exit;
}


class Pinterest
{
    /**
     * @var array
     */
    protected $metas = [
        'data-pin-description' => '_imageseo_data_pin_description',
        'data-pin-url' => '_imageseo_data_pin_url',
        'data-pin-id' => '_imageseo_data_pin_id',
        'data-pin-media' => '_imageseo_data_pin_media',
    ];

    /**
     * Get options default
     * @return array
     */
    public function getDataPinterestByAttachmentId($attachmentId)
    {
        $data = [];
        foreach ($this->metas as $key => $meta) {
            $data[$key] = get_post_meta($attachmentId, $meta, true);
        }
        return $data;
    }
}
