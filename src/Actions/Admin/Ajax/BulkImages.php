<?php

namespace ImageSeoWP\Actions\Admin\Ajax;

if (!defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Exception\NoRenameFile;

class BulkImages
{
    public function __construct()
    {

        $this->tagsToStringService = imageseo_get_service('TagsToString');
        $this->renameFileService = imageseo_get_service('RenameFile');
        $this->altService = imageseo_get_service('Alt');
    }

    public function hooks()
    {

		add_action('wp_ajax_imageseo_start_bulk', [$this, 'start']);

    }

    public function start()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'code' => 'not_authorized',
            ]);
            exit;
        }

        if (!isset($_POST['data'])) {
            wp_send_json_error([
                'code' => 'missing_parameters',
            ]);

            return;
        }

		$data = explode(',', $_POST['data']);
		$settings = [
            'total_images'         => count($data),
            'id_images'            => $data,
            'id_images_optimized'  => [],
			'current_index_image'  => 0,
			'size_indexes_image'   => apply_filters('imageseo_size_indexes_image_bulk_process', 10),
            'settings'             => [
                'formatAlt'          => $_POST['formatAlt'],
                'formatAltCustom'    => $_POST['formatAltCustom'],
                'language'           => $_POST['language'],
                'optimizeAlt'        => 'true' === $_POST['optimizeAlt'] ? true : false,
                'optimizeFile'       => 'true' === $_POST['optimizeFile'] ? true : false,
                'wantValidateResult' => 'true' === $_POST['wantValidateResult'] ? true : false,
            ],
        ];
        update_option('_imageseo_bulk_process_settings', $settings);

		as_schedule_single_action( time(), 'action_bulk_image_process_action_scheduler', ["current_index_image" => 0], "group_bulk_image");

        wp_send_json_success();
    }

}
