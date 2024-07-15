<?php

namespace ImageSeoWP\Actions;

defined( 'ABSPATH' ) or die( 'Cheatin&#8217; uh?' );

class VendorProtect {
	public function hooks() {
		add_action( 'init', array( $this, 'protect_vendor_files' ) );
	}

	public function protect_vendor_files() {
		$restricted = array(
			'wp-content/plugins/imageseo/vendor/',
		);

		$requested = $_SERVER['REQUEST_URI'];

		foreach ( $restricted as $file ) {
			if ( strpos( $requested, $file ) !== false ) {
				status_header( 403 );
				exit;
			}
		}
	}
}
