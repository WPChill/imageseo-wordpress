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
        $this->altServices = imageseo_get_service('Alt');
    }

    public function hooks()
    {
        if (!imageseo_allowed()) {
            return;
        }

        add_filter('manage_media_columns', [$this, 'manageMediaColumns']);
        add_filter('attachment_fields_to_edit', [$this, 'fieldsEdit'], 999, 2);
        add_action('attachment_fields_to_save', [$this, 'saveDataPinterest'], 10, 2);
        add_action('manage_media_custom_column', [$this, 'manageMediaCustomColumn'], 10, 2);

        add_action('wp_ajax_imageseo_media_alt_update', [$this, 'ajaxAltUpdate']);

        add_action('admin_init', [$this, 'metaboxReport']);
        add_action('add_attachment', [$this, 'addAltOnUpload']);
        add_action('add_attachment', [$this, 'updateCount'], 100);
        add_filter('wp_generate_attachment_metadata', [$this, 'renameFileOnUpload'], 10, 2);

        // add_action('admin_menu', [$this, 'addMediaPage']);
    }

    public function muteOnUpload()
    {
        remove_action('add_attachment', [$this, 'addAltOnUpload']);
        remove_filter('wp_generate_attachment_metadata', [$this, 'renameFileOnUpload'], 10, 2);
    }

    public function addMediaPage()
    {
        add_media_page('Image SEO', 'Image SEO', 'manage_options', 'imageseo_media_files', [$this, 'adminMediaFiles']);
    }

    public function adminMediaFiles()
    {
        include_once IMAGESEO_TEMPLATES_ADMIN_PAGES . '/media_library.php';
    }

    /**
     * @param int $postId
     */
    public function addAltOnUpload($attachmentId)
    {
        if (!wp_attachment_is_image($attachmentId)) {
            return;
        }

        $activeWriteReport = $this->optionService->getOption('active_alt_write_upload');

        if (!$activeWriteReport) {
            return;
        }

        try {
            $response = $this->reportImageService->generateReportByAttachmentId($attachmentId, ['force' => true]);
        } catch (\Exception $e) {
            return;
        }

        if (!$response['success']) {
            return;
        }

        $this->altServices->updateAltAttachmentWithReport($attachmentId);
    }

    public function updateCount($attachmentId)
    {
        if (!wp_attachment_is_image($attachmentId)) {
            return;
        }

        $alt = $this->altServices->getAlt($attachmentId);
        if (empty($alt)) {
            $total = get_option('imageseo_get_number_image_non_optimize_alt');
            if ($total) {
                update_option('imageseo_get_number_image_non_optimize_alt', (int) $total + 1);
            }
        }

        $total = get_option('imageseo_get_total_images');
        if ($total) {
            update_option('imageseo_get_total_images', (int) $total + 1);
        }
    }

    /**
     * @param array $metadata
     * @param int   $attachmentId
     *
     * @return array
     */
    public function renameFileOnUpload($metadata, $attachmentId)
    {
        if (!wp_attachment_is_image($attachmentId)) {
            return $metadata;
        }

        $activeWriteReport = $this->optionService->getOption('active_rename_write_upload');

        if (!$activeWriteReport) {
            return $metadata;
        }

        $response = $this->reportImageService->generateReportByAttachmentId($attachmentId, ['force' => true]);
        if (!$response['success']) {
            return $metadata;
        }

        $result = $this->renameFileService->renameAttachment($attachmentId, $metadata);
        if (array_key_exists('metadata', $result)) {
            $metadata = $result['metadata'];
        }

        return $metadata;
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

        update_post_meta($postId, '_wp_attachment_image_alt', $alt);
    }

    /**
     * @param array  $formFields
     * @param object $post
     *
     * @return array
     */
    public function fieldsEdit($formFields, $post)
    {
        global $pagenow;

        $formFields['imageseo-data-pin-description'] = [
            'label'         => __('Pinterest description', 'imageseo'),
            'input'         => 'textarea',
            'value' 		      => get_post_meta($post->ID, '_imageseo_data_pin_description', true),
            'show_in_edit'  => true,
            'show_in_modal' => true,
            'helps'         => '&lt;img src="#" data-pin-description="My description" /&gt;',
        ];
        $formFields['imageseo-data-pin-url'] = [
            'label'         => __('Pinterest URL', 'imageseo'),
            'input'         => 'text',
            'value' 		      => get_post_meta($post->ID, '_imageseo_data_pin_url', true),
            'show_in_edit'  => true,
            'show_in_modal' => true,
            'helps'         => '&lt;img src="#" data-pin-url="https://imageseo.io" /&gt;',
        ];
        $formFields['imageseo-data-pin-id'] = [
            'label'         => __('Pinterest ID', 'imageseo'),
            'input'         => 'text',
            'value' 		      => get_post_meta($post->ID, '_imageseo_data_pin_id', true),
            'show_in_edit'  => true,
            'show_in_modal' => true,
            'helps'         => '&lt;img src="#" data-pin-id="id-pin" /&gt;',
        ];
        $formFields['imageseo-data-pin-media'] = [
            'label'         => __('Pinterest Media', 'imageseo'),
            'input'         => 'text',
            'value' 		      => get_post_meta($post->ID, '_imageseo_data_pin_media', true),
            'show_in_edit'  => true,
            'show_in_modal' => true,
            'helps'         => '&lt;img src="#"  data-pin-media="https://example.com/my-image.jpg" /&gt;',
        ];

        if ('post.php' !== $pagenow) {
            $formFields['imageseo-has-report'] = [
                'label'         => __('ImageSEO Report', 'imageseo'),
                'input'         => 'html',
                'html'          => '<a id="imageseo-' . $post->ID . '" href="' . esc_url(admin_url('post.php?post=' . $post->ID . '&action=edit')) . '" class="button">' . __('View report', 'imageseo') . '</a>',
                'show_in_edit'  => true,
                'show_in_modal' => true,
            ];
        }

        return $formFields;
    }

    public function saveDataPinterest($post, $attachment)
    {
        if (isset($attachment['imageseo-data-pin-description'])) {
            update_post_meta($post['ID'], '_imageseo_data_pin_description', $attachment['imageseo-data-pin-description']);
        }
        if (isset($attachment['imageseo-data-pin-url'])) {
            update_post_meta($post['ID'], '_imageseo_data_pin_url', $attachment['imageseo-data-pin-url']);
        }
        if (isset($attachment['imageseo-data-pin-id'])) {
            update_post_meta($post['ID'], '_imageseo_data_pin_id', $attachment['imageseo-data-pin-id']);
        }
        if (isset($attachment['imageseo-data-pin-media'])) {
            update_post_meta($post['ID'], '_imageseo_data_pin_media', $attachment['imageseo-data-pin-media']);
        }

        return $post;
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
        $columns['imageseo_filename'] = __('Filename', 'imageseo');

        return $columns;
    }

    protected function renderAlt($attachmentId)
    {
        $alt = wp_strip_all_tags($this->altServices->getAlt($attachmentId));
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
