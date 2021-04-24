<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<div id="wrap-imageseo">
    <div class="wrap">
		<?php if (!isset($_GET['wizard'])) { ?>
			<div id="js-module-imageseo"></div>
		<?php } elseif (isset($_GET['wizard'])) { ?>
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
