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
    const ALT_YOAST_FOCUS_KW = '[yoast_focus_keyword] - [keyword_1]';

    /**
     * @var string
     */
    const ALT_SEOPRESS_TARGET_KEYWORD = '[seopress_target_keyword_1] - [keyword_1]';

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
        $formats = [
            [
                'format'      => self::ALT_SIMPLE,
                'description' => __('We use Artificial Intelligence to generate SEO friendly keywords for your alternative texts. We recommend you to use this format for SEO.', 'imageseo'),
            ],
            [
                'format'      => self::ALT_POST_TITLE,
                'description' => __('We will use your post title and generate one SEO friendly keyword.', 'imageseo'),
            ],
            [
                'format'      => self::ALT_SITE_TITLE,
                'description' => __('We will use your site title and generate one SEO friendly keyword.', 'imageseo'),
            ],
        ];

        if (
            is_plugin_active('woocommerce/woocommerce.php')
        ) {
            $formats[] = [
                'format'      => self::ALT_PRODUCT_WOOCOMMERCE,
                'description' => '',
            ];
        }

        if (
            is_plugin_active('wordpress-seo/wp-seo.php')
        ) {
            $formats[] = [
                'format'      => self::ALT_YOAST_FOCUS_KW,
                'description' => __('We will use your "Focus keyword" that you defined in your article linked to the image and generate SEO friendly keywords'),
            ];
        }

        if (
            is_plugin_active('wp-seopress/seopress.php')
        ) {
            $formats[] = [
                'format'      => self::ALT_SEOPRESS_TARGET_KEYWORD,
                'description' => __('We will use your "Target Keywords" that you defined in your article linked to the image and generate SEO friendly keywords'),
            ];
        }

        return apply_filters('imageseo_alt_formats', $formats);
    }
}
