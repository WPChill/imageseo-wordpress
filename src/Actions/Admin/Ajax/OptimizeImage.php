<?php

namespace ImageSeoWP\Actions\Admin\Ajax;

if (!defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Exception\NoRenameFile;
use ImageSeoWP\Async\BulkImageBackgroundProcess;

class OptimizeImage
{
    public function __construct()
    {
        $this->tagsToStringService = imageseo_get_service('TagsToString');
        $this->renameFileService = imageseo_get_service('RenameFile');
        $this->altService = imageseo_get_service('Alt');
        $this->bulkProcess = new BulkImageBackgroundProcess();
    }

    public function hooks()
    {
        add_action('wp_ajax_imageseo_preview_data_report', [$this, 'getPreviewDataReport']);
        add_action('wp_ajax_imageseo_optimize_alt', [$this, 'optimizeAlt']);
        add_action('wp_ajax_imageseo_optimize_filename', [$this, 'optimizeFilename']);

        add_action('wp_ajax_imageseo_stop_bulk', [$this, 'stopBulk']);
        add_action('wp_ajax_imageseo_dispatch_bulk', [$this, 'dispatchBulk']);
        add_action('wp_ajax_imageseo_get_current_dispatch', [$this, 'getCurrentDispatchProcess']);
    }

    public function stopBulk()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'code' => 'not_authorized',
            ]);
            exit;
        }

        $optionBulkProcess = get_option('_imageseo_bulk_process');
        update_option('_imageseo_need_to_stop_process', true);
        update_option('_imageseo_last_bulk_process', $optionBulkProcess);

        wp_send_json_success();
    }

    public function dispatchBulk()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'code' => 'not_authorized',
            ]);
            exit;
        }

        if (!isset($_POST['data'])) {
            wp_send_json_error([
                'code' => 'missing_parameters',
            ]);

            return;
        }

        delete_option('_imageseo_last_bulk_process');

        $data = explode(',', $_POST['data']);
        update_option('_imageseo_bulk_process', [
            'total_images'         => count($data),
            'id_images'            => $data,
            'id_images_optimized'  => [],
            'current_index_image'  => 0,
            'settings'             => [
                'formatAlt'          => $_POST['formatAlt'],
                'formatAltCustom'    => 'CUSTOM_FORMAT' === $_POST['formatAltCustom'] ? true : false,
                'language'           => $_POST['language'],
                'optimizeAlt'        => 'true' === $_POST['optimizeAlt'] ? true : false,
                'optimizeFile'       => 'true' === $_POST['optimizeFile'] ? true : false,
                'wantValidateResult' => 'true' === $_POST['wantValidateResult'] ? true : false,
            ],
        ]);

        $this->bulkProcess->push_all_data($data)->save()->dispatch();

        wp_send_json_success();
    }

    public function getCurrentDispatchProcess()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'code' => 'not_authorized',
            ]);
            exit;
        }

        $infoBulkProcess = get_option('_imageseo_bulk_process');
        if (!$infoBulkProcess) {
            $infoBulkProcess = [
                'current_index_image'   => -1,
                'id_images'             => [],
                'id_images_optimized'   => [],
                'settings'              => [],
            ];
        }
        wp_send_json_success([
            'is_running'           => $this->bulkProcess->is_process_running(),
            'need_to_stop_process' => get_option('_imageseo_need_to_stop_process'),
            'bulk_process'         => $infoBulkProcess,
        ]);
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
            $filename = $this->renameFileService->getNameFileWithAttachmentId($attachmentId, $excludeFilenames);
        } catch (NoRenameFile $e) {
            $filename = $this->renameFileService->getFilenameByAttachmentId($attachmentId);
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
            $this->renameFileService->removeFilename($attachmentId);
            wp_send_json_success([
                'filename' => $filename,
            ]);

            return;
        }

        try {
            $extension = $this->renameFileService->getExtensionFilenameByAttachmentId($attachmentId);
            $filename = $this->renameFileService->validateUniqueFilename($attachmentId, $filename);

            $this->renameFileService->updateFilename($attachmentId, sprintf('%s.%s', $filename, $extension));
        } catch (\Exception $e) {
            wp_send_json_error();
        }

        wp_send_json_success([
            'filename' => $filename,
        ]);
    }
}
