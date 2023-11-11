<?php

namespace ImageSeoWP\Actions\Admin;

if (!defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Helpers\AltFormat;
use ImageSeoWP\Helpers\Bulk\AltSpecification;
use ImageSeoWP\Helpers\Pages;

class Enqueue
{
    public function hooks()
    {
        add_action('admin_enqueue_scripts', [$this, 'adminEnqueueScripts']);
        add_action('admin_enqueue_scripts', [$this, 'adminEnqueueCSS']);
    }

	/**
	 * Enqueue admin CSS
	 *
	 * @see admin_enqueue_scripts
	 *
	 * @param string $page
	 */
	public function adminEnqueueCSS( $page ) {
		wp_enqueue_style( 'imageseo-admin-global-css', IMAGESEO_URL_DIST . '/css/admin-global.css', [], IMAGESEO_VERSION );
	}

    /**
     * @see admin_enqueue_scripts
     *
     * @param string $page
     */
    public function adminEnqueueScripts($page)
    {
	    // Enqueue new admin functionality script
	    if ( 'toplevel_page_imageseo-settings' === $page ) {
		    wp_enqueue_media();
		    wp_enqueue_script( 'imageseo-functionality', IMAGESEO_URL_DIST . '/functionality.js', array(
			    'jquery',
			    'wp-color-picker',
			    'wp-i18n'
		    ),                 IMAGESEO_VERSION, true );
		    wp_enqueue_style( 'wp-color-picker' );
	    }
        if (!in_array($page, ['toplevel_page_' . Pages::SETTINGS, 'image-seo_page_imageseo-optimization', 'upload.php', 'post.php', 'image-seo_page_imageseo-options', 'image-seo_page_imageseo-settings', 'image-seo_page_imageseo-social-media'], true)) {
            return;
        }

	    if ( in_array( $page, [ 'upload.php' ], true ) ) {
		    wp_enqueue_script( 'imageseo-admin-js', IMAGESEO_URL_DIST . '/media-upload.js', [ 'jquery', 'wp-i18n' ] );
		    wp_add_inline_script( 'imageseo-admin-js', 'const imageseo_upload_nonce = ' . wp_create_nonce( 'imageseo_upload_nonce' ) . ';', 'before' );
	    }

        if (in_array($page, ['post.php'], true)) {
            wp_enqueue_script('imageseo-admin-generate-social-media-js', IMAGESEO_URL_DIST . '/generate-social-media.js', ['jquery'], IMAGESEO_VERSION, true);
	        wp_add_inline_script( 'imageseo-admin-js', 'const imageseo_ajax_nonce = ' . wp_create_nonce( 'imageseo_ajax_nonce' ) . ';', 'before' );
        }
    }
}
