<?php

namespace ImageSeoWP\Helpers;

if (!defined('ABSPATH')) {
    exit;
}

abstract class SocialMediaSize
{
    const OPEN_GRAPH = [
        'width'  => 1200,
        'height' => 630,
    ];

    const PINTEREST = [
        'width'  => 1000,
        'height' => 1500,
    ];
    const INSTAGRAM = [
        'width'  => 1080,
        'height' => 1920,
    ];
    const INSTAGRAM_SQUARE = [
        'width'  => 1000,
        'height' => 1000,
    ];
}
