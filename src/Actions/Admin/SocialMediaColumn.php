<?php

namespace ImageSeoWP\Actions\Admin;

if (!defined('ABSPATH')) {
    exit;
}

class SocialMediaColumn
{
    public function __construct()
    {
        $this->optionService = imageseo_get_service('Option');
    }

    public function hooks()
    {
        if (!imageseo_allowed()) {
            return;
        }

        $postTypes = $this->optionService->getOption('social_media_post_types');
        foreach ($postTypes as $postType) {
            add_filter('manage_' . $postType . '_posts_columns', [$this, 'addColumn']);
            add_action('manage_' . $postType . '_posts_custom_column', [$this, 'previewSocialMediaImage'], 10, 2);
        }
    }

    public function addColumn($columns)
    {
        $columns = ['imageseo_social_media' => '<span class="dashicons dashicons-format-image"></span><span style="padding-left:10px">Social</span>'] + $columns;

        return $columns;
    }

    public function getPreviewImageUrlSocialMedia($postId)
    {
        $medias = $this->optionService->getOption('social_media_type');

        foreach ($medias as $media) {
            $id = get_post_meta($postId, sprintf('_imageseo_social_media_image_%s', $media), true);
            if (!$id) {
                continue;
            }

            return wp_get_attachment_image_url($id, 'medium');
        }

        return false;
    }

    public function previewSocialMediaImage($column, $postId)
    {
        switch ($column) {
            case 'imageseo_social_media':
                $url = $this->getPreviewImageUrlSocialMedia($postId);
                if (!$url) {
                    ?>
                    <p><?php _e('No image', 'imageseo'); ?></p>
                    <a href="<?php echo esc_url(admin_url('admin-post.php?action=imageseo_generate_manual_social_media&post_id=' . $postId)); ?>" class="button">
                        <?php _e('Generate image', 'imageseo'); ?>
                    </a>
                    <?php
                } else {
                    ?>
                    <div>
                        <img src="<?php echo $url; ?>" width="100" height="100" style="object-fit:contain;" />
                    </div>
                    
                    <a href="<?php echo esc_url(admin_url('admin-post.php?action=imageseo_generate_manual_social_media&post_id=' . $postId)); ?>" class="button" style="display:inline-block;">
                        <?php _e('Regenerate image', 'imageseo'); ?>
                    </a><?php
                }
                break;
        }
    }
}
