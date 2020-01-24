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
        $this->limitExcedeed = imageseo_get_service('UserInfo')->hasLimitExcedeed();

        $postTypes = $this->optionService->getOption('social_media_post_types');
        foreach ($postTypes as $postType) {
            add_filter('manage_' . $postType . '_posts_columns', [$this, 'addColumn']);
            add_action('manage_' . $postType . '_posts_custom_column', [$this, 'previewSocialMediaImage'], 10, 2);
        }

        add_action('add_meta_boxes', [$this, 'addMetaBoxPreviewSocialImage']);
    }

    public function addMetaBoxPreviewSocialImage()
    {
        $postTypes = $this->optionService->getOption('social_media_post_types');
        foreach ($postTypes as $postType) {
            add_meta_box(
                'imageseo_preview_social_image',
                __('Social Media Image', 'imageseo'),
                [$this, 'renderPreviewSocialImage'],
                $postType,
                'advanced',
                'high'
            );
        }
    }

    public function renderPreviewSocialImage()
    {
        echo 'here bobby';
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
                $postType = isset($_GET['post_type']) ? $_GET['post_type'] : 'post';
                $adminGenerateUrl = admin_url(sprintf('admin-post.php?action=imageseo_generate_manual_social_media&post_id=%s&post_type=%s', $postId, $postType));
                $adminGenerateUrl = wp_nonce_url($adminGenerateUrl, 'imageseo_generate_manual_social_media');

                $url = $this->getPreviewImageUrlSocialMedia($postId);
                $process = get_transient(sprintf('_imageseo_filename_social_process_%s', $postId));
                if (!$url && !$process) {
                    ?>
                    <p><?php _e('No social image', 'imageseo'); ?></p>
                    <?php if (!$this->limitExcedeed): ?>
                        <a href="<?php echo esc_url($adminGenerateUrl); ?>" class="button">
                            <?php _e('Generate', 'imageseo'); ?>
                        </a>
                    <?php else: ?>
                        <a
                            class="imageseo-btn--simple imageseo-btn--small-padding imageseo-btn"
                            target="_blank"
                            href="https://app.imageseo.io/plan"
                        >
                            <?php _e('Get more credits', 'imageseo'); ?>
                        </a>
                    <?php endif; ?>
                    <?php
                } elseif (!$url && $process) {
                    ?>
                     <img
                        src="<?php echo IMAGESEO_URL_DIST; ?>/images/rotate-cw.svg"
                        style="animation:imageseo-rotation 1s infinite linear;"
                    />
                    <?php _e('Current loading... Reload the page.', 'imageseo'); ?>
                    <?php
                } elseif ($url) {
                    if ($process) {
                        delete_transient(sprintf('_imageseo_filename_social_process_%s', $postId));
                    } ?>
                    <div>
                        <img src="<?php echo $url; ?>" width="100" style="object-fit:contain;" />
                    </div>
                    
                    <?php if (!$this->limitExcedeed): ?>
                        <a href="<?php echo esc_url($adminGenerateUrl); ?>" style="display:inline-block;">
                            <?php _e('Regenerate', 'imageseo'); ?>
                        </a>
                    <?php endif; ?>
                    <?php
                }
                break;
        }
    }
}
