<?php

namespace ImageSeoWP\Actions\Admin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * @since 1.0.0
 */
class Option
{
    /**
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->optionServices = imageseo_get_service('Option');
        $this->clientServices = imageseo_get_service('ClientApi');
    }

    public function hooks()
    {
        add_action('admin_init', [$this, 'optionsInit']);
        add_action('admin_notices', [$this, 'settingsNoticesSuccess']);
    }

    public function settingsNoticesSuccess()
    {
        if (false !== get_transient('imageseo_success_settings')) {
            delete_transient('imageseo_success_settings');
        } else {
            return;
        } ?>
		<div class="notice notice-success">
			<p><?php _e('Your settings have been saved.', 'imageseo'); ?></p>
		</div>
    	<?php
    }

    /**
     * Activate plugin.
     */
    public function activate()
    {
        update_option('imageseo_version', IMAGESEO_VERSION);
        $options = $this->optionServices->getOptions();

        $this->optionServices->setOptions($options);
    }

    /**
     * Register setting options.
     *
     * @see admin_init
     */
    public function optionsInit()
    {
        register_setting(IMAGESEO_OPTION_GROUP, IMAGESEO_SLUG, [$this, 'sanitizeOptions']);
    }

    /**
     * Callback register_setting for sanitize options.
     *
     * @param array $options
     *
     * @return array
     */
    public function sanitizeOptions($options)
    {
        if (!isset($_POST['action']) || ('update' !== $_POST['action'] && 'imageseo_social_media_settings_save' !== $_POST['action'] && 'imageseo_valid_api_key' !== $_POST['action'])) {
            return $options;
        }

        $optionsBdd = $this->optionServices->getOptions();
        $newOptions = wp_parse_args($options, $optionsBdd);

        switch ($_POST['action']) {
            case 'update':
                $newOptions['active_alt_write_upload'] = isset($options['active_alt_write_upload']) ? 1 : 0;
                $newOptions['active_rename_write_upload'] = isset($options['active_rename_write_upload']) ? 1 : 0;
                $newOptions['default_language_ia'] = isset($options['default_language_ia']) ? $options['default_language_ia'] : 'en';
                $newOptions['social_media_post_types'] = isset($options['social_media_post_types']) ? $options['social_media_post_types'] : [];
                $newOptions['social_media_type'] = isset($options['social_media_type']) ? $options['social_media_type'] : [];

                set_transient('imageseo_success_settings', 1, 60);
                break;
            case 'imageseo_valid_api_key':
                $newOptions['api_key'] = isset($_POST['api_key']) ? sanitize_text_field($_POST['api_key']) : $options['api_key'];
                break;
            case 'imageseo_social_media_settings_save':
                $keys = ['layout',
                    'textColor',
                    'contentBackgroundColor',
                    'textAlignment',
                    'starColor',
                    'defaultBgImg',
                    'logoUrl',
                ];

                $keysBool = ['visibilitySubTitle', 'visibilitySubTitleTwo', 'visibilityAvatar', 'visibilityRating'];

                foreach ($keys as $key) {
                    $newOptions['social_media_settings'][$key] = isset($_POST[$key]) ? sanitize_text_field($_POST[$key]) : $options['social_media_settings'][$key];
                }

                foreach ($keysBool as $key) {
                    $newOptions['social_media_settings'][$key] = isset($_POST[$key]) && 'true' === $_POST[$key] ? true : false;
                }

                break;
        }

        return $newOptions;
    }
}
