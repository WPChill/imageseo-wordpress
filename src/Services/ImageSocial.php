<?php

namespace ImageSeoWP\Services;

if (!defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Helpers\SocialMedia;

class ImageSocial
{
    protected $transientProcess = null;

    public function __construct()
    {
        $this->optionService = imageseo_get_service('Option');
    }

    /**
     * @param int    $postId
     * @param string $size
     */
    public function getPreviewImageUrlSocialMedia($postId, $size = 'medium')
    {
        $medias = [
            SocialMedia::OPEN_GRAPH['name'],
        ];
        foreach ($medias as $media) {
            $id = get_post_meta($postId, sprintf('_imageseo_social_media_image_%s', $media), true);
            if (!$id) {
                continue;
            }

            return wp_get_attachment_image_url($id, $size);
        }

        return false;
    }

    public function getTransientProcess()
    {
        if ($this->transientProcess) {
            return $this->transientProcess;
        }

        $this->transientProcess = get_transient('_imageseo_filename_social_process');

        return $this->transientProcess;
    }

    public function setCurrentProcess($postId)
    {
        $transient = $this->getTransientProcess();
        if (!$transient) {
            $transient = [];
        }
        $transient[$postId] = 1;

        set_transient('_imageseo_filename_social_process', $transient, 60);
    }

    /**
     * @param int $postId
     *
     * @return bool
     */
    public function isCurrentProcess($postId)
    {
        $transient = $this->getTransientProcess();
        if (!$transient) {
            return false;
        }

        return array_key_exists($postId, $transient);
    }
}
