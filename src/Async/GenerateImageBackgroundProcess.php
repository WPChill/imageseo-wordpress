<?php

namespace ImageSeoWP\Async;

class GenerateImageBackgroundProcess extends WPBackgroundProcess
{
    protected $action = 'imageseo_generate_image_background_process';

    /**
     * Task.
     *
     * @param mixed $item Queue item to iterate over
     *
     * @return mixed
     */
    protected function task($item)
    {
        error_log(serialize($item));
        $post = get_post($item['id']);
        if (!$post) {
            return;
        }

        $word = str_word_count(strip_tags($post->post_content));
        $minutes = floor($word / 200);

        imageseo_get_service('GenerateImageSocial')->generate($post->post_title, [
            'title' => $post->post_title,
        ]);

        return false;
    }

    protected function complete()
    {
        parent::complete();
    }
}
