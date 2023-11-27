<?php

if (!defined('ABSPATH')) {
    exit;
}
$postId = isset($_GET['post']) ? (int) $_GET['post'] : null;

if (!$postId) {
    ?>

    <p><?php esc_html_e('The card will be generated when your article is published!', 'imageseo'); ?>

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
    <p><?php esc_html_e('No social image', 'imageseo'); ?></p>

    <?php
} else {
        $text = esc_html__('Update', 'imageseo'); ?>
    <img id="imageseo-social-media-image" src="<?php echo esc_url( $url ); ?>" />

    <?php
    }
?>

<a href="<?php echo esc_url( $adminGenerateUrl ); ?>" class="button" style="display: flex; align-items: center;">
    <?php echo wp_kses_post( $text ); ?>
</a>
