<?php

if (!defined('ABSPATH')) {
    exit;
}

// $storage = \C_Gallery_Storage::get_instance();
// var_dump(get_class_methods($storage));
// var_dump(getimagesize($storage->get_image_abspath(1)));
// var_dump($storage->get_image_url(1));

// global $wpdb;
// $sqlQuery = 'SELECT p.extras_post_id as id ';
// $sqlQuery .= "FROM {$wpdb->prefix}ngg_pictures p ";
// $sqlQuery .= 'WHERE 1=1 ';
// $sqlQuery .= 'AND p.pid = %d ';

// $images = $wpdb->get_results($wpdb->prepare($sqlQuery,
//     1,
// ), ARRAY_A);
// var_dump(current($images));
$a = imageseo_get_service('ReportImage')->generateReportByAttachmentIdForNextGen(1);
var_dump($a);
exit;

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
