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
        $this->uploadImageServices = imageseo_get_service('UploadImage');
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
        // error_log('WP INSERT POST : ' . $post_id);
        // if (wp_is_post_revision($post_id)) {
        //     return;
        // }

        // $this->process->push_to_queue([
        //     'id' => $post_id,
        // ]);

        // $this->process->save()->dispatch();
    }
}
