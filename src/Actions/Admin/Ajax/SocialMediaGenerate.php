<?php

namespace ImageSeoWP\Actions\Admin\Ajax;

if (!defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Async\GenerateImageBackgroundProcess;

class SocialMediaGenerate
{
    public function __construct()
    {
        $this->imageSocialService = imageseo_get_service('ImageSocial');
        $this->process = new GenerateImageBackgroundProcess();
    }

    public function hooks()
    {
        add_action('wp_ajax_imageseo_check_current_process', [$this, 'pingCheckCurrentProcess']);
        add_action('wp_ajax_imageseo_generate_social_media', [$this, 'generate']);
    }

    public function pingCheckCurrentProcess()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'code' => 'not_authorized',
            ]);
            exit;
        }

        if (!isset($_POST['post_id'])) {
            wp_send_json_error([
                'code' => 'missing_parameters',
            ]);
            exit;
        }

        $postId = (int) $_POST['post_id'];
        $process = $this->imageSocialService->isCurrentProcess($postId);

        if ($process) {
            wp_send_json_success([
                'current_process' => true,
            ]);

            return;
        }

        $imgUrl = $this->imageSocialService->getPreviewImageUrlSocialMedia($postId);

        wp_send_json_success([
            'current_process' => false,
            'url'             => $imgUrl,
        ]);
    }

    public function generate()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'code' => 'not_authorized',
            ]);
            exit;
        }

        if (!isset($_POST['post_id'])) {
            wp_send_json_error([
                'code' => 'missing_parameters',
            ]);
            exit;
        }

        $postId = (int) $_POST['post_id'];
        $this->imageSocialService->setCurrentProcess($postId);

        $this->process->push_to_queue([
            'id' => $postId,
        ]);

        $this->process->save()->dispatch();

        wp_send_json_success();
    }
}
