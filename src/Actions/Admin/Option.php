<?php

namespace ImageSeoWP\Actions\Admin;

if (! defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Helpers\TabsAdmin;

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
        $this->optionServices   = imageseo_get_service('Option');
        $this->clientServices   = imageseo_get_service('ClientApi');
    }

    /**
     * @return void
     */
    public function hooks()
    {
        add_action('admin_init', [ $this, 'optionsInit' ]);
        add_action('admin_notices', [ $this, 'settingsNoticesSuccess']);
    }

    public function settingsNoticesSuccess()
    {
        if (get_transient('imageseo_success_settings') !== false) {
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
     * Activate plugin
     *
     * @return void
     */
    public function activate()
    {
        update_option('imageseo_version', IMAGESEO_VERSION);
        $options = $this->optionServices->getOptions();

        $this->optionServices->setOptions($options);
    }

    /**
     * Register setting options
     *
     * @see admin_init
     *
     * @return void
     */
    public function optionsInit()
    {
        register_setting(IMAGESEO_OPTION_GROUP, IMAGESEO_SLUG, [ $this, 'sanitizeOptions' ]);
    }

    /**
     * Callback register_setting for sanitize options
     *
     * @param array $options
     * @return array
     */
    public function sanitizeOptions($options)
    {
        $tab         = (isset($_POST['tab'])) ? $_POST['tab'] : null;
        $optionsBdd = $this->optionServices->getOptions();
        $newOptions = wp_parse_args($options, $optionsBdd);


        switch ($tab) {
            case TabsAdmin::SETTINGS:
                if (! empty($options['api_key'])) {
                    $result = $this->clientServices->getApiKeyOwner($options['api_key']);
                    $newOptions['allowed'] = $result['success'];
                } else {
                    $newOptions['allowed'] = false;
                }

                break;
            case TabsAdmin::SETTINGS_ALT:
                $newOptions['active_alt_write_upload'] = isset($options['active_alt_write_upload']) ? 1 : 0;
                $newOptions['active_alt_write_with_report'] = isset($options['active_alt_write_with_report']) ? 1 : 0;
                break;
            // case TabsAdmin::SETTINGS_RENAME_FILE:
            //     break;
        }

        set_transient('imageseo_success_settings', 1, 60);

        return $newOptions;
    }
}
