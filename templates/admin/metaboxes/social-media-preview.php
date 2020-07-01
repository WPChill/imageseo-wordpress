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
$adminGenerateUrl = admin_url(sprintf('admin-post.php?action=imageseo_generate_manual_social_media&post_id=%s&post_type=%s', $postId, get_post_type($postId)));
$adminGenerateUrl = wp_nonce_url($adminGenerateUrl, 'imageseo_generate_manual_social_media');

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

<a href="<?php echo $adminGenerateUrl; ?>" class="button" style="display: flex; align-items: center;">
    <?php echo $text; ?>
</a>
