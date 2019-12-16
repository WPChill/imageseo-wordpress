<?php

namespace ImageSeoWP\Services\Bulk;

if (!defined('ABSPATH')) {
    exit;
}

class FiltersSpecification
{
    const WOO_PRODUCT_IMAGE = 'WOO_PRODUCT_IMAGE';
    const WIDTH_SIZE = 'WIDTH_SIZE';
    const FILENAME = 'FILENAME';
    const HEIGHT_SIZE = 'HEIGHT_SIZE';

    const EQUALS = 'EQUALS';
    const CONTAINS = 'CONTAINS';
    const NOT_EQUALS = 'NOT_EQUALS';
    const NOT_CONTAINS = 'NOT_CONTAINS';
    const GREATER = 'GREATER';
    const LESSER = 'LESSER';
    const GREATER_THAN = 'GREATER_THAN';
    const LESSER_THAN = 'LESSER_THAN';

    public function getMetas()
    {
        $metas = [
            [
                'id'         => self::FILENAME,
                'name'       => __('Filename', 'imageseo'),
                'conditions' => [
                    self::CONTAINS,
                    self::NOT_CONTAINS,
                    self::EQUALS,
                    self::NOT_EQUALS,
                ],
            ],
            [
                'id'         => self::WIDTH_SIZE,
                'name'       => __('Width', 'imageseo'),
                'conditions' => [
                    self::GREATER,
                    self::GREATER_THAN,
                    self::LESSER,
                    self::LESSER_THAN,
                    self::EQUALS,
                    self::NOT_EQUALS,
                ],
            ],
            [
                'id'         => self::HEIGHT_SIZE,
                'name'       => __('Height', 'imageseo'),
                'conditions' => [
                    self::GREATER,
                    self::GREATER_THAN,
                    self::LESSER,
                    self::LESSER_THAN,
                    self::EQUALS,
                    self::NOT_EQUALS,
                ],
            ],
        ];

        if (
            is_plugin_active('woocommerce/woocommerce.php')
        ) {
            $metas[] = [
                'id'         => self::WOO_PRODUCT_IMAGE,
                'name'       => __('Only WooCommerce product images', 'imageseo'),
                'conditions' => [],
            ];
        }

        return apply_filters('imageseo_bulk_filters_metas', $metas);
    }

    public function getConditions()
    {
        return apply_filters('imageseo_bulk_filters_condition', [
            self::EQUALS            => [
                'name' => __('Equals', 'imageseo'),
            ],
            self::CONTAINS          => [
                'name' => __('Contains', 'imageseo'),
            ],
            self::NOT_EQUALS        => [
                'name' => __('Not equals', 'imageseo'),
            ],
            self::NOT_CONTAINS      => [
                'name' => __('Not contains', 'imageseo'),
            ],
            self::GREATER           => [
                'name' => 'Greater (>)',
            ],
            self::LESSER              => [
                'name' => 'Less (<)',
            ],
            self::GREATER_THAN      => [
                'name' => 'Greater than (>=)',
            ],
            self::LESSER_THAN       => [
                'name' => 'Lesser than (<=)',
            ],
        ]);
    }
}
