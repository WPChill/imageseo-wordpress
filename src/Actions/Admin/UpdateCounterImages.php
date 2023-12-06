<?php

namespace ImageSeoWP\Actions\Admin;

if (!defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Async\QueryImagesNoAltBackgroundProcess;
use ImageSeoWP\Async\QueryTotalImagesBackgroundProcess;

class UpdateCounterImages
{
	public $processQueryImagesNoAlt;
	public $processQueryTotalImages;

    public function __construct()
    {
        $this->processQueryImagesNoAlt = new QueryImagesNoAltBackgroundProcess();
        $this->processQueryTotalImages = new QueryTotalImagesBackgroundProcess();
    }

    public function hooks()
    {
        add_action('admin_post_imageseo_recount_images', [$this, 'recountImages']);
    }

    public function recountImages()
    {
        $redirectUrl = admin_url('admin.php?page=imageseo-settings');

        if (!wp_verify_nonce($_GET['_wpnonce'], 'imageseo_recount_images')) {
            wp_redirect($redirectUrl);
            exit;
        }

        if (!current_user_can('manage_options')) {
            wp_redirect($redirectUrl);
            exit;
        }

        set_transient('imageseo_process_query_count_images', true, 20);

        $this->processQueryImagesNoAlt->push_to_queue([
            'query_images_no_alt' => true,
        ]);
        $this->processQueryImagesNoAlt->save()->dispatch();

        $this->processQueryTotalImages->push_to_queue([
            'query_total_images' => true,
        ]);
        $this->processQueryTotalImages->save()->dispatch();

        wp_redirect($redirectUrl);
    }
}
