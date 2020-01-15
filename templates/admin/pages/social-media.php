<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<div id="wrap-imageseo">
    <div class="wrap">
        <h1 class="imageseo__page-title"><?php _e('Image SEO Social media', 'imageseo'); ?></h1>
        <div id="js-module-social-media"></div>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php?action=imageseo_generate_image')); ?>">
            <input type="hidden" value="" name="image_base64" id="image_base64">
            <input type="hidden" value="imageseo_generate_image" name="action">
            <button type="submit">Upload image</button>
        </form>
    </div>
</div>