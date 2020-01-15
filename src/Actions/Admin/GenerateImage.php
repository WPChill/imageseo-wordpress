<?php

namespace ImageSeoWP\Actions\Admin;

if (!defined('ABSPATH')) {
    exit;
}

class GenerateImage
{
    public function __construct()
    {
        $this->uploadImageServices = imageseo_get_service('UploadImage');
    }

    public function hooks()
    {
        if (!imageseo_allowed()) {
            return;
        }

        add_action('admin_post_imageseo_generate_image', [$this, 'generateImage']);
    }

    public function generateImage()
    {
        $attachmentId = $this->uploadImageServices->saveFromBase64($_POST['image_base64'], 'test-bob');
        wp_redirect(admin_url('post.php?post=' . $attachmentId . '&action=edit'));
    }
}
