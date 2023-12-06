<?php

namespace ImageSeoWP\Actions;

defined('ABSPATH') or die('Cheatin&#8217; uh?');

use ImageSeoWP\Async\QueryImagesNoAltBackgroundProcess;
use ImageSeoWP\Async\QueryTotalImagesBackgroundProcess;

class Migration
{
	protected $processQueryImagesNoAlt;
	protected $processQueryTotalImages;
    public function __construct()
    {
        $this->processQueryImagesNoAlt = new QueryImagesNoAltBackgroundProcess();
        $this->processQueryTotalImages = new QueryTotalImagesBackgroundProcess();
    }

    public function hooks()
    {
        add_action('init', [$this, 'migrate']);
    }

    public function migrate()
    {
        if (!defined('IMAGESEO_VERSION')) {
            return;
        }

        $version = get_option('imageseo_version');

        if (!$version || version_compare($version, IMAGESEO_VERSION, '<')) { // Update version
            update_option('imageseo_version', IMAGESEO_VERSION);
        }

        if (!$version || version_compare($version, '1.2.4', '<')) {
            $this->processQueryImagesNoAlt->push_to_queue([
                'query_images_no_alt' => true,
            ]);
            $this->processQueryImagesNoAlt->save()->dispatch();

            $this->processQueryTotalImages->push_to_queue([
                'query_total_images' => true,
            ]);
            $this->processQueryTotalImages->save()->dispatch();
        }
    }
}
