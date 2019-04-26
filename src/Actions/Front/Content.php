<?php

namespace ImageSeoWP\Actions\Front;

if (! defined('ABSPATH')) {
    exit;
}

class Content
{
    public function __construct()
    {
        $this->reportImageServices = imageseo_get_service('ReportImage');
        $this->optionServices = imageseo_get_service('Option');
        $this->pinterestServices = imageseo_get_service('Pinterest');
    }

    /**
     * @return void
     */
    public function hooks()
    {
        if (!imageseo_allowed()) {
            return;
        }

        if (! apply_filters('imageseo_active_alt_rewrite', true)) {
            return;
        }

        add_filter('the_content', [ $this, 'contentAlt' ], 1);
    }

    /**
     * @return string
     */
    public function contentAlt($contentFilter)
    {
        $regex = "#<!-- wp:image([^\>]+?)?id\":(.*?)} -->([\s\S]*)<!-- \/wp:image#mU";
        preg_match_all($regex, $contentFilter, $matches);

        if (empty($matches[0])) {
            return $contentFilter;
        }

        $ids = $matches[2];
        $contents = $matches[3];

        $regexImg = "#<img([^\>]+?)?alt=(\"|\')([\s\S]*)(\"|\')([^\>]+?)?>#U";

        foreach ($ids as $key => $id) {
            $pinterest = $this->pinterestServices->getDataPinterestByAttachmentId($id);
            $altSave = $this->reportImageServices->getAlt($id);
            if (empty($altSave)) {
                continue;
            }

            preg_match_all($regexImg, $contents[$key], $matches);
            $alt = $matches[3][0];

            $strDataPinterest = '';
            foreach ($pinterest as $key => $metaPinterest) {
                if (empty($metaPinterest)) {
                    continue;
                }

                $strDataPinterest .= sprintf("%s='%s' ", $key, $metaPinterest);
            }
            $replaceContent = $matches[0][0];

            $imgReplace = str_replace('<img', '<img ' . $strDataPinterest, $replaceContent);
            $imgReplace = str_replace('alt=""', 'alt="' . $altSave . '"', $imgReplace);
            $contentFilter = str_replace($replaceContent, $imgReplace, $contentFilter);
        }

        return apply_filters('imageseo_the_content_alt', $contentFilter);
    }
}
