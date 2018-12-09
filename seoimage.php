<?php
/*
Plugin Name: SeoImage
Plugin URI: http://wordpress.org/plugins/seoimage/
Description: Improve your SEO Image
Author: SeoImage
Author URI: https://seoimage.io/
Text Domain: seoimage
Domain Path: /languages/
Version: 1.0.0
*/

if (! defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/../../seoimage-php/vendor/autoload.php';

use SeoImageWP\ContextSeoImage;

define('SEOIMAGE_NAME', 'SeoImage');
define('SEOIMAGE_SLUG', 'seoimage');
define('SEOIMAGE_OPTION_GROUP', 'group-seoimage');
define('SEOIMAGE_VERSION', '1.0.0');
define('SEOIMAGE_PHP_MIN', '5.6');
define('SEOIMAGE_BNAME', plugin_basename(__FILE__));
define('SEOIMAGE_DIR', __DIR__);
define('SEOIMAGE_DIR_LANGUAGES', SEOIMAGE_DIR . '/languages');
define('SEOIMAGE_DIR_DIST', SEOIMAGE_DIR . '/dist');
define('SEOIMAGE_DEBUG', false);
define('SEOIMAGE_API_LOCAL', false);

define('SEOIMAGE_DIRURL', plugin_dir_url(__FILE__));
define('SEOIMAGE_URL_DIST', SEOIMAGE_DIRURL . 'dist');

define('SEOIMAGE_TEMPLATES', SEOIMAGE_DIR . '/templates');
define('SEOIMAGE_TEMPLATES_ADMIN', SEOIMAGE_TEMPLATES . '/admin');
define('SEOIMAGE_TEMPLATES_ADMIN_NOTICES', SEOIMAGE_TEMPLATES_ADMIN . '/notices');
define('SEOIMAGE_TEMPLATES_ADMIN_PAGES', SEOIMAGE_TEMPLATES_ADMIN . '/pages');


/**
 * Check compatibility this SeoImage with WordPress config.
 */
function seoimage_is_compatible()
{
    // Check php version.
    if (version_compare(PHP_VERSION, SEOIMAGE_PHP_MIN) < 0) {
        add_action('admin_notices', 'seoimage_php_min_compatibility');
        return false;
    }

    return true;
}

/**
 * Admin notices if seoimage not compatible
 *
 * @return void
 */
function seoimage_php_min_compatibility()
{
    if (! file_exists(SEOIMAGE_TEMPLATES_ADMIN_NOTICES . '/php-min.php')) {
        return;
    }

    include_once SEOIMAGE_TEMPLATES_ADMIN_NOTICES . '/php-min.php';
}

/**
 * @since 1.0.0
 */
function seoimage_plugin_activate()
{
    if (! seoimage_is_compatible()) {
        return;
    }

    require_once __DIR__ . '/seoimage-functions.php';

    ContextSeoImage::getContext()->activatePlugin();
}

/**
 * @since 1.0.0
 */
function seoimage_plugin_deactivate()
{
    require_once __DIR__ . '/seoimage-functions.php';

    ContextSeoImage::getContext()->deactivatePlugin();
}

/**
 * @since 1.0.0
 */
function seoimage_plugin_uninstall()
{
    delete_option(SEOIMAGE_SLUG);
}


/**
 * Load SeoImage.
 *
 * @since 1.0.0
 */
function seoimage_plugin_loaded()
{
    require_once __DIR__ . '/seoimage-compatibility.php';

    if (seoimage_is_compatible()) {
        require_once __DIR__ . '/vendor/autoload.php';

        require_once __DIR__ . '/seoimage-functions.php';

        load_plugin_textdomain('seoimage', false, SEOIMAGE_DIR_LANGUAGES);

        ContextSeoImage::getContext()->initPlugin();
    }
}


register_activation_hook(__FILE__, 'seoimage_plugin_activate');
register_deactivation_hook(__FILE__, 'seoimage_plugin_deactivate');
register_uninstall_hook(__FILE__, 'seoimage_plugin_uninstall');

add_action('plugins_loaded', 'seoimage_plugin_loaded');
