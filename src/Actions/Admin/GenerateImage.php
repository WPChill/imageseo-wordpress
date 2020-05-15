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
        $this->imageSocialService = imageseo_get_service('ImageSocial');
        $this->process = new GenerateImageBackgroundProcess();
    }

    public function hooks()
    {
        if (!imageseo_allowed()) {
            return;
        }

        add_action('transition_post_status', [$this, 'generateSocialMedia'], 10, 3);
        add_action('admin_post_imageseo_generate_manual_social_media', [$this, 'generateSocialMediaManually']);
    }

    public function generateSocialMediaManually()
    {
        $redirectUrl = admin_url('edit.php');
        if ('post' !== $postType) {
            $redirectUrl .= '?post_type=' . $postType;
        }

        if (!wp_verify_nonce($_GET['_wpnonce'], 'imageseo_generate_manual_social_media')) {
            wp_redirect($redirectUrl);
            exit;
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'code' => 'not_authorized',
            ]);
            exit;
        }

        $postType = (isset($_GET['post_type'])) ? $_GET['post_type'] : 'post';
        $redirectUrl = admin_url('edit.php');
        if ('post' !== $postType) {
            $redirectUrl .= '?post_type=' . $postType;
        }

        $limitExcedeed = imageseo_get_service('UserInfo')->hasLimitExcedeed();
        if ($limitExcedeed) {
            wp_redirect($redirectUrl);
        }

        if (!isset($_GET['post_id'])) {
            wp_redirect($redirectUrl);

            return;
        }

        $post = get_post((int) $_GET['post_id']);
        $postTypesAuthorized = $this->optionServices->getOption('social_media_post_types');
        if (!in_array($post->post_type, $postTypesAuthorized, true)) {
            return;
        }

        $this->imageSocialService->setCurrentProcess($post->ID);

        $this->process->push_to_queue([
            'id' => $post->ID,
        ]);

        $this->process->save()->dispatch();

        wp_redirect($redirectUrl);
    }

    public function generateSocialMedia($newStatus, $oldStatus, $post)
    {
        if (defined('REST_REQUEST') && REST_REQUEST) {
            return;
        }

        if ('publish' !== $newStatus) {
            return;
        }

        if (wp_is_post_revision($post->ID)) {
            return;
        }

        $postTypesAuthorized = $this->optionServices->getOption('social_media_post_types');
        if (!in_array($post->post_type, $postTypesAuthorized, true)) {
            return;
        }

        $this->imageSocialService->setCurrentProcess($post->ID);

        $this->process->push_to_queue([
            'id' => $post->ID,
        ]);

        $this->process->save()->dispatch();
    }
}
