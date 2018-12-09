<?php

namespace SeoImageWP\Helpers;

if (! defined('ABSPATH')) {
    exit;
}

abstract class AltTagsSeoImage
{

    /**
     * @var string
     */
    const ALT_AUTO = '%alt_auto%';

    const SITE_TITLE = '%site_title%';

    const POST_TITLE = '%post_title%';

    /**
     * Get tabs constant
     *
     * @since 2.0
     * @static
     * @return array
     */
    public static function getTags()
    {
        return [
            self::SETTINGS,
            self::SETTINGS_ALT
        ];
    }
}
