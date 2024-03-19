<?php

/**
 * Plugin Name:              Image SEO
 * Plugin URI:               https://imageseo.io
 * Description:              Optimize your images for search engines. Search engine optimization and web marketing strategy often neglect their images.
 * Author:                   WPChill
 * Version:                  3.0.0
 * Author URI:               https://www.wpchill.com/
 * License:                  GPLv3 or later
 * License URI:              http://www.gnu.org/licenses/gpl-3.0.html
 * Requires PHP:             7.0
 * Text Domain:              imageseo
 * Domain Path:              /languages/
 *
 * Copyright 2019-2023        WPUmbrella      aurelio@wp-umbrella.com
 * Copyright 24.10.2023       WPChill         heyyy@wpchill.com
 *
 *
 * Original Plugin URI:      https://imageseo.io
 * Original Author URI:      https://imageseo.io
 * Original Author:          https://profiles.wordpress.org/gmulti/
 *
 * NOTE:
 *
 * WP Umbrella has transferred ownership to WPChill on: 24th of October, 2023.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 3, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

if (!defined('ABSPATH')) {
	exit;
}

require_once __DIR__ . '/vendor/autoload.php';

use ImageSeoWP\Admin\SettingsPage;
use ImageSeoWP\Context;
use ImageSeoWP\Processes\OnUploadImage;
use ImageSeoWP\Services\BulkOptimizer;
use ImageSeoWP\V2\RestApi;

define('IMAGESEO_NAME', 'ImageSEO');
define('IMAGESEO_SLUG', 'imageseo');
define('IMAGESEO_OPTION_GROUP', 'group-imageseo');
define('IMAGESEO_VERSION', '3.0.0');
define('IMAGESEO_PHP_MIN', '7.0');
define('IMAGESEO_DEBUG', false);
define('IMAGESEO_DEBUG_ALT', false);
define('IMAGESEO_BNAME', plugin_basename(__FILE__));
define('IMAGESEO_DIR', __DIR__);
define('IMAGESEO_DIR_LANGUAGES', IMAGESEO_DIR . '/languages');
define('IMAGESEO_DIR_DIST', IMAGESEO_DIR . '/dist');
define('IMAGESEO_API_URL', 'https://api.imageseo.com');
// define('IMAGESEO_API_URL', 'http://192.168.1.148:3000');

define('IMAGESEO_APP_URL', 'https://app.imageseo.io');
define('IMAGESEO_SITE_URL', 'https://imageseo.io');
define('IMAGESEO_LANGUAGES', IMAGESEO_DIR . '/languages/');

define('IMAGESEO_DIRURL', plugin_dir_url(__FILE__));
define('IMAGESEO_URL_DIST', IMAGESEO_DIRURL . 'dist');

define('IMAGESEO_TEMPLATES', IMAGESEO_DIR . '/templates');
define('IMAGESEO_TEMPLATES_ADMIN', IMAGESEO_TEMPLATES . '/admin');
define('IMAGESEO_TEMPLATES_ADMIN_NOTICES', IMAGESEO_TEMPLATES_ADMIN . '/notices');
define('IMAGESEO_TEMPLATES_ADMIN_PAGES', IMAGESEO_TEMPLATES_ADMIN . '/pages');
define('IMAGESEO_TEMPLATES_ADMIN_METABOXES', IMAGESEO_TEMPLATES_ADMIN . '/metaboxes');
define('IMAGESEO_LOCALE', get_locale());
/**
 * Check compatibility this ImageSeo with WordPress config.
 */
function imageseo_is_compatible()
{
	// Check php version.
	if (version_compare(PHP_VERSION, IMAGESEO_PHP_MIN) < 0) {
		add_action('admin_notices', 'imageseo_php_min_compatibility');

		return false;
	}

	return true;
}

/**
 * Admin notices if imageseo not compatible.
 */
function imageseo_php_min_compatibility()
{
	if (!file_exists(IMAGESEO_TEMPLATES_ADMIN_NOTICES . '/php-min.php')) {
		return;
	}

	include_once IMAGESEO_TEMPLATES_ADMIN_NOTICES . '/php-min.php';
}

function imageseo_plugin_activate()
{
	if (!imageseo_is_compatible()) {
		return;
	}

	require_once __DIR__ . '/imageseo-functions.php';

	Context::getContext()->activatePlugin();
}

function imageseo_plugin_deactivate()
{
	require_once __DIR__ . '/imageseo-functions.php';

	Context::getContext()->deactivatePlugin();
}

function imageseo_plugin_uninstall()
{
	delete_option(IMAGESEO_SLUG);
	delete_option('imageseo_version');
}

if (!class_exists('ActionScheduler')) {
	require_once IMAGESEO_DIR . '/thirds/action-scheduler/action-scheduler.php';
}


/**
 * Load ImageSEO.
 */
function imageseo_plugin_loaded()
{

	if (imageseo_is_compatible()) {
		require_once __DIR__ . '/imageseo-functions.php';
		load_plugin_textdomain('imageseo', false, IMAGESEO_DIR_LANGUAGES);

		Context::getContext()->initPlugin();

		/**
		 * Async process for background upload process
		 */
		OnUploadImage::getInstance();

		/**
		 * Render the settings page
		 */
		SettingsPage::get_instance();

		/**
		 * We need this here so that the bulk optimizer is always available.
		 */
		BulkOptimizer::getInstance();
	}
}

register_activation_hook(__FILE__, 'imageseo_plugin_activate');
register_deactivation_hook(__FILE__, 'imageseo_plugin_deactivate');
register_uninstall_hook(__FILE__, 'imageseo_plugin_uninstall');

add_action('plugins_loaded', 'imageseo_plugin_loaded');
