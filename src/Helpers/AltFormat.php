<?php

namespace ImageSeoWP\Helpers;

if (!defined('ABSPATH')) {
    exit;
}

abstract class AltFormat
{
    /**
     * @var string
     */
    const ALT_SIMPLE = '[keyword_1] - [keyword_2]';

    /**
     * @var string
     */
    const ALT_POST_TITLE = '[post_title] - [keyword_1]';

    /**
     * @var string
     */
    const ALT_SITE_TITLE = '[site_title] - [keyword_1]';

    /**
     * @var string
     */
    const ALT_PRODUCT_WOOCOMMERCE = '[product_title] - [keyword_1]';

    /**
     * @static
     *
     * @return array
     */
    public static function getFormats()
    {
        return apply_filters('imageseo_alt_formats', [
            self::ALT_SIMPLE,
            self::ALT_POST_TITLE,
            self::ALT_SITE_TITLE,
            self::ALT_PRODUCT_WOOCOMMERCE,
        ]);
    }
}
