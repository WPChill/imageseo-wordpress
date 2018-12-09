<?php

namespace SeoImageWP\Actions\Admin;

if (! defined('ABSPATH')) {
    exit;
}

/**
 *
 * @since 1.0.0
 */
class OptionSeoImage
{

    /**
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->optionServices   = seoimage_get_service('OptionSeoImage');
    }

    /**
     * @return void
     */
    public function hooks()
    {
        add_action('admin_init', [ $this, 'optionsInit' ]);
    }

    /**
     * Activate plugin
     *
     * @return void
     */
    public function activate()
    {
        update_option('seoimage_version', SEOIMAGE_VERSION);
        $options = $this->optionServices->get_options();

        $this->optionServices->set_options($options);
    }

    /**
     * Register setting options
     *
     * @see admin_init
     * @since 2.0
     *
     * @return void
     */
    public function optionsInit()
    {
        register_setting(SEOIMAGE_OPTION_GROUP, SEOIMAGE_SLUG, [ $this, 'sanitizeOptions' ]);
    }

    /**
     * Callback register_setting for sanitize options
     *
     * @since 2.0
     *
     * @param array $options
     * @return array
     */
    public function sanitizeOptions($options)
    {
        $optionsBdd = $this->optionServices->getOptions();
        $newOptions = wp_parse_args($options, $optionsBdd);

        $newOptions['active_alt_rewrite'] = isset($options['active_alt_rewrite']) ? 1 : 0;

        return $newOptions;
    }
}
