<?php

namespace ImageSeoWP\Actions\Admin;

use ImageSeoWP\Admin\SettingsPage;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.0.0
 */
class Option {
	/**
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->optionServices = imageseo_get_service( 'Option' );
		$this->clientServices = imageseo_get_service( 'ClientApi' );
	}

	public function hooks() {
		add_action( 'admin_init', [ $this, 'optionsInit' ] );
		add_action( 'admin_notices', [ $this, 'settingsNoticesSuccess' ] );
	}

	public function settingsNoticesSuccess() {
		if ( false !== get_transient( 'imageseo_success_settings' ) ) {
			delete_transient( 'imageseo_success_settings' );
		} else {
			return;
		} ?>
		<div class="notice notice-success">
			<p><?php esc_html_e( 'Your settings have been saved.', 'imageseo' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Activate plugin.
	 */
	public function activate() {
		update_option( 'imageseo_version', IMAGESEO_VERSION );
		$options = $this->optionServices->getOptions();

		$this->optionServices->setOptions( $options );
	}

	/**
	 * Register setting options.
	 *
	 * @see admin_init
	 */
	public function optionsInit() {
		register_setting( IMAGESEO_OPTION_GROUP, IMAGESEO_SLUG, [ $this, 'sanitizeOptions' ] );
	}

	/**
	 * Callback register_setting for sanitize options.
	 *
	 * @param array $options
	 *
	 * @return array
	 */
	public function sanitizeOptions( $options ) {
		// Verify nonce
		if ( ! isset( $_POST['_wpnonce'] ) && ! wp_verify_nonce( $_POST['_nonce'], IMAGESEO_OPTION_GROUP . '-options' ) ) {
			wp_die( esc_html__( 'Cheatin&#8217; uh?', 'imageseo' ) );
		}
		if ( ! isset( $_POST['action'] ) || ( 'update' !== $_POST['action'] && 'imageseo_social_media_settings_save' !== $_POST['action'] && 'imageseo_valid_api_key' !== $_POST['action'] ) ) {
			return $options;
		}

		$optionsBdd = $this->optionServices->getOptions();
		$newOptions = wp_parse_args( $options, $optionsBdd );

		switch ( $_POST['action'] ) {
			case 'update':
				$settings_page   = SettingsPage::get_instance();
				$current_tab     = $_POST['imageseo-tab'];
				$current_section = $_POST['imageseo-section'];
				$current_fields  = $settings_page->get_tab_settings( $current_tab, $current_section );
				$missig_fields   = array();
				// Cycle through the fields and check for missing options
				foreach ( $current_fields as $field ) {
					// Check to see if it's a missing field or a missing subfield
					if ( ! isset( $options[ $field ] ) && ! isset( $options[ array_key_first( $options ) ][ $field ] ) ) {
						$missig_fields[] = $field;
					}
				}
				// Get array difference
				foreach ( $missig_fields as $option ) {
					switch ( $option ) {
						case 'active_alt_write_upload':
							$newOptions['active_alt_write_upload'] = 0;
							break;
						case 'active_rename_write_upload':
							$newOptions['active_rename_write_upload'] = 0;
							break;
						case 'default_language_ia':
							$newOptions['default_language_ia'] = 'en';
							break;
						case 'social_media_post_types':
							$newOptions['social_media_post_types'] = [];
							break;
						case 'social_media_type':
							$newOptions['social_media_type'] = [];
							break;
						case 'visibilitySubTitle':
							$newOptions['social_media_settings']['visibilitySubTitle'] = 0;
							break;
						case 'visibilitySubTitleTwo':
							$newOptions['social_media_settings']['visibilitySubTitleTwo'] = 0;
							break;
						case 'visibilityRating':
							$newOptions['social_media_settings']['visibilityRating'] = 0;
							break;
						case 'visibilityAvatar':
							$newOptions['social_media_settings']['visibilityAvatar'] = 0;
							break;
						case 'optimizeFile':
							$newOptions['optimizeFile'] = 0;
							break;
						default:
							$newOptions = apply_filters( 'imageseo_sanitize_' . $option, $newOptions, $options, $current_fields, $current_tab, $current_section );
							break;
					}
				}
				set_transient( 'imageseo_success_settings', 1, 60 );
				break;
			case 'imageseo_valid_api_key':
				$newOptions['api_key'] = isset( $_POST['api_key'] ) ? sanitize_text_field( $_POST['api_key'] ) : '';
				break;
		}

		return $newOptions;
	}
}
