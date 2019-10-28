<?php

namespace ImageSeoWP\Actions\Admin;

if (! defined('ABSPATH')) {
    exit;
}

class MediaLibraryFilters
{
    public function __construct()
    {
        $this->optionServices = imageseo_get_service('Option');
        $this->reportImageServices   = imageseo_get_service('ReportImage');
        $this->renameFileServices   = imageseo_get_service('RenameFile');
    }

    /**
     * @return void
     */
    public function hooks()
    {
        if (!imageseo_allowed()) {
            return;
        }

        add_action('restrict_manage_posts', [$this,'filtersByAlt']);
        add_action('pre_get_posts',[$this, 'applyFiltersByAlt']);
    }

    public function filtersByAlt()
    {
        $scr = get_current_screen();
        if ($scr->base !== 'upload') {
            return;
        }
    
        $isEmpty   = filter_input(INPUT_GET, 'alt_is_empty', FILTER_SANITIZE_STRING);
        $selected = (int)$isEmpty > 0 ? $isEmpty : '-1'; ?>
        <select name="alt_is_empty" id="alt_is_empty" class="">
            <option value="-1" <?php selected($selected,'-1'); ?>><?php esc_html_e('All', 'imageseo'); ?></option>
            <option value="1" <?php selected($selected,'1'); ?>><?php esc_html_e('Alt is empty', 'imageseo'); ?></option>
            <option value="2" <?php selected($selected,'2'); ?>><?php esc_html_e('Alt is not empty', 'imageseo'); ?></option>
        </select>
        <?php
    }

    public function applyFiltersByAlt($query){
        if(!is_admin()){
            return;
        }

        if(!$query->is_main_query()){
            return;
        }
        
        if (!isset($_GET['alt_is_empty']) || $_GET['alt_is_empty'] == -1) {
            return;
        }


        
        $compare = (int) $_GET['alt_is_empty'] === 1 ? '=' : '!=';

        $meta_query = [
            [
                'key' => '_wp_attachment_image_alt',
                'value' => '',
                'compare' => $compare
            ]
        ];
        $query->set( 'meta_query', $meta_query );
        
    }
}
