<?php

namespace ImageSeoWP\Helpers;

if (! defined('ABSPATH')) {
    exit;
}

abstract class RenameTags extends AltTags
{

    /**
     * @var string
     */
    const ATTACHED_POST_TITLE = '%attached_post_title%';

    /**
     * Get tags constant
     * @static
     * @return array
     */
    // public static function getTags()
    // {
    //     return array_merge([
    //         self::ATTACHED_POST_TITLE
    //     ], parent::getTags());
    // }
}
