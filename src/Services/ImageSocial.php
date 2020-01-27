<?php

namespace ImageSeoWP\Services;

if (!defined('ABSPATH')) {
    exit;
}

class ImageSocial
{
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
        $medias = $this->optionService->getOption('social_media_type');

        foreach ($medias as $media) {
            $id = get_post_meta($postId, sprintf('_imageseo_social_media_image_%s', $media), true);
            if (!$id) {
                continue;
            }

            return wp_get_attachment_image_url($id, $size);
        }

        return false;
    }

    /**
     * @param int $postId
     *
     * @return bool
     */
    public function isCurrentProcess($postId)
    {
        $transient = get_transient(sprintf('_imageseo_filename_social_process_%s', $postId));

        return $transient ? true : false;
    }
}
