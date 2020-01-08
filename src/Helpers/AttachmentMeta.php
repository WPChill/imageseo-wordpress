<?php

namespace ImageSeoWP\Helpers;

if (!defined('ABSPATH')) {
    exit;
}

abstract class AttachmentMeta
{
    /**
     * @var string
     */
    const DATE_REPORT = '_imageseo_date_report';

    /**
     * @var string
     */

    const REPORT = '_imageseo_report';

    const LANGUAGE = '_imageseo_language_report';
}
