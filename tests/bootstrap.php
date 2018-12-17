<?php
/**
 * PHPUnit bootstrap file
 *
 * @package ImageSeo
 */

use Symfony\Component\Dotenv\Dotenv;

$file_env = __DIR__ . '/../.env.json';
if (file_exists($file_env)) {
    $config = json_decode(file_get_contents($file_env), true);
    putenv('API_KEY=' . $config['API_KEY']);
}

$_tests_dir = getenv('WP_TESTS_DIR');

if (! $_tests_dir) {
    $_tests_dir = rtrim(sys_get_temp_dir(), '/\\') . '/wordpress-tests-lib';
}

if (! file_exists($_tests_dir . '/includes/functions.php')) {
    throw new Exception("Could not find $_tests_dir/includes/functions.php, have you run bin/install-wp-tests.sh ?");
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin()
{
    require dirname(dirname(__FILE__)) . '/imageseo.php';
}
tests_add_filter('muplugins_loaded', '_manually_load_plugin');

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';
