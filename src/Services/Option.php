<?php

namespace ImageSeoWP\Services;

if (!defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Helpers\AltFormat;

class Option
{
    /**
     * @var array
     */
    protected $optionsDefault = [
        'api_key'                       => '',
        'allowed'                       => false,
        'active_alt_write_upload'       => 0,
        'active_rename_write_upload'    => 0,
        'default_language_ia'           => 'en',
        'alt_template_default'          => AltFormat::ALT_SIMPLE,
        'social_media_post_types'       => [],
    ];

    /**
     * Get options default.
     *
     * @return array
     */
    public function getOptionsDefault()
    {
        return $this->optionsDefault;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return apply_filters(
            'imageseo_get_options',
            wp_parse_args(get_option(IMAGESEO_SLUG), $this->getOptionsDefault())
        );
    }

    /**
     * @param string $name
     *
     * @return array
     */
    public function getOption($name)
    {
        $options = $this->getOptions();
        if (!array_key_exists($name, $options)) {
            return null;
        }

        return apply_filters('imageseo_' . $name . '_option', $options[$name]);
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    public function setOptions($options)
    {
        update_option(IMAGESEO_SLUG, $options);

        return $this;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function setOptionByKey($key, $value)
    {
        $options = $this->getOptions();
        $options[$key] = $value;
        $this->setOptions($options);

        return $this;
    }
}
