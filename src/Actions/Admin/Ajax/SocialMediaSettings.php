<?php

namespace ImageSeoWP\Actions\Admin\Ajax;

if (!defined('ABSPATH')) {
    exit;
}

class SocialMediaSettings
{
    public function __construct()
    {
        $this->optionServices = imageseo_get_service('Option');
    }

    public function hooks()
    {
        add_action('wp_ajax_imageseo_social_media_settings_save', [$this, 'save']);
    }

    public function save()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'code' => 'not_authorized',
            ]);
            exit;
        }

        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'social_media_settings_save')) {
            wp_send_json_error([
                'code' => 'not_authorized',
            ]);
            exit;
        }

        $socialSettings = [
            'layout'                     => sanitize_text_field($_POST['layout']),
            'textColor'                  => sanitize_text_field($_POST['textColor']),
            'contentBackgroundColor'     => sanitize_text_field($_POST['contentBackgroundColor']),
            'starColor'                  => sanitize_text_field($_POST['starColor']),
            'defaultBgImg'               => sanitize_text_field($_POST['defaultBgImg']),
            'textAlignment'              => sanitize_text_field($_POST['textAlignment']),
            'logoUrl'                    => sanitize_text_field($_POST['logoUrl']),
            'visibilitySubTitle'         => isset($_POST['visibilitySubTitle']) ? true : false,
            'visibilitySubTitleTwo'      => isset($_POST['visibilitySubTitleTwo']) ? true : false,
            'visibilityRating'           => isset($_POST['visibilityRating']) ? true : false,
            'visibilityAvatar'           => isset($_POST['visibilityAvatar']) ? true : false,
        ];

        $optionsBdd = $this->optionServices->getOptions();
        $newOptions = wp_parse_args($options, $optionsBdd);

        $newOptions['social_media_settings'] = $socialSettings;

        $this->optionServices->setOptions($newOptions);

        wp_send_json_success();
    }
}
