<?php

namespace ImageSeoWP\Actions\Front;

if (!defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Helpers\SocialMedia;

class SocialMediaHead
{
    public function hooks()
    {
        if (!imageseo_allowed()) {
            return;
        }

        if (!function_exists('is_plugin_active')) {
            require_once ABSPATH . '/wp-admin/includes/plugin.php';
        }

        if (is_plugin_active('wordpress-seo/wp-seo.php')) {
            $this->compatibilityYoast();

            return;
        }

        add_action('wp_head', [$this, 'openGraph'], 1);
    }

    public function canActiveSocialMedia()
    {
        if (is_front_page()) {
            return false;
        }

        global $post;

        $postTypesAuthorized = imageseo_get_service('Option')->getOption('social_media_post_types');
        if (!in_array(get_post_type(), $postTypesAuthorized, true)) {
            return false;
        }

        return true;
    }

    public function compatibilityYoast()
    {
        add_filter('wpseo_og_og_image', [$this, 'getImageUrlOpenGraph']);
        add_filter('wpseo_og_og_image_secure_url', [$this, 'getImageUrlOpenGraph']);
        add_filter('wpseo_og_og_image_width', [$this, 'getWidthOpenGraph']);
        add_filter('wpseo_og_og_image_height', [$this, 'getHeightOpenGraph']);
        add_filter('wpseo_twitter_image', [$this, 'getImageUrlOpenGraph']);
    }

    protected function getAttachmentId($type)
    {
        global $post;

        return get_post_meta($post->ID, sprintf('_imageseo_social_media_image_%s', $type), true);
    }

    public function getImageUrlOpenGraph($url = null)
    {
        if (!$this->canActiveSocialMedia()) {
            return $url;
        }
        $id = $this->getAttachmentId(SocialMedia::OPEN_GRAPH['name']);
        if (!$id) {
            return $url;
        }

        return wp_get_attachment_image_url($id, 'full');
    }

    public function getWidthOpenGraph($width = null)
    {
        if (!$this->canActiveSocialMedia()) {
            return $width;
        }

        $id = $this->getAttachmentId(SocialMedia::OPEN_GRAPH['name']);
        if (!$id) {
            return $width;
        }

        return SocialMedia::OPEN_GRAPH['sizes']['width'];
    }

    public function getHeightOpenGraph($height = null)
    {
        if (!$this->canActiveSocialMedia()) {
            return $height;
        }

        $id = $this->getAttachmentId(SocialMedia::OPEN_GRAPH['name']);
        if (!$id) {
            return $height;
        }

        return SocialMedia::OPEN_GRAPH['sizes']['height'];
    }

    public function openGraph()
    {
        if (!$this->canActiveSocialMedia()) {
            return;
        }

        global $wp;
        echo '<meta property="og:title" content="' . wp_get_document_title() . '">';
        echo "\n";
        echo '<meta property="twitter:title" content="' . wp_get_document_title() . '">';
        echo "\n";
        if (is_singular()) {
            $desc = str_replace(' [&hellip;]', '&hellip;', wp_strip_all_tags(get_the_excerpt()));
            if ('' != $desc) {
                echo '<meta property="og:description" content="' . $desc . '">';
                echo "\n";
                echo sprintf('<meta name="twitter:description" content="%s">', $desc);
                echo "\n";
            }
        }
        $url = $this->getImageUrlOpenGraph();
        $width = $this->getWidthOpenGraph();
        $height = $this->getHeightOpenGraph();

        if (!$url) {
            return;
        }

        echo '<meta name="twitter:card" content="summary_large_image">';
        echo "\n";
        echo sprintf('<meta name="twitter:image" content="%s">', $url);
        echo "\n";
        echo sprintf('<meta property="og:image:width" content="%s">', $width);
        echo "\n";
        echo sprintf('<meta property="og:image:height" content="%s">', $height);
        echo "\n";
        echo sprintf('<meta property="og:image" content="%s">', $url);
        echo "\n";
        if (is_ssl()) {
            echo sprintf('<meta property="og:image:secure_url" content="%s">', $url);
            echo "\n";
        }
    }
}
