<?php

namespace ImageSeoWP\Actions\Admin\Notices;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class NoConfiguration  {

	/**
	 * @since 2.0
	 */
	public function __construct() {
		$this->optionServices = imageseo_get_service('Option');
	}

	/**
	 * @return void
	 */
	public function hooks() {
		$apiKey = $this->optionServices->getOption('api_key');
		if ( empty( $apiKey ) ) { 
            add_action('admin_notices', [ '\ImageSeoWP\Notices\NoConfiguration', 'admin_notice' ]);
        }
	}

}
