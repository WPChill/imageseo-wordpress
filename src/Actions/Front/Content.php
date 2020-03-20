<?php

namespace ImageSeoWP\Actions\Front;

if (!defined('ABSPATH')) {
    exit;
}

class Content
{
    public function __construct()
    {
        $this->reportImageService = imageseo_get_service('ReportImage');
        $this->altService = imageseo_get_service('Alt');
        $this->pinterestService = imageseo_get_service('Pinterest');
    }

    public function hooks()
    {
        if (!imageseo_allowed()) {
            return;
        }

        if (!apply_filters('imageseo_active_alt_rewrite', true)) {
            return;
        }

        add_filter('the_content', [$this, 'contentImagesAttribute'], 1);
        add_filter('wp_get_attachment_image_attributes', [$this, 'postThumbnailAttributes'], 10, 2);
    }

    public function getAttachmentIdByUrl($url)
    {
        $post_id = attachment_url_to_postid($url);

        if (!$post_id) {
            $dir = wp_upload_dir();
            $path = $url;
            if (0 === strpos($path, $dir['baseurl'] . '/')) {
                $path = substr($path, strlen($dir['baseurl'] . '/'));
            }

            if (preg_match('/^(.*)(\-\d*x\d*)(\.\w{1,})/i', $path, $matches)) {
                $url = $dir['baseurl'] . '/' . $matches[1] . $matches[3];
                $post_id = attachment_url_to_postid($url);
            }
        }

        return (int) $post_id;
    }

    public function genericContent($content)
    {
        $regex = '#<img[^>]* alt=(?:\"|\')(?<alt>([^"]*))(?:\"|\')[^>]*>#mU';

        preg_match_all($regex, $content, $matches);

        $matchesTag = $matches[0];
        $matchesAlt = $matches['alt'];
        if (empty($matchesAlt)) {
            return $content;
        }

        $regexSrc = '#<img[^>]* src=(?:\"|\')(?<src>([^"]*))(?:\"|\')[^>]*>#mU';

        foreach ($matchesAlt as $key => $alt) {
            if (!empty($alt)) {
                continue;
            }
            $contentMatch = $matchesTag[$key];

            preg_match($regexSrc, $contentMatch, $matchSrc);
            $src = $matchSrc['src'];
            if (empty($src)) {
                continue;
            }

            $attachmentId = $this->getAttachmentIdByUrl($src);
            $contentToReplace = $this->updateAltContent($contentMatch, $attachmentId);

            if ($contentMatch !== $contentToReplace) {
                $content = str_replace($contentMatch, $contentToReplace, $content);
            }
        }

        return $content;
    }

    /**
     * @param string $content
     * @param int    $attachmentId
     *
     * @return string
     */
    protected function updatePinterestContent($content, $attachmentId)
    {
        $pinterest = $this->pinterestService->getDataPinterestByAttachmentId($attachmentId);

        $strDataPinterest = '';
        foreach ($pinterest as $key => $metaPinterest) {
            if (empty($metaPinterest)) {
                continue;
            }

            $strDataPinterest .= sprintf("%s='%s' ", $key, esc_attr($metaPinterest));
        }

        if (!empty($strDataPinterest)) {
            $content = str_replace('<img', '<img ' . $strDataPinterest, $content);
        }

        return $content;
    }

    /**
     * @param string $content
     * @param int    $attachmentId
     *
     * @return string
     */
    protected function updateAltContent($content, $attachmentId)
    {
        $altSave = $this->altService->getAlt($attachmentId);

        if (!empty($altSave)) {
            $content = str_replace('alt=""', 'alt="' . $altSave . '"', $content);
        }

        return $content;
    }

    /**
     * @param string $contentFilter
     *
     * @return string
     */
    public function contentImagesAttribute($contentFilter)
    {
        $regex = "#<!-- wp:image([^\>]+?)?id\":(.*?)} -->([\s\S]*)<!-- \/wp:image#mU";
        preg_match_all($regex, $contentFilter, $matches);

        if (empty($matches[0])) {
            return $this->genericContent($contentFilter);
        }

        $ids = $matches[2];
        $contents = $matches[3];

        $regexImg = "#<img([^\>]+?)?alt=(\"|\')([\s\S]*)(\"|\')([^\>]+?)?>#U";

        foreach ($ids as $key => $id) {
            preg_match_all($regexImg, $contents[$key], $matches);
            $alt = $matches[3][0];

            $contentMatch = $matches[0][0];

            $contentReplace = $this->updatePinterestContent($contentMatch, $id);
            $contentReplace = $this->updateAltContent($contentReplace, $id);

            if ($contentMatch !== $contentReplace) {
                $contentFilter = str_replace($contentMatch, $contentReplace, $contentFilter);
            }
        }

        return apply_filters('imageseo_the_content_alt', $contentFilter);
    }

    /**
     * @param array  $attrs
     * @param object $attachment
     *
     * @return array
     */
    public function postThumbnailAttributes($attrs, $attachment)
    {
        $pinterest = $this->pinterestService->getDataPinterestByAttachmentId($attachment->ID);
        $alt = $this->altService->getAlt($attachment->ID);

        foreach ($pinterest as $key => $metaPinterest) {
            if (empty($metaPinterest)) {
                continue;
            }

            $attrs[$key] = $metaPinterest;
        }

        if (!array_key_exists('alt', $attrs) && empty($attrs['alt'])) {
            $attrs['alt'] = $alt;
        }

        return $attrs;
    }
}
