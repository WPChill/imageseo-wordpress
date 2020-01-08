<?php

namespace ImageSeoWP\Actions\Admin\Ajax;

if (!defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Exception\NoRenameFile;

class Optimize
{
    public function __construct()
    {
        $this->tagsToStringServices = imageseo_get_service('TagsToString');
        $this->reportImageServices = imageseo_get_service('ReportImage');
        $this->renameFileServices = imageseo_get_service('RenameFile');
        $this->altServices = imageseo_get_service('Alt');
    }

    public function hooks()
    {
        add_action('wp_ajax_imageseo_preview_optimize_alt', [$this, 'getPreviewAlt']);
        add_action('wp_ajax_imageseo_preview_optimize_filename', [$this, 'getPreviewFilename']);
        add_action('wp_ajax_imageseo_optimize_alt', [$this, 'optimizeAlt']);
        add_action('wp_ajax_imageseo_optimize_filename', [$this, 'optimizeFilename']);
        add_action('wp_ajax_imageseo_save_current_bulk', [$this, 'saveCurrentBulk']);
        add_action('wp_ajax_imageseo_delete_current_bulk', [$this, 'deleteCurrentBulk']);
    }

    public function saveCurrentBulk()
    {
        if (!isset($_POST['settings']) || !isset($_POST['state']) || !isset($_POST['countOptimized'])) {
            wp_send_json_error([
                'code' => 'missing_parameters',
            ]);

            return;
        }

        $now = new \DateTime('now');
        update_option('_imageseo_current_processed', [
            'settings'        => json_decode(stripslashes($_POST['settings'])),
            'state'           => json_decode(stripslashes($_POST['state'])),
            'count_optimized' => (int) $_POST['countOptimized'],
            'last_updated'    => $now->format('Y-m-d H:i:s'),
        ]);

        wp_send_json_success();
    }

    public function deleteCurrentBulk()
    {
        delete_option('_imageseo_current_processed');
        wp_send_json_success();
    }

    public function getPreviewAlt()
    {
        if (!isset($_POST['attachmentId']) || !isset($_POST['altTemplate'])) {
            wp_send_json_error([
                'code' => 'missing_parameters',
            ]);

            return;
        }

        $attachmentId = (int) $_POST['attachmentId'];
        $template = sanitize_text_field($_POST['altTemplate']);
        $alt = $this->tagsToStringServices->replace($template, $attachmentId);

        wp_send_json_success($alt);
    }

    public function getPreviewFilename()
    {
        if (!isset($_POST['attachmentId'])) {
            wp_send_json_error([
                'code' => 'missing_parameters',
            ]);

            return;
        }

        $attachmentId = (int) $_POST['attachmentId'];

        $excludeFilenames = [];
        try {
            $excludeFilenames = isset($_POST['excludeFilenames']) ? json_decode(stripslashes($_POST['excludeFilenames']), true) : [];
        } catch (\Exception $e) {
            $excludeFilenames = [];
        }

        try {
            $filename = $this->renameFileServices->getNameFileWithAttachmentId($attachmentId, $excludeFilenames);
        } catch (NoRenameFile $e) {
            $filename = $this->renameFileServices->getFilenameByAttachmentId($attachmentId);
        }

        $splitFilename = explode('.', $filename);
        if (1 === count($splitFilename)) {
            $currentFilename = wp_get_attachment_image_src($attachmentId, 'full');
            $splitCurrentFilename = explode('.', $currentFilename[0]);
            $extension = $splitCurrentFilename[count($splitCurrentFilename) - 1];
        } else {
            $extension = $splitFilename[count($splitFilename) - 1];
        }

        wp_send_json_success([
            'filename'  => $filename,
            'extension' => $extension,
        ]);
    }

    public function optimizeAlt()
    {
        if (!isset($_POST['attachmentId']) || !isset($_POST['alt'])) {
            wp_send_json_error([
                'code' => 'missing_parameters',
            ]);

            return;
        }

        $attachmentId = (int) $_POST['attachmentId'];
        $alt = sanitize_text_field($_POST['alt']);

        $this->altServices->updateAlt($attachmentId, $alt);

        wp_send_json_success();
    }

    public function optimizeFilename()
    {
        if (!isset($_POST['attachmentId']) || !isset($_POST['filename'])) {
            wp_send_json_error([
                'code' => 'missing_parameters',
            ]);

            return;
        }

        $attachmentId = (int) $_POST['attachmentId'];
        $filename = sanitize_text_field($_POST['filename']);

        try {
            $this->renameFileServices->updateFilename($attachmentId, $filename);
        } catch (\Exception $e) {
            wp_send_json_error();
        }

        wp_send_json_success();
    }
}
