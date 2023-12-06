<?php

namespace ImageSeoWP\Actions\Admin;

if (!defined('ABSPATH')) {
    exit;
}

class SocialMediaColumn
{
	public $optionService;
	public $imageSocialService;
	
    public function __construct()
    {
        $this->optionService = imageseo_get_service('Option');
        $this->imageSocialService = imageseo_get_service('ImageSocial');
    }

    public function hooks()
    {
        if (!imageseo_allowed()) {
            return;
        }

        $postTypes = $this->optionService->getOption('social_media_post_types');
        if (is_array($postTypes)) {
            foreach ($postTypes as $postType) {
                add_filter('manage_' . $postType . '_posts_columns', [$this, 'addColumn']);
                add_action('manage_' . $postType . '_posts_custom_column', [$this, 'previewSocialMediaImage'], 10, 2);
            }
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
                'side',
                'high'
            );
        }
    }

    public function renderPreviewSocialImage()
    {
        include_once IMAGESEO_TEMPLATES_ADMIN_METABOXES . '/social-media-preview.php';
    }

    public function addColumn($columns)
    {
        $columns = ['imageseo_social_media' => '<span class="dashicons dashicons-format-image"></span><span style="padding-left:10px">Social</span>'] + $columns;

        return $columns;
    }

    public function previewSocialMediaImage($column, $postId)
    {
        switch ($column) {
            case 'imageseo_social_media':
                $limitExcedeed = imageseo_get_service('UserInfo')->hasLimitExcedeed();

                $postType = isset($_GET['post_type']) ? $_GET['post_type'] : 'post';
                $adminGenerateUrl = admin_url(sprintf('admin-post.php?action=imageseo_generate_manual_social_media&post_id=%s&post_type=%s', $postId, $postType));
                $adminGenerateUrl = wp_nonce_url($adminGenerateUrl, 'imageseo_generate_manual_social_media');

                $url = $this->imageSocialService->getPreviewImageUrlSocialMedia($postId);
                $metadata = wp_get_attachment_metadata($this->imageSocialService->getPreviewImageIdSocialMedia($postId));
                if (isset($metadata['last_updated']) && $url) {
                    $url = sprintf('%s?last_updated=%s&_empty_cache=true', $url, $metadata['last_updated']);
                }

                if (!$url && !$this->imageSocialService->isCurrentProcess($postId)) {
                    ?>
                    <p><?php esc_html_e('No social image', 'imageseo'); ?></p>
                    <?php if (!$limitExcedeed) { ?>
                        <a href="<?php echo esc_url($adminGenerateUrl); ?>" class="button">
                            <?php esc_html_e('Generate', 'imageseo'); ?>
                        </a>
                    <?php } else { ?>
                        <a
                            class="imageseo-btn--simple imageseo-btn--small-padding imageseo-btn"
                            target="_blank"
                            href="https://app.imageseo.io/plan"
                        >
                            <?php esc_html_e('Get more credits', 'imageseo'); ?>
                        </a>
                    <?php } ?>
                    <?php
                } elseif (!$url && $this->imageSocialService->isCurrentProcess($postId)) {
                    ?>
                     <img
                        src="<?php echo esc_url( IMAGESEO_URL_DIST ); ?>/images/rotate-cw.svg"
                        style="animation:imageseo-rotation 1s infinite linear;"
                    />
                    <?php esc_html_e('Current loading... Reload the page.', 'imageseo'); ?>
                    <?php
                } elseif ($url) {
                    if ($this->imageSocialService->isCurrentProcess($postId)) {
                        ?>
                         <img
                            src="<?php echo esc_url( IMAGESEO_URL_DIST ); ?>/images/rotate-cw.svg"
                            style="animation:imageseo-rotation 1s infinite linear;"
                        />
                        <?php esc_html_e('Current regeneration...', 'imageseo'); ?>
                        <?php
                    } ?>
                    <div>
                        <img src="<?php echo esc_url( $url ); ?>" width="100" style="object-fit:contain;" />
                    </div>

                    <?php if (!$limitExcedeed) { ?>
                        <a href="<?php echo esc_url($adminGenerateUrl); ?>" style="display:inline-block;">
                            <?php esc_html_e('Update', 'imageseo'); ?>
                        </a>
                    <?php } ?>
                    <?php
                }
                break;
        }
    }
}
