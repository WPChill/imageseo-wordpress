<?php

namespace ImageSeoWP\Actions\Admin\Ajax;

if (!defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Exception\NoRenameFile;

class OptimizeImage
{
    public function __construct()
    {
        $this->tagsToStringService = imageseo_get_service('TagsToString');
        $this->generateFilename = imageseo_get_service('GenerateFilename');
        $this->altService = imageseo_get_service('Alt');
    }

    public function hooks()
    {
        add_action('wp_ajax_imageseo_preview_data_report', [$this, 'getPreviewDataReport']);
        add_action('wp_ajax_imageseo_optimize_alt', [$this, 'optimizeAlt']);
        add_action('wp_ajax_imageseo_optimize_filename', [$this, 'optimizeFilename']);

        add_action('admin_post_imageseo_force_stop', [$this, 'forceStop']);
    }

    public function forceStop()
    {
        $redirectUrl = admin_url('admin.php?page=imageseo-optimization');

        if (!wp_verify_nonce($_GET['_wpnonce'], 'imageseo_force_stop')) {
            wp_redirect($redirectUrl);
            exit;
        }

        if (!current_user_can('manage_options')) {
            wp_redirect($redirectUrl);
            exit;
        }

        $optionBulkProcess = get_option('_imageseo_bulk_process');

        if ($optionBulkProcess['current_index_image'] + 1 == $optionBulkProcess['total_images']) {
            delete_option('_imageseo_last_bulk_process');
        }

        delete_option('_imageseo_bulk_exclude_filenames');
        delete_option('_imageseo_need_to_stop_process');
        delete_option('_imageseo_bulk_process');
        delete_option('_imageseo_bulk_is_finish');

        wp_redirect($redirectUrl);
    }

    public function getPreviewDataReport()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'code' => 'not_authorized',
            ]);
            exit;
        }

        if (!isset($_POST['attachmentId'])) {
            wp_send_json_error([
                'code' => 'missing_parameters',
            ]);

            return;
        }

        $attachmentId = (int) $_POST['attachmentId'];

        $data = get_post_meta($attachmentId, '_imageseo_bulk_report', true);

        wp_send_json_success($data);
    }

    protected function getFilenameForPreview($attachmentId, $excludeFilenames = [])
    {
        try {
            $filename = $this->generateFilename->getNameFileWithAttachmentId($attachmentId, $excludeFilenames);
        } catch (NoRenameFile $e) {
            $filename = $this->generateFilename->getFilenameByAttachmentId($attachmentId);
        }

        $splitFilename = explode('.', $filename);
        if (1 === count($splitFilename)) { // Need to retrieve current extension
            $currentFilename = wp_get_attachment_image_src($attachmentId, 'full');
            $splitCurrentFilename = explode('.', $currentFilename[0]);
            $extension = $splitCurrentFilename[count($splitCurrentFilename) - 1];
        } else {
            $extension = $splitFilename[count($splitFilename) - 1];
            array_pop($splitFilename);
            $filename = implode('.', $splitFilename);
        }

        return [
            $filename,
            $extension,
        ];
    }

    public function optimizeAlt()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'code' => 'not_authorized',
            ]);
            exit;
        }

        if (!isset($_POST['attachmentId']) || !isset($_POST['alt'])) {
            wp_send_json_error([
                'code' => 'missing_parameters',
            ]);

            return;
        }

        $attachmentId = (int) $_POST['attachmentId'];
        $alt = sanitize_text_field($_POST['alt']);

        $this->altService->updateAlt($attachmentId, $alt);

        wp_send_json_success();
    }

    public function optimizeFilename()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'code' => 'not_authorized',
            ]);
            exit;
        }

        if (!isset($_POST['attachmentId']) || !isset($_POST['filename'])) {
            wp_send_json_error([
                'code' => 'missing_parameters',
            ]);

            return;
        }

        $attachmentId = (int) $_POST['attachmentId'];
        $filename = sanitize_title($_POST['filename']);

        if (empty($filename)) {
            $this->generateFilename->removeFilename($attachmentId);
            wp_send_json_success([
                'filename' => $filename,
            ]);

            return;
        }

        try {
            $extension = $this->generateFilename->getExtensionFilenameByAttachmentId($attachmentId);
            $filename = $this->generateFilename->validateUniqueFilename($attachmentId, $filename);

            imageseo_get_service('UpdateFile')->updateFilename($attachmentId, sprintf('%s.%s', $filename, $extension));
        } catch (\Exception $e) {
            wp_send_json_error();
        }

        wp_send_json_success([
            'filename' => $filename,
        ]);
    }
}
