<?php

namespace ImageSeoWP\Helpers;

if (!defined('ABSPATH')) {
    exit;
}

abstract class SocialMedia
{
    const OPEN_GRAPH = [
        'name'  => 'open-graph',
        'sizes' => [
            'width'  => 1200,
            'height' => 630,
        ],
    ];

    const PINTEREST = [
        'name'  => 'pinterest',
        'sizes' => [
            'width'  => 1000,
            'height' => 1500,
        ],
    ];
    const INSTAGRAM = [
        'name'  => 'instagram',
        'sizes' => [
            'width'  => 1080,
            'height' => 1920,
        ],
    ];
    const INSTAGRAM_SQUARE = [
        'name'  => 'instagram-square',
        'sizes' => [
            'width'  => 1000,
            'height' => 1000,
        ],
    ];
}
