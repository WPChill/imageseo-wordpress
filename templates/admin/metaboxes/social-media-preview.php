<?php

if (!defined('ABSPATH')) {
    exit;
}
$postId = isset($_GET['post']) ? (int) $_GET['post'] : null;

if (!$postId) {
    ?>

    <p><?php _e('The card will be generated when your article is published!', 'imageseo'); ?>

    <?php
    return;
}

$url = imageseo_get_service('ImageSocial')->getPreviewImageUrlSocialMedia($postId, 'large');
$process = imageseo_get_service('ImageSocial')->isCurrentProcess($postId);
$text = __('Generate image', 'imageseo');
if (!$url && !$process) {
    ?>
    <p><?php _e('No social image', 'imageseo'); ?></p>
    
    <?php
} else {
        $text = __('Update', 'imageseo'); ?>
    <img id="imageseo-social-media-image" src="<?php echo $url; ?>" />
    
    <?php
    }
?>

<button id="imageseo-social-media" data-id="<?php echo $postId; ?>" class="button" style="display: flex; align-items: center;">
    <img
        src="<?php echo IMAGESEO_URL_DIST; ?>/images/rotate-cw.svg"
        style="animation:imageseo-rotation 1s infinite linear; margin-right:5px; display:none;"
    />
    <?php echo $text; ?>
</button>
