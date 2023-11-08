<?php

namespace ImageSeoWP\Actions\Admin\Ajax\Bulk;

if (!defined('ABSPATH')) {
    exit;
}

class Start
{
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

        $limitExcedeed = imageseo_get_service('UserInfo')->hasLimitExcedeed();

        if ($limitExcedeed) {
            wp_send_json_error();
        }

	    $data = explode( ',', $_POST['data'] );

	    $settings = [
		    'total_images'        => count( $data ),
		    'id_images'           => $data,
		    'id_images_optimized' => [],

		    'size_indexes_image' => apply_filters( 'imageseo_size_indexes_image_bulk_process', 5 ),
		    'settings'           => [
			    'formatAlt'          => isset( $_POST['formatAlt'] ) ? $_POST['formatAlt'] : '',
			    'formatAltCustom'    => isset( $_POST['formatAltCustom'] ) ? $_POST['formatAltCustom'] : '',
			    'altFilter'          => isset( $_POST['altFilter'] ) ? $_POST['altFilter'] : '',
			    'language'           => $_POST['language'],
			    'optimizeAlt'        => 'true' === $_POST['optimizeAlt'] ? true : false,
			    'optimizeFile'       => 'true' === $_POST['optimizeFile'] ? true : false,
			    'wantValidateResult' => 'true' === $_POST['wantValidateResult'] ? true : false,
		    ],
	    ];
	    update_option( '_imageseo_bulk_process_settings', $settings );
	    delete_option( '_imageseo_pause_bulk_process' );
	    delete_option( '_imageseo_finish_bulk_process' );
	    as_schedule_single_action( time(), 'action_bulk_image_process_action_scheduler', [], 'group_bulk_image' );

	    wp_send_json_success( $settings );
    }
}
