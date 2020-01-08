<?php

namespace ImageSeoWP\Helpers\Bulk;

if (!defined('ABSPATH')) {
    exit;
}

class FiltersSpecification
{
    const EQUALS = 'EQUALS';
    const CONTAINS = 'CONTAINS';
    const NOT_EQUALS = 'NOT_EQUALS';
    const NOT_CONTAINS = 'NOT_CONTAINS';
    const GREATER = 'GREATER';
    const LESSER = 'LESSER';
    const GREATER_THAN = 'GREATER_THAN';
    const LESSER_THAN = 'LESSER_THAN';

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
