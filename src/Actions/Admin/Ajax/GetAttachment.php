<?php

namespace ImageSeoWP\Actions\Admin\Ajax;

if (!defined('ABSPATH')) {
    exit;
}

class GetAttachment
{
    public function __construct()
    {
        $this->altService = imageseo_get_service('Alt');
    }

    public function hooks()
    {
        add_action('wp_ajax_imageseo_get_attachment', [$this, 'get']);
    }

    public function get()
    {
        if (!isset($_POST['attachmentId'])) {
            wp_send_json_error([
                'code' => 'missing_parameters',
            ]);
            exit;
        }

        $attachmentId = (int) $_POST['attachmentId'];

        $attachment = get_post($attachmentId, ARRAY_A);
        $metadata = wp_get_attachment_metadata($attachmentId);
        $attachment['metadata'] = wp_get_attachment_metadata($attachmentId);
        $attachment['thumbnail'] = wp_get_attachment_image_src($attachmentId, 'thumbnail');
        $attachment['alt'] = $this->altService->getAlt($attachmentId);

        $filename = wp_get_attachment_image_src($attachmentId, 'full');
        $pathinfo = pathinfo($filename[0]);
        $attachment['filename'] = basename($filename[0]);
        $attachment['extension'] = $pathinfo['extension'];

        wp_send_json_success($attachment);
    }
}
