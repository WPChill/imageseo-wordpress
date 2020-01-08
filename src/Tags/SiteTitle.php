<?php

namespace ImageSeoWP\Tags;

if (!defined('ABSPATH')) {
    exit;
}

class SiteTitle
{
    const NAME = 'site_title';

    public function getValue()
    {
        return get_bloginfo('name');
    }
}
