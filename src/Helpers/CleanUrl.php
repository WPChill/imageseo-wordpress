<?php

namespace ImageSeoWP\Helpers;

if (!defined('ABSPATH')) {
    exit;
}

abstract class CleanUrl
{
    /**
     * Maybe remove query string from URL.
     *
     * @param string $url
     *
     * @return string
     */
    public static function maybeRemoveQueryString($url)
    {
        $parts = explode('?', $url);

        return reset($parts);
    }

    /**
     * Remove scheme from URL.
     *
     * @param string $url
     *
     * @return string
     */
    public static function removeScheme($url)
    {
        return preg_replace('/^(?:http|https):/', '', $url);
    }

    /**
     * Remove size from filename (image[-100x100].jpeg).
     *
     * @param string $url
     * @param bool   $remove_extension
     *
     * @return string
     */
    public static function removeSizeFromFilename($url, $remove_extension = false)
    {
        $url = preg_replace('/^(\S+)-[0-9]{1,4}x[0-9]{1,4}(\.[a-zA-Z0-9\.]{2,})?/', '$1$2', $url);

        if ($remove_extension) {
            $ext = pathinfo($url, PATHINFO_EXTENSION);
            $url = str_replace(".$ext", '', $url);
        }

        return $url;
    }

    public static function removeDomainFromFilename($url)
    {
        // Holding place for possible future function
        $url = str_replace(self::removeScheme(get_bloginfo('url')), '', $url);

        return $url;
    }

    /**
     * Strip an image URL down to bare minimum for matching.
     *
     * @param string $url
     *
     * @return string
     */
    public static function getMatchUrl($url)
    {
        $url = self::removeScheme($url);
        $url = self::maybeRemoveQueryString($url);
        $url = self::removeSizeFromFilename($url, true);
        $url = self::removeDomainFromFilename($url);

        return $url;
    }
}
