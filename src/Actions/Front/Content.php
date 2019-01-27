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
    }

    /**
     * @return void
     */
    public function hooks()
    {
        if (!imageseo_allowed()) {
            return;
        }

        if (!$this->optionServices->getOption('active_alt_rewrite')) {
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
            $altSave = $this->reportImageServices->getAlt($id);
            if (empty($altSave)) {
                continue;
            }

            preg_match_all($regexImg, $contents[$key], $matches);
            $alt = $matches[3][0];

            if (!empty($alt)) {
                continue;
            }

            $replaceContent = $matches[0][0];
            $imgReplace = str_replace('alt=""', 'alt="' . $altSave . '"', $replaceContent);

            $contentFilter = str_replace($replaceContent, $imgReplace, $contentFilter);
        }

        return apply_filters('imageseo_the_content_alt', $contentFilter);
    }
}