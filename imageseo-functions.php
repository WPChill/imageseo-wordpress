<?php

if (! defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Context;

/**
 * Get a service
 * @since 1.0.0
 *
 * @param string $service
 * @return object
 */
function imageseo_get_service($service)
{
    return Context::getContext()->getService($service);
}

/**
 * Get all options
 * @since 1.0.0
 *
 * @return array
 */
function imageseo_get_options()
{
    return Context::getContext()->getService('Option')->getOptions();
}

/**
 * Get option
 * @since 1.0.0
 * @param string $key
 * @return any
 */
function imageseo_get_option($key)
{
    return Context::getContext()->getService('Option')->getOption($key);
}

/**
 * @return bool
 */
function imageseo_allowed()
{
    return imageseo_get_option('allowed');
}
