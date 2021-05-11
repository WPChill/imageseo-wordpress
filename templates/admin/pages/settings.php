<?php

if (!defined('ABSPATH')) {
    exit;
}
// $excludeFilenames = get_option('_imageseo_bulk_exclude_filenames');
// if (!$excludeFilenames) {
//     $excludeFilenames = [];
// }

// $renameFileService = imageseo_get_service('GenerateFilenameNextGen');
// list($filename, $extension) = $renameFileService->generateFilenameForAttachmentId(3, $excludeFilenames);
// var_dump($filename);
// var_dump($extension);
// exit;
// $alt = imageseo_get_service('TagsToString')->replace('[keyword_1]', 190);
// error_log('[alt nextgen] : ' . $alt);
// imageseo_get_service('QueryNextGen')->updateAlt(1, $alt);
// exit;
// // list($filename, $extension) = $renameFileService->generateFilenameForAttachmentId(2);
// // var_dump($filename);
// // var_dump($extension);
// // exit;

?>

<div id="wrap-imageseo">
    <div class="wrap">
		<?php if (!isset($_GET['wizard']) || imageseo_get_api_key()) {?>
			<div id="js-module-imageseo"></div>
		<?php } elseif (isset($_GET['wizard']) && !imageseo_get_api_key()) {?>
			<div id="js-module-imageseo-wizard"></div>
		<?php } ?>
    </div>
</div>


<input type="hidden" id="_nonce_imageseo_register" value="<?php echo wp_create_nonce('imageseo_register'); ?>">
<input type="hidden" id="_nonce_imageseo_login" value="<?php echo wp_create_nonce('imageseo_login'); ?>">
<input type="hidden" id="_nonce_imageseo_valid_api_key" value="<?php echo wp_create_nonce('imageseo_valid_api_key'); ?>">
<input type="hidden" id="_nonce_imageseo_proxy" value="<?php echo wp_create_nonce('imageseo_proxy'); ?>">
<input type="hidden" id="_nonce_imageseo_save_global_settings" value="<?php echo wp_create_nonce('imageseo_save_global_settings'); ?>">
<input type="hidden" id="_nonce_imageseo_save_social_settings" value="<?php echo wp_create_nonce('imageseo_save_social_settings'); ?>">
