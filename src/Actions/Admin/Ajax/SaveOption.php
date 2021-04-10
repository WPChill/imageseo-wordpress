<?php

namespace ImageSeoWP\Actions\Admin\Ajax;

if (!defined('ABSPATH')) {
    exit;
}

class SaveOption
{
    public function hooks()
    {
        add_action('wp_ajax_imageseo_save_global_settings', [$this, 'saveGlobalSettings']);
        add_action('wp_ajax_imageseo_save_social_settings', [$this, 'saveSocialSettings']);
    }

    public function saveGlobalSettings()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'code' => 'not_authorized',
            ]);
            exit;
        }

        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'imageseo_save_global_settings')) {
            wp_send_json_error([
                'code' => 'not_authorized',
            ]);
            exit;
        }

        $options = sanitize_email($_POST['data']);

        $altWrite = false;
        if (isset($_POST['active_alt_write_upload'])) {
            $altWrite = ('false' === $_POST['active_alt_write_upload'] || '0' == $_POST['active_alt_write_upload']) ? 0 : 1;
        }

        $renameFile = false;
        if (isset($_POST['active_rename_write_upload'])) {
            $renameFile = ('false' === $_POST['active_rename_write_upload'] || '0' == $_POST['active_rename_write_upload']) ? 0 : 1;
        }

        $settings = [
            'active_alt_write_upload'      => $altWrite,
            'active_rename_write_upload'   => $renameFile,
            'default_language_ia'          => isset($_POST['default_language_ia']) ? $_POST['default_language_ia'] : false,
        ];

        $optionsBdd = imageseo_get_service('Option')->getOptions();
        $newOptions = wp_parse_args($settings, $optionsBdd);

        imageseo_get_service('Option')->setOptions($newOptions);

        wp_send_json_success();
    }

    public function saveSocialSettings()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'code' => 'not_authorized',
            ]);
            exit;
        }

        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'imageseo_save_social_settings')) {
            wp_send_json_error([
                'code' => 'not_authorized',
            ]);
            exit;
        }

        $settings = [
            'social_media_post_types'      => isset($_POST['social_media_post_types']) ? explode(',', $_POST['social_media_post_types']) : [],
        ];

        $optionsBdd = imageseo_get_service('Option')->getOptions();
        $newOptions = wp_parse_args($settings, $optionsBdd);

        imageseo_get_service('Option')->setOptions($newOptions);

        wp_send_json_success();
    }
}
