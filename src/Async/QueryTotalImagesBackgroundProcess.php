<?php

namespace ImageSeoWP\Async;

class QueryTotalImagesBackgroundProcess extends WPBackgroundProcess
{
    protected $action = 'imageseo_query_total_images_background_process';

    /**
     * Task.
     *
     * @param mixed $item Queue item to iterate over
     *
     * @return mixed
     */
    protected function task($item)
    {
        imageseo_get_service('QueryImages')->getTotalImages([
            'forceQuery'=> true,
            'withCache' => false,
        ]);

        return false;
    }
}
