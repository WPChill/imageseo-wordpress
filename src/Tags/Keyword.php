<?php

namespace ImageSeoWP\Tags;

if (!defined('ABSPATH')) {
    exit;
}

class Keyword
{
    const NAME = 'keyword_X';

    public function getValue($params = null)
    {
        if (null === $params) {
            return '';
        }
        list($attachmentId) = $params;
        $numberAlt = (isset($params['number'])) ? $params['number'] : 1;

        $report = imageseo_get_service('ReportImage')->getReportByAttachmentId($attachmentId);
        if (!$report) {
            return '';
        }

        $i = 1;
        $str = '';
        foreach ($report['alts'] as $alt) {
            if ($i > $numberAlt) {
                break;
            }
            if (empty($alt['name'])) {
                continue;
            }

            if ($i < $numberAlt) {
                ++$i;
                continue;
            }
            $str = $alt['name'];
            ++$i;
        }

        return $str;
    }
}
