<?php

namespace ImageSeoWP\Tags;

if (!defined('ABSPATH')) {
    exit;
}

class YoastFocusKeyword
{
    const NAME = 'yoast_focus_keyword';

    public function getValue($params)
    {
        if (null === $params) {
            return '';
        }

        if (!class_exists('WPSEO_Meta')) {
            return '';
        }

	    $attachmentId = absint( $params[0] );
	    $id           = imageseo_get_service( 'QueryImages' )->getPostByAttachmentId( $attachmentId );
        if (!$id) {
            return '';
        }

        $focuskw = \WPSEO_Meta::get_value('focuskw', $id);

        return $focuskw;
    }
}
