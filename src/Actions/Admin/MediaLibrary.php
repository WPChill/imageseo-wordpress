<?php

namespace ImageSeoWP\Actions\Admin;

if (!defined('ABSPATH')) {
    exit;
}

class MediaLibrary
{
    public function __construct()
    {
        $this->optionService = imageseo_get_service('Option');
        $this->reportImageService = imageseo_get_service('ReportImage');
        $this->renameFileService = imageseo_get_service('RenameFile');
        $this->altService = imageseo_get_service('Alt');
    }

    public function hooks()
    {
        if (!imageseo_allowed()) {
            return;
        }

        add_filter('manage_media_columns', [$this, 'manageMediaColumns']);
        add_action('manage_media_custom_column', [$this, 'manageMediaCustomColumn'], 10, 2);

        add_action('wp_ajax_imageseo_media_alt_update', [$this, 'ajaxAltUpdate']);

        add_action('admin_init', [$this, 'metaboxReport']);
        add_filter('wp_generate_attachment_metadata', [$this, 'createProcessOnUpload'], 10, 2);
        add_action('delete_attachment', [$this, 'updateDeleteCount'], 100);
    }

    public function muteOnUpload()
    {
        remove_filter('wp_generate_attachment_metadata', [$this, 'createProcessOnUpload'], 10, 2);
    }

    public function createProcessOnUpload($metadata, $attachmentId)
    {
        if (!wp_attachment_is_image($attachmentId)) {
            return $metadata;
        }

        $activeAltOnUpload = $this->optionService->getOption('active_alt_write_upload');
        $activeRenameOnUpload = $this->optionService->getOption('active_rename_write_upload');

        $total = get_option('imageseo_get_total_images');
        if ($total) {
            update_option('imageseo_get_total_images', (int) $total + 1, false);
        }

        if (!$activeAltOnUpload) {
            $total = get_option('imageseo_get_number_image_non_optimize_alt');
            if ($total) {
                update_option('imageseo_get_number_image_non_optimize_alt', (int) $total + 1, false);
            }
        }

        if (!$activeAltOnUpload && !$activeRenameOnUpload) {
            return $metadata;
        }

        try {
            $response = imageseo_get_service('ReportImage')->generateReportByAttachmentId($attachmentId, ['force' => true]);
        } catch (\Exception $e) {
            return $metadata;
        }

        if ($activeAltOnUpload) {
            $this->altService->updateAltAttachmentWithReport($attachmentId);
        }

        if ($activeRenameOnUpload) {
            try {
                $filename = $this->renameFileService->getNameFileWithAttachmentId($attachmentId);
                $result = $this->renameFileService->updateFilename($attachmentId, $filename, $metadata, ['backup' => false, 'onUpload' => true]);
                if (array_key_exists('metadata', $result)) {
                    $metadata = $result['metadata'];
                }

                return $metadata;
            } catch (NoRenameFile $e) {
            }
        }
    }

    /**
     * @param int $attachmentId
     */
    public function updateDeleteCount($attachmentId)
    {
        if (!wp_attachment_is_image($attachmentId)) {
            return;
        }

        $alt = $this->altService->getAlt($attachmentId);

        $total = get_option('imageseo_get_number_image_non_optimize_alt');
        if ($total) {
            update_option('imageseo_get_number_image_non_optimize_alt', (int) $total - 1, false);
        }

        $total = get_option('imageseo_get_total_images');
        if ($total) {
            update_option('imageseo_get_total_images', (int) $total - 1, false);
        }
    }

