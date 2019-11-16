<?php

namespace ImageSeoWP\Notices;

if (!defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Models\AbstractNotice;

class Curl extends AbstractNotice
{
    /**
     * @static
     *
     * @return string
     */
    public static function get_template_file()
    {
        return IMAGESEO_TEMPLATES_ADMIN_NOTICES . '/no-curl.php';
    }
}
