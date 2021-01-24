<?php

if (!defined('ABSPATH')) {
    exit;
}

$tab = isset($_GET['tab']) ? $_GET['tab'] : null;

switch ($tab) {
   case 'support':
        include_once __DIR__ . '/_support.php';

        return;
    break;
}

?>

<div id="wrap-imageseo">
    <div class="wrap">
        <h1 class="imageseo__page-title screen-reader-text"><?php _e('Image SEO Settings', 'imageseo'); ?></h1>
		<div id="js-module-imageseo"></div>
    </div>
</div>

