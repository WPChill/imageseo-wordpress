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
        } elseif (is_plugin_active('wp-seopress/seopress.php')) {
            $this->compatibilitySEOPress();

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

    public function compatibilitySEOpress()
    {
        add_filter('seopress_social_og_thumb', [$this, 'replaceOG']);
        add_filter('seopress_social_twitter_card_thumb', [$this, 'replaceTwitter']);
        add_filter('seopress_social_twitter_card_summary', [$this, 'replaceTwitterCard']);
    }

    public function replaceOG($html)
    {
        $regexIMG = "#(\"|\')og:image(\"|\') content=(\"|\')(?<imgUrl>[\s\S]*)(\"|\')([^\>]+?)?\/>#mU";
        preg_match_all($regexIMG, $html, $matches);
        if (empty($matches['imgUrl'])) {
            return $html;
        }

        $regexWIDTH = "#(\"|\')og:image:width(\"|\') content=(\"|\')(?<width>[\s\S]*)(\"|\')([^\>]+?)?\/>#mU";
        preg_match_all($regexWIDTH, $html, $matchesWidth);
        $regexHEIGHT = "#(\"|\')og:image:height(\"|\') content=(\"|\')(?<height>[\s\S]*)(\"|\')([^\>]+?)?\/>#mU";
        preg_match_all($regexHEIGHT, $html, $matchesHeight);

        $imgUrl = $matches['imgUrl'][0];
        $width = $matchesWidth['width'][0];
        $width = $matchesHeight['height'][0];

        $html = str_replace($imgUrl, $this->getImageUrlOpenGraph(), $html);
        $html = str_replace($width, $this->getWidthOpenGraph(), $html);
        $html = str_replace($height, $this->getHeightOpenGraph(), $html);

        return $html;
    }

    public function replaceTwitter($html)
    {
        $regexIMG = "#(\"|\')twitter:image(\"|\') content=(\"|\')(?<imgUrl>[\s\S]*)(\"|\')([^\>]+?)?\/>#mU";
        preg_match_all($regexIMG, $html, $matches);
        $imgUrl = $matches['imgUrl'][0];

        if (!empty($matches['imgUrl'])) {
            $html = str_replace($imgUrl, $this->getImageUrlOpenGraph(), $html);
        }

        $regexIMG = "#(\"|\')twitter:image:src(\"|\') content=(\"|\')(?<imgUrl>[\s\S]*)(\"|\')([^\>]+?)?\/>#mU";
        preg_match_all($regexIMG, $html, $matches);
        $imgUrl = $matches['imgUrl'][0];
        if (!empty($matches['imgUrl'])) {
            $html = str_replace($imgUrl, $this->getImageUrlOpenGraph(), $html);
        }

        return $html;
    }

    public function replaceTwitterCard($html)
    {
        $regexTwitterCard = "#(\"|\')twitter:card(\"|\') content=(\"|\')(?<card>[\s\S]*)(\"|\')([^\>]+?)?\/>#mU";
        preg_match_all($regexTwitterCard, $html, $matches);

        $card = $matches['card'][0];
        if (!empty($matches['card'])) {
            $html = str_replace($card, 'summary_large_image', $html);
        }

        return $html;
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

        $metadata = wp_get_attachment_metadata($id);
        $url = wp_get_attachment_image_url($id, 'full');

        if (isset($metadata['last_updated'])) {
            return sprintf('%s?last_updated=%s', $url, $metadata['last_updated']);
        }

        return $url;
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
