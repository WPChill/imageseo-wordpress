<?php
/*
Plugin Name: Image SEO
Plugin URI: https://imageseo.io
Description: Optimize your images for search engines. Search engine optimization and web marketing strategy often neglect their images. Stop doing this mistake and take control back on your WordPress Medias !
Author: Image SEO
Author URI: https://imageseo.io/
Text Domain: imageseo
Domain Path: /languages/
Version: 3.0.0
Requires at least: 5.0
License: GPLv2
*/

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

use ImageSeoWP\Context;

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
define('IMAGESEO_API_URL', 'https://imageseo-api-external.herokuapp.com');

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

    require_once IMAGESEO_DIR . '/src/Async/BulkImageActionScheduler.php';
    require_once IMAGESEO_DIR . '/src/Async/WorkerOnUploadActionScheduler.php';

    if (imageseo_is_compatible()) {
        require_once __DIR__ . '/imageseo-functions.php';

        load_plugin_textdomain('imageseo', false, IMAGESEO_DIR_LANGUAGES);

        Context::getContext()->initPlugin();
		\ImageSeoWP\Admin\SettingsPage::get_instance();
    }
}

register_activation_hook(__FILE__, 'imageseo_plugin_activate');
register_deactivation_hook(__FILE__, 'imageseo_plugin_deactivate');
register_uninstall_hook(__FILE__, 'imageseo_plugin_uninstall');

add_action('plugins_loaded', 'imageseo_plugin_loaded');
