<?php

namespace ImageSeoWP\Actions\Admin;

if (!defined('ABSPATH')) {
    exit;
}

class MediaLibraryFilters
{
	public $optionServices;
	public $reportImageServices;

    public function __construct()
    {
        $this->optionServices = imageseo_get_service('Option');
        $this->reportImageServices = imageseo_get_service('ReportImage');
    }

    public function hooks()
    {
        if (!imageseo_allowed()) {
            return;
        }

        add_action('restrict_manage_posts', [$this, 'filtersByAlt']);
        add_action('pre_get_posts', [$this, 'applyFiltersByAlt']);
    }

    public function filtersByAlt()
    {
        $scr = get_current_screen();
        if ('upload' !== $scr->base) {
            return;
        }

	    $isEmpty = 0;
	    if ( isset( $_GET['alt_is_empty'] ) ) {
		    $isEmpty = htmlspecialchars( $_GET['alt_is_empty'] );
	    }

        $selected = (int) $isEmpty > 0 ? $isEmpty : '-1'; ?>
        <select name="alt_is_empty" id="alt_is_empty" class="">
            <option value="-1" <?php selected($selected, '-1'); ?>><?php esc_html_e('All (alt empty or not)', 'imageseo'); ?></option>
            <option value="1" <?php selected($selected, '1'); ?>><?php esc_html_e('Alt is empty', 'imageseo'); ?></option>
            <option value="2" <?php selected($selected, '2'); ?>><?php esc_html_e('Alt is not empty', 'imageseo'); ?></option>
        </select>
        <?php
    }

    public function applyFiltersByAlt($query)
    {
        if (!is_admin()) {
            return;
        }

        if (!$query->is_main_query()) {
            return;
        }

        if (!isset($_GET['alt_is_empty']) || -1 == $_GET['alt_is_empty']) {
            return;
        }

        $compare = 1 === (int) $_GET['alt_is_empty'] ? '=' : '!=';

        $meta_query = [
            'relation' => 'OR',
            [
                'key'     => '_wp_attachment_image_alt',
                'value'   => '',
                'compare' => $compare,
            ],
        ];

        if ('=' === $compare) {
            $meta_query[] = [
                'key'     => '_wp_attachment_image_alt',
                'compare' => 'NOT EXISTS',
            ];
        }

        $query->set('meta_query', $meta_query);
    }
}
