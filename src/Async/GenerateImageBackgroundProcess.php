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

                $subTitle = html_entity_decode(sprintf('%s%s', $product->get_price(), get_woocommerce_currency_symbol()));
                break;
            default:
                $subTitle = get_the_author_meta('display_name', $post->post_author);
                break;
        }

        return apply_filters('imageseo_get_sub_title_social_media', $subTitle, $post);
    }

    protected function getSubTitleTwo($post)
    {
        switch ($post->post_type) {
            case 'product':
                if (!function_exists('wc_get_product')) {
                    return '';
                }

                $product = wc_get_product($post->ID);

                $subTitle = $product->get_review_count();
                break;
            default:
                $subTitle = sprintf(__('%s min read', 'imageseo'), $this->getMinutes($post->post_content));
                break;
        }

        return apply_filters('imageseo_get_sub_title_two_social_media', $subTitle, $post);
    }

    protected function getVisibilityRating($post, $settings)
    {
        switch ($post->post_type) {
            case 'product':
                $visibility = $settings['visibilityRating'];
                break;
            default:
                $visibility = false;
                break;
        }

        return apply_filters('imageseo_get_visibility_rating', $visibility, $post);
    }

    protected function getAvatarUrl($post)
    {
        return apply_filters('imageseo_get_avatar_url', get_avatar_url($post->post_author));
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
        $subTitleTwo = $this->getSubTitleTwo($post);

        $visibilityRating = $this->getVisibilityRating($post, $settings);

        $nbGoodStars = 0;
        if ($visibilityRating && function_exists('wc_get_product')) {
            $product = wc_get_product($post->ID);
            $nbGoodStars = ceil($product->get_average_rating());
        }
        $avatarUrl = $this->getAvatarUrl($post);

        $featuredImgUrl = get_the_post_thumbnail_url($item['id'], 'full');

        if (!$featuredImgUrl) {
            $featuredImgUrl = $settings['defaultBgImg'];
        }

        set_transient(sprintf('_imageseo_filename_social_process_%s', $item['id']), 1, 20);
        foreach ($medias as $media) {
            $formatFilename = sanitize_title(sprintf('%s-%s-%s', $siteTitle, $post->post_name, $media));
            $filename = apply_filters('imageseo_filename_social_media_image', $formatFilename, $item['id'], $media);

            $result = imageseo_get_service('GenerateImageSocial')->generate($filename, [
                'title'                            => $post->post_title,
                'subTitle'                         => $subTitle,
                'subTitleTwo'                      => $subTitleTwo,
                'layout'                           => $settings['layout'],
                'textColor'                        => str_replace('#', '', $settings['textColor']),
                'contentBackgroundColor'           => str_replace('#', '', $settings['contentBackgroundColor']),
                'starColor'                        => str_replace('#', '', $settings['starColor']),
                'visibilitySubTitle'               => $settings['visibilitySubTitle'],
                'visibilitySubTitleTwo'            => $settings['visibilitySubTitleTwo'],
                'visibilityAvatar'                 => $settings['visibilityAvatar'],
                'visibilityRating'                 => $visibilityRating,
                'layout'                           => $settings['layout'],
                'textAlignment'                    => $settings['textAlignment'],
                'logoUrl'                          => $settings['logoUrl'],
                'avatarUrl'                        => $avatarUrl,
                'bgImgUrl'                         => $featuredImgUrl,
                'nbGoodStars'                      => $nbGoodStars,
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
