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

        add_action('transition_post_status', [$this, 'generateSocialMedia'], 10, 3);
    }

    public function generateSocialMedia($new_status, $old_status, $post)
    {
        if (defined('REST_REQUEST') && REST_REQUEST) {
            return;
        }

        if ('publish' !== $new_status || 'publish' === $old_status) {
            return;
        }

        if (wp_is_post_revision($post->ID)) {
            return;
        }

        $postTypesAuthorized = $this->optionServices->getOption('social_media_post_types');
        if (!in_array($post->post_type, $postTypesAuthorized, true)) {
            return;
        }

        $this->process->push_to_queue([
            'id' => $post->ID,
        ]);

        $this->process->save()->dispatch();
    }
}
