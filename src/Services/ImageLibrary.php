<?php

namespace ImageSeoWP\Services;

if (!defined('ABSPATH')) {
    exit;
}

class ImageLibrary
{
    /**
     * @param int $numberImagesNonOptimize
     * @param int $base
     *
     * @return int
     */
    public function getPercentLooseTraffic($numberImagesNonOptimize, $base = 100)
    {
        $percent = ($numberImagesNonOptimize * 100) / $base;

        return round($percent / 5, 2);
    }

    /**
     * @return int
     */
    public function getImagesNeedByMonth()
    {
        return round(imageseo_get_service('QueryImages')->getTotalImages() / 12);
    }

    public function getEstimatedByImagesHuman($numberImages, $type = 'minutes')
    {
        $timeEstimatedByImage = 90;
        $seconds = $numberImages * $timeEstimatedByImage;
        switch ($type) {
            case 'minutes':
            default:
                return round($seconds / 60);
            case 'seconds':
                return $seconds;
        }
    }

    public function getStringEstimatedImages($numberImages)
    {
        $seconds = $this->getEstimatedByImagesHuman($numberImages, 'seconds');
        $time = $seconds / 3;

        $mins = floor($time / 60 % 60);
        $secs = floor($time % 60);

        return sprintf(__('%s minutes and %s seconds', 'imageseo'), $mins, $secs);
    }
}
