<?php

namespace ImageSeoWP\Tags;

if (!defined('ABSPATH')) {
    exit;
}

class SeopressTargetKeyword
{
    const NAME = 'seopress_target_keyword_X';

    public function getValue($params)
    {
        if (null === $params) {
            return '';
        }

        list($attachmentId) = $params;
        $numberKeyword = (isset($params['number'])) ? $params['number'] : 1;

        $id = imageseo_get_service('QueryImages')->getPostByAttachmentId($attachmentId);
        if (!$id) {
            return '';
        }

        $keywords = get_post_meta($id, '_seopress_analysis_target_kw', true);

        if (!$keywords) {
            return '';
        }

        $keywords = array_map('trim', explode(',', $keywords));
        $i = 1;
        $str = '';
        foreach ($keywords as $keyword) {
            if ($i > $numberKeyword) {
                break;
            }
            if (empty($keyword)) {
                continue;
            }

            if ($i < $keyword) {
                ++$i;
                continue;
            }
            $str = $keyword;
            ++$i;
        }

        return $str;
    }
}
