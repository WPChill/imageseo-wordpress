<?php

namespace ImageSeoWP\Helpers\Bulk;

if (!defined('ABSPATH')) {
    exit;
}

class AltSpecification
{
    const ALL = 'ALL';
    const WOO_PRODUCT_IMAGE = 'WOO_PRODUCT_IMAGE';
    const FEATURED_IMAGE = 'FEATURED_IMAGE';

    const FILL_ALL = 'FILL_ALL';
    const FILL_ONLY_EMPTY = 'FILL_ONLY_EMPTY';

    public static function getMetas()
    {
        $metas[] = [
            'id'          => self::ALL,
            'label'       => __('All images', 'imageseo'),
            'conditions'  => [],
        ];

        $metas[] = [
            'id'          => self::FEATURED_IMAGE,
            'label'       => __('Only featured images', 'imageseo'),
            'conditions'  => [],
        ];

        if (
            is_plugin_active('woocommerce/woocommerce.php')
        ) {
            $metas[] = [
                'id'          => self::WOO_PRODUCT_IMAGE,
                'label'       => __('Only WooCommerce product images', 'imageseo'),
                'conditions'  => [],
            ];
        }

        return apply_filters('imageseo_bulk_alt_specification', $metas);
    }

    public static function getFillType()
    {
        return [
            [
                'id'          => self::FILL_ALL,
                'label'       => __('All images', 'imageseo'),
            ],
            [
                'id'          => self::FILL_ONLY_EMPTY,
                'label'       => __('Only alt empty', 'imageseo'),
            ],
        ];
    }
}