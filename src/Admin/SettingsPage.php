<?php

namespace ImageSeoWP\Admin;

use ImageSeoWP\Helpers\Bulk\AltSpecification;
use ImageSeoWP\Helpers\Pages;

class SettingsPage {

	public static $instance;

	public static function get_instance(): SettingsPage {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof SettingsPage ) ) {
			self::$instance = new SettingsPage();
		}

		return self::$instance;
	}

	private function __construct() {
		$this->hooks();
	}

	private function hooks() {
		add_action( 'admin_menu', array( $this, 'plugin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	public function plugin_menu() {
		add_menu_page(
			'Image SEO',
			'Image SEO',
			'manage_options',
			Pages::SETTINGS,
			[
				$this,
				'imageseo_settings'
			],
			'dashicons-imageseo-logo'
		);
	}

	public function imageseo_settings() {
		echo '<div id="imageseo-settings-v2"></div>';
	}

	/**
	 * Get settings URL
	 *
	 * @return string
	 */
	public static function get_url() {
		return admin_url( 'admin.php?page=imageseo-settings' );
	}

	public function enqueue_scripts() {
		$all_post_types     = imageseo_get_service( 'WordPressData' )->getAllPostTypesSocialMedia();
		$allowed_post_types = array();
		foreach ( $all_post_types as $post_type ) {
			$allowed_post_types[] = [
				'value' => $post_type->name,
				'label' => $post_type->label
			];
		}

		$language_codes = imageseo_get_service( 'ClientApi' )->getLanguages();

		$totalImages = imageseo_get_service( 'QueryImages' )->getTotalImages(
			[
				'withCache'  => false,
				'forceQuery' => true
			]
		);
		$totalNoAlt  = imageseo_get_service( 'QueryImages' )->getNumberImageNonOptimizeAlt(
			[
				'withCache'  => false,
				'forceQuery' => true
			]
		);

		$bulkQuery = imageseo_get_service( 'BulkOptimizerQuery' )->getImages();

		$languages = [];
		foreach ( $language_codes as $language ) {
			$languages[] = [
				'value' => $language['code'],
				'label' => $language['name']
			];
		}

		$options          = imageseo_get_options();
		$current_language = get_locale();

		$current_user = wp_get_current_user();
		$user_info    = array(
			'email'     => '',
			'firstName' => '',
			'lastName'  => ''
		);

		if ( in_array( 'administrator', $current_user->roles ) ) {
			$user_info['email']     = $current_user->user_email;
			$user_info['firstName'] = $current_user->user_firstname;
			$user_info['lastName']  = $current_user->user_lastname;
		}

		$camelCased = [];

		foreach ( $options as $key => $value ) {
			if ( strpos( $key, '_' ) === - 1 ) {
				$camelCased[ $key ] = $value;
				continue;
			}

			$camelCaseKey                = lcfirst( str_replace( '_', '', ucwords( $key, '_' ) ) );
			$camelCased[ $camelCaseKey ] = $value;
		}

		$camelCased['user'] = $user_info;

		$global = [
			'languages'        => $languages,
			'nonce'            => wp_create_nonce( 'wp_rest' ),
			'apiUrl'           => get_rest_url( null, 'imageseo/v1' ),
			'user'             => $user_info,
			'currentLanguage'  => $current_language,
			'allowedPostTypes' => $allowed_post_types,
			'totalImages'      => $totalImages,
			'totalNoAlt'       => $totalNoAlt,
			'altSpecification' => AltSpecification::getMetas(),
			'bulkQuery'        => $bulkQuery,
		];
		wp_add_inline_script(
			'imageseo-v2',
			'const imageSeoGlobal = ' . json_encode( $global ),
			'before'
		);
		wp_add_inline_script(
			'imageseo-v2',
			'const imageSeoSettings = ' . json_encode( $camelCased ),
			'before'
		);
	}
}
