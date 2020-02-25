<?php

namespace ImageSeoWP\Async;

class QueryImagesNoAltBackgroundProcess extends WPBackgroundProcess
{
    protected $action = 'imageseo_query_images_no_alt_background_process';

    /**
     * Task.
     *
     * @param mixed $item Queue item to iterate over
     *
     * @return mixed
     */
    protected function task($item)
    {
        imageseo_get_service('QueryImages')->getNumberImageNonOptimizeAlt([
            'forceQuery'=> true,
            'withCache' => false,
        ]);

        delete_transient('imageseo_process_query_count_images');

        return false;
    }
}
