<?php

namespace ImageSeoWP\Tags;

if (!defined('ABSPATH')) {
    exit;
}

class PostTitle
{
    const NAME = 'post_title';

    public function getValue($params = null)
    {
        if (null === $params) {
            return '';
        }

	    $attachmentId = $params['attachmentId'];
	    $id           = imageseo_get_service( 'QueryImages' )->getPostByAttachmentId( $attachmentId );
        if (!$id) {
            return '';
        }

        $post = get_post($id);

        return $post->post_title;
    }
}
