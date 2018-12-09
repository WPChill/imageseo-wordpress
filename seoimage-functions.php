<?php

if (! defined('ABSPATH')) {
    exit;
}

use SeoImageWP\ContextSeoImage;

/**
 * Get a service
 * @since 1.0.0
 *
 * @param string $service
 * @return object
 */
function seoimage_get_service($service)
{
    return ContextSeoImage::getContext()->getService($service);
}

/**
 * Get all options
 * @since 1.0.0
 *
 * @return array
 */
function seoimage_get_options()
{
    return ContextSeoImage::getContext()->getService('OptionSeoImage')->get_options();
}

/**
 * Get option
 * @since 1.0.0
 * @param string $key
 * @return any
 */
function seoimage_get_option($key)
{
    return ContextSeoImage::getContext()->getService('OptionSeoImage')->get_option($key);
}
