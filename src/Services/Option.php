<?php

namespace ImageSeoWP\Services;

if (!defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Helpers\AltTags;
use ImageSeoWP\Helpers\RenameTags;

class Option
{
    /**
     * @var array
     */
    protected $optionsDefault = [
        'api_key'                      => '',
        'allowed'                      => false,
        'active_alt_write_upload'      => 1,
        'active_rename_write_upload'   => 1,
        'active_alt_write_with_report' => 1,
        'default_language_ia'          => 'en',
        'alt_value'                    => AltTags::ALT_AUTO_CONTEXT . ' ' . AltTags::ALT_AUTO_REPRESENTATION,
        'rename_delimiter'             => '-',
        'rename_value'                 => RenameTags::ALT_AUTO_CONTEXT . '-' . RenameTags::ALT_AUTO_REPRESENTATION,
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
