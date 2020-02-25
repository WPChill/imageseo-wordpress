<?php

namespace ImageSeoWP\Actions;

defined('ABSPATH') or die('Cheatin&#8217; uh?');

use ImageSeoWP\Async\QueryImagesNoAltBackgroundProcess;
use ImageSeoWP\Async\QueryTotalImagesBackgroundProcess;

class Activation
{
    public function __construct()
    {
        $this->processQueryImagesNoAlt = new QueryImagesNoAltBackgroundProcess();
        $this->processQueryTotalImages = new QueryTotalImagesBackgroundProcess();
    }

    public function activate()
    {
        $this->processCountImages();
    }

    public function processCountImages()
    {
        $this->processQueryImagesNoAlt->push_to_queue([
            'query_images_no_alt' => true,
        ]);
        $this->processQueryImagesNoAlt->save()->dispatch();

        $this->processQueryTotalImages->push_to_queue([
            'query_total_images' => true,
        ]);
        $this->processQueryTotalImages->save()->dispatch();
    }
}
