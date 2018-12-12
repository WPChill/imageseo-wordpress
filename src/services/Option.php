<?php

namespace SeoImageWP\Services;

if (! defined('ABSPATH')) {
    exit;
}

use SeoImageWP\Helpers\AltTagsSeoImage;

class Option
{
    /**
     * @var array
     */
    protected $optionsDefault = [
        'api_key' => '',
        'allowed' => false,
        'active_alt_rewrite' => 1,
        'alt_auto_percent' => 80,
        'alt_value' => AltTagsSeoImage::SITE_TITLE . ' - ' . AltTagsSeoImage::ALT_AUTO_CONTEXT
    ];

    /**
     * Get options default
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
            'seoimage_get_options',
            wp_parse_args(get_option(SEOIMAGE_SLUG), $this->getOptionsDefault())
        );
    }

    /**
     * @param string $name
     * @return array
     */
    public function getOption($name)
    {
        $options = $this->getOptions();
        if (! array_key_exists($name, $options)) {
            return null;
        }

        return $options[ $name ];
    }



    /**
     * @param array $options
     * @return $this
     */
    public function setOptions($options)
    {
        update_option(SEOIMAGE_SLUG, $options);
        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setOptionByKey($key, $value)
    {
        $options         = $this->getOptions();
        $options[ $key ] = $value;
        $this->setOptions($options);
        return $this;
    }
}