    public function ajaxAltUpdate()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'code' => 'not_authorized',
            ]);
            exit;
        }

        $postId = absint($_POST['post_id']);
        $alt = wp_strip_all_tags($_POST['alt']);

        imageseo_get_service('Alt')->updateAlt($postId, $alt);
    }

    public function metaboxReport()
    {
        add_meta_box(
            'imageseo-report',
            __('Report ImageSEO', 'imageseo'),
            [$this, 'viewMetaboxReport'],
            'attachment',
            'normal'
        );
    }

    /**
     * @param object $post
     */
    public function viewMetaboxReport($post)
    {
        include_once IMAGESEO_TEMPLATES_ADMIN_METABOXES . '/report.php';
    }

    /**
     * Activate array.
     */
    public function manageMediaColumns($columns)
    {
        $columns['imageseo_alt'] = __('Alt', 'imageseo');
        // $columns['imageseo_filename'] = __('Filename', 'imageseo');

        return $columns;
    }

    protected function renderAlt($attachmentId)
    {
        $alt = wp_strip_all_tags($this->altService->getAlt($attachmentId));
        $haveAlreadyReport = $this->reportImageService->haveAlreadyReportByAttachmentId($attachmentId); ?>
        <div class="media-column-imageseo">
            <?php
            if (empty($alt)) {
                ?>
                <div class="media-column-imageseo--no_alt">
                    <span class="dashicons dashicons-dismiss"></span>
                    <span class="text"><?php esc_html_e('This image has not alt attribute !', 'imageseo'); ?>
                </div>
                <?php
            } ?>
					
            <div id="wrapper-imageseo-alt-<?php echo $attachmentId; ?>" class="wrapper-imageseo-input-alt">
                <input
                    type="text"
                    name="imageseo-alt"
                    data-id="<?php echo $attachmentId; ?>"
                    class="imageseo-alt-ajax large-text"
                    id="imageseo-alt-<?php echo $attachmentId; ?>"
                    value="<?php echo $alt; ?>"
                    placeholder="<?php echo esc_html('Enter alt attribute', 'imageseo'); ?>"
                />
                <button class="button" data-id="<?php echo $attachmentId; ?>">
                    <span><?php _e('Submit', 'imageseo'); ?></span>
                    <div class="imageseo-loading imageseo-loading--library" style="display:none"></div>
                </button>
            </div>
            <br />
            <a id="imageseo-analyze-<?php echo $attachmentId; ?>" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=imageseo_generate_alt&attachment_id=' . $attachmentId), 'imageseo_generate_alt')); ?>" class="button button-primary">
                <?php _e('Generate alt automatically', 'imageseo'); ?>
            </a>
        </div>
        <?php
    }

    public function renderFilename($attachmentId)
    {
        $filename = $this->renameFileService->getFilenameByAttachmentId($attachmentId);
        $splitFilename = explode('.', $filename);
        array_pop($splitFilename);
        $filename = implode('-', $splitFilename);
        $haveAlreadyReport = $this->reportImageService->haveAlreadyReportByAttachmentId($attachmentId); ?>
        <div class="media-column-imageseo">
            <span class="text"><?php esc_html_e("Don't use a file extension but just the name.", 'imageseo'); ?>
            <div id="wrapper-imageseo-filename-<?php echo $attachmentId; ?>" class="wrapper-imageseo-input-filename">
                <input
                    type="text"
                    name="imageseo-filename"
                    data-id="<?php echo $attachmentId; ?>"
                    class="imageseo-filename-ajax large-text"
                    id="imageseo-filename-<?php echo $attachmentId; ?>"
                    value="<?php echo $filename; ?>"
                    placeholder="<?php echo esc_html('Enter filename', 'imageseo'); ?>"
                />
                <button class="button" data-id="<?php echo $attachmentId; ?>">
                    <span><?php _e('Submit', 'imageseo'); ?></span>
                    <div class="imageseo-loading imageseo-loading--library" style="display:none"></div>
                </button>
            </div>
            <br />
            <a id="imageseo-rename-file<?php echo $attachmentId; ?>" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=imageseo_rename_attachment&attachment_id=' . $attachmentId), 'imageseo_rename_attachment')); ?>" class="button button-primary">
                <?php echo __('Rename file automatically', 'imageseo'); ?>
            </a>
        </div>
        <?php
    }

    /**
     * @param string $columnName    Name of the custom column.
     * @param int    $attachment_id Attachment ID.
     */
    public function manageMediaCustomColumn($columnName, $attachmentId)
    {
        switch ($columnName) {
            case 'imageseo_alt':
                $this->renderAlt($attachmentId);
                break;
            case 'imageseo_filename':
                $this->renderFilename($attachmentId);
                break;
        }
    }
}
