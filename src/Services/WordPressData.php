<?php

namespace ImageSeoWP\Services;

if (!defined('ABSPATH')) {
    exit;
}

class WordPressData
{
    public function getBlacklistPostTypesSocialMedia()
    {
        return apply_filters('imageseo_blacklist_post_types_social_media', [
            'attachment',
            'seopress_404',
            'elementor_library',
        ]);
    }

    public function getAllPostTypesSocialMedia()
    {
        $args = [
            'public' => true,
        ];

        $postTypes = get_post_types($args, 'objects');
        foreach ($this->getBlacklistPostTypesSocialMedia() as $value) {
            if (array_key_exists($value, $postTypes)) {
                unset($postTypes[$value]);
            }
        }

        return apply_filters('imageseo_get_all_post_types', $postTypes);
    }
}
