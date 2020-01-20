<?php

namespace ImageSeoWP\Async;

class GenerateImageBackgroundProcess extends WPBackgroundProcess
{
    protected $action = 'imageseo_generate_image_background_process';

    protected function getMinutes($content)
    {
        $word = str_word_count(strip_tags($content));
        $minutes = floor($word / 200);
        if ($minutes < 1) {
            $minutes = 1;
        }

        return $minutes;
    }

    protected function getSubTitle($post)
    {
        switch ($post->post_type) {
            case 'product':
                if (!function_exists('wc_get_product')) {
                    return '';
                }

                $product = wc_get_product($post->ID);

                return html_entity_decode(sprintf('%s%s', $product->get_price(), get_woocommerce_currency_symbol()));
                break;
            default:
                return sprintf(__('%s min read', 'imageseo'), $this->getMinutes($post->post_content));
        }
    }

    protected function getVisibilityRating($post, $settings)
    {
        switch ($post->post_type) {
            case 'product':
                return $settings['visibilityRating'];
            default:
                return false;
        }
    }

    /**
     * Task.
     *
     * @param mixed $item Queue item to iterate over
     *
     * @return mixed
     */
    protected function task($item)
    {
        $post = get_post($item['id']);
        if (!$post) {
            return;
        }

        $medias = imageseo_get_service('Option')->getOption('social_media_type');
        $settings = imageseo_get_service('Option')->getOption('social_media_settings');

        $siteTitle = get_bloginfo('name');
        $subTitle = $this->getSubTitle($post);

        $visibilityRating = $this->getVisibilityRating($post, $settings);

        $nbGoodStars = 0;
        if ($visibilityRating && function_exists('wc_get_product')) {
            $product = wc_get_product($post->ID);
            $nbGoodStars = ceil($product->get_average_rating());
        }

        foreach ($medias as $media) {
            $formatFilename = sanitize_title(sprintf('%s-%s-%s', $siteTitle, $post->post_name, $media));
            $filename = apply_filters('imageseo_filename_social_media_image', $formatFilename, $item['id'], $media);
            $featuredImgUrl = get_the_post_thumbnail_url($item['id'], 'full');

            if (!$featuredImgUrl) {
                $featuredImgUrl = $settings['defaultBgImg'];
            }

            $result = imageseo_get_service('GenerateImageSocial')->generate($filename, [
                'title'                    => $post->post_title,
                'subTitle'                 => $subTitle,
                'layout'                   => $settings['layout'],
                'textColor'                => str_replace('#', '', $settings['textColor']),
                'contentBackgroundColor'   => str_replace('#', '', $settings['contentBackgroundColor']),
                'starColor'                => str_replace('#', '', $settings['starColor']),
                'visibilitySubTitle'       => $settings['visibilitySubTitle'],
                'visibilityRating'         => $visibilityRating,
                'layout'                   => $settings['layout'],
                'bgImgUrl'                 => $featuredImgUrl,
                'nbGoodStars'              => $nbGoodStars,
            ]);

            if (isset($result['attachment_id'])) {
                update_post_meta($item['id'], sprintf('_imageseo_social_media_image_%s', $media), $result['attachment_id']);
            }
        }

        return false;
    }

    protected function complete()
    {
        parent::complete();
    }
}
