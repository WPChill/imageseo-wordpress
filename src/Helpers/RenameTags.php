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
}
