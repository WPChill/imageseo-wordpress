<?php

namespace ImageSeoWP\Actions\Admin\Ajax;

if (!defined('ABSPATH')) {
    exit;
}

class UploadSocialImage
{
    public function __construct()
    {
        $this->uploadImageServices = imageseo_get_service('UploadImage');
    }

    public function hooks()
    {
        add_action('wp_ajax_imageseo_upload_social_image', [$this, 'upload']);
    }

    public function upload()
    {
        error_log('Upload my image');
        $this->uploadImageServices->saveFromBase64($_POST['image'], 'coquinou');

        wp_send_json_success();
    }
}
