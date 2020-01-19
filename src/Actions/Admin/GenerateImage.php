<?php

namespace ImageSeoWP\Actions\Admin;

if (!defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Async\GenerateImageBackgroundProcess;

class GenerateImage
{
    public function __construct()
    {
        $this->optionServices = imageseo_get_service('Option');
        $this->process = new GenerateImageBackgroundProcess();
    }

    public function hooks()
    {
        if (!imageseo_allowed()) {
            return;
        }

        add_action('wp_insert_post', [$this, 'generateSocialMedia'], 10, 3);
    }

    public function generateSocialMedia($post_id, $post, $update)
    {
        if (wp_is_post_revision($post_id)) {
            return;
        }

        $postTypesAuthorized = $this->optionServices->getOption('social_media_post_types');
        if (!in_array($post->post_type, $postTypesAuthorized, true)) {
            return;
        }

        error_log('WP INSERT POST : ' . $post->post_type);

        // $this->process->push_to_queue([
        //     'id' => $post_id,
        // ]);

        // $this->process->save()->dispatch();
    }
}
