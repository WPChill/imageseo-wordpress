<?php

namespace ImageSeoWP\Actions\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use ImageSeoWP\Helpers\Pages;

class Enqueue {

	public function hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'adminEnqueueScripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'adminEnqueueCSS' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueueBlockEditorAssets' ) );
	}

	/**
	 * Enqueue admin CSS
	 *
	 * @see admin_enqueue_scripts
	 *
	 * @param string $page
	 */
	public function adminEnqueueCSS( $page ) {
		wp_enqueue_style( 'imageseo-admin-global-css', IMAGESEO_URL_DIST . '/css/admin-global.css', array(), IMAGESEO_VERSION );
	}

	/**
	 * @see admin_enqueue_scripts
	 *
	 * @param string $page
	 */
	public function adminEnqueueScripts( $page ) {
		if ( 'toplevel_page_imageseo-settings' === $page ) {
			$asset_script_path = IMAGESEO_DIR_DIST . '/settingsv2/index.asset.php';
			$asset_file        = require $asset_script_path;
			wp_enqueue_media();
			wp_enqueue_script(
				'imageseo-v2',
				IMAGESEO_URL_DIST . '/settingsv2/index.js',
				$asset_file['dependencies'],
				$asset_file['version'],
			);
			wp_enqueue_style(
				'imageseo-v2',
				IMAGESEO_URL_DIST . '/settingsv2/index.css',
				array( 'wp-components' ),
				$asset_file['version'],
			);
		}
		if ( ! in_array(
			$page,
			array(
				'toplevel_page_' . Pages::SETTINGS,
				'image-seo_page_imageseo-optimization',
				'upload.php',
				'post.php',
				'image-seo_page_imageseo-options',
				'image-seo_page_imageseo-settings',
				'image-seo_page_imageseo-social-media',
			),
			true
		) ) {
			return;
		}

		if ( in_array( $page, array( 'upload.php' ), true ) ) {
			wp_enqueue_script( 'imageseo-admin-js', IMAGESEO_URL_DIST . '/media-upload.js', array( 'jquery', 'wp-i18n' ) );
			wp_add_inline_script( 'imageseo-admin-js', 'const imageseo_upload_nonce ="' . wp_create_nonce( 'imageseo_upload_nonce' ) . '";', 'before' );
		}

		if ( in_array( $page, array( 'post.php' ), true ) ) {
			wp_enqueue_script( 'imageseo-admin-generate-social-media-js', IMAGESEO_URL_DIST . '/generate-social-media.js', array( 'jquery' ), IMAGESEO_VERSION, true );
			wp_add_inline_script( 'imageseo-admin-js', 'const imageseo_ajax_nonce = "' . wp_create_nonce( IMAGESEO_OPTION_GROUP . '-options' ) . '";', 'before' );
		}
	}

	public function enqueueBlockEditorAssets() {
		$asset_path = IMAGESEO_DIR_DIST . '/gutenberg/image-block/index.asset.php';
		$asset_file = require $asset_path;
		wp_enqueue_script(
			'imageseo-gutenberg-image-block',
			IMAGESEO_URL_DIST . '/gutenberg/image-block/index.js',
			$asset_file['dependencies'],
			$asset_file['version'],
			true
		);
		wp_enqueue_style(
			'imageseo-gutenberg-image-block',
			IMAGESEO_URL_DIST . '/gutenberg/image-block/index.css',
			array( 'wp-block-editor' ),
			$asset_file['version'],
		);

		$settings = imageseo_get_options();

		wp_localize_script(
			'imageseo-gutenberg-image-block',
			'imageSEO',
			array(
				'rewriteFilename' => $settings['activeRenameWriteUpload'],
			)
		);
	}
}
