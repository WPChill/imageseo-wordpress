<?php

namespace ImageSeoWP\Traits;

trait Debug {

	private $debugLock = 'imageseo_bulk_optimizer_debug_lock';
	public function resetDebug() {
		update_option( $this->debugOption, array() );
	}

	public function getDebug() {
		return get_option( $this->debugOption, array() );
	}

	public function writeDebug( $value ) {
		while ( get_transient( $this->debugLock ) ) {
			usleep( 1000 );
		}
		set_transient( $this->debugLock, true, 10 );

		$currentDebug = $this->getDebug();
		if ( $this->debug ) {
			if ( is_array( $value ) ) {
				update_option(
					$this->debugOption,
					array_merge(
						$currentDebug,
						$value
					)
				);
			} else {
				$currentDebug[] = $value;
				update_option( $this->debugOption, $currentDebug );
			}
		}

		delete_transient( $this->debugLock );
	}
}
