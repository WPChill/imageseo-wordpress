<?php

namespace ImageSeoWP\Helpers;

if (!defined('ABSPATH')) {
    exit;
}

abstract class SocialMedia
{
    const OPEN_GRAPH = [
        'name'   => 'open-graph',
        'label'  => 'Open Graph (Twitter, Facebook, LinkedIn)',
        'sizes'  => [
            'width'  => 1200,
            'height' => 630,
        ],
    ];

    const PINTEREST = [
        'name'  => 'pinterest',
        'label' => 'Pinterest',
        'sizes' => [
            'width'  => 1000,
            'height' => 1500,
        ],
    ];
    const INSTAGRAM = [
        'name'  => 'instagram',
        'label' => 'Instagram',
        'sizes' => [
            'width'  => 1080,
            'height' => 1920,
        ],
    ];
    const INSTAGRAM_SQUARE = [
        'name'  => 'instagram-square',
        'label' => 'Instagram (Square)',
        'sizes' => [
            'width'  => 1000,
            'height' => 1000,
        ],
    ];

    public static function getSocialMedias()
    {
        return [
            self::OPEN_GRAPH,
            // self::PINTEREST,
            // self::INSTAGRAM,
            // self::INSTAGRAM_SQUARE,
        ];
    }
}
