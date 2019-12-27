<?php

namespace ImageSeoWP\Actions\Admin\Ajax;

if (!defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Exception\NoRenameFile;

/**
 * @since 1.0.0
 */
class Report
{
    /**
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->reportImageService = imageseo_get_service('ReportImage');
        $this->altService = imageseo_get_service('Alt');
        $this->renameFileService = imageseo_get_service('RenameFile');
    }

    public function hooks()
    {
        if (!imageseo_allowed()) {
            return;
        }

        add_action('wp_ajax_imageseo_report_attachment', [$this, 'ajaxReport']);
        add_action('wp_ajax_imageseo_generate_report', [$this, 'generateReport']);
    }

    /**
     * @return int
     */
    protected function getAttachmentId()
    {
        if ('GET' === $_SERVER['REQUEST_METHOD']) {
            return (int) $_GET['attachment_id'];
        } elseif ('POST' === $_SERVER['REQUEST_METHOD']) {
            return(int) $_POST['attachment_id'];
        }
    }

    public function generateReport()
    {
        if (!isset($_POST['attachmentId'])) {
            wp_send_json_error([
                'code' => 'missing_parameters',
            ]);

            return;
        }

        $attachmentId = (int) $_POST['attachmentId'];

        $report = $this->reportImageService->getReportByAttachmentId($attachmentId);

        if ($report) {
            $report['ID'] = $attachmentId;
            wp_send_json_success($report);

            return;
        }

        try {
            $response = $this->reportImageService->generateReportByAttachmentId($attachmentId);
        } catch (\Exception $e) {
            wp_send_json_error([
                'code' => 'error_generate_report',
            ]);

            return;
        }

        if (!$response['success']) {
            wp_send_json_error([
                'code' => 'error_generate_report',
            ]);

            return;
        }

        $report = $response['result'];
        $report['ID'] = $attachmentId;
        wp_send_json_success($report);
    }

    /**
     * @param array $query
     *
     * @return array
     */
    protected function generateReportAttachment($query = [])
    {
        if (!isset($_GET['attachment_id']) && !isset($_POST['attachment_id'])) {
            return [
                'success' => false,
            ];
        }

        $attachmentId = $this->getAttachmentId();

        $report = $this->reportImageService->getReportByAttachmentId($attachmentId);
        if ($report) {
            return [
                'success' => true,
                'result'  => $report,
            ];
        }

        return $this->reportImageService->generateReportByAttachmentId($attachmentId, $query);
    }

    public function ajaxReport()
    {
        $currentBulk = (int) $_POST['current'];
        $total = (int) $_POST['total'];

        try {
            $response = $this->generateReportAttachment();
        } catch (\Exception $e) {
            wp_send_json_error([
                'code' => 'error_generate_report',
            ]);
            exit;
        }

        if (!$response['success']) {
            wp_send_json_error($response);
            exit;
        }

        $attachmentId = $this->getAttachmentId();
        $report = $response['result'];

        $updateAlt = (isset($_POST['update_alt']) && 'true' === $_POST['update_alt']) ? true : false;
        $updateAltNotEmpty = (isset($_POST['update_alt_not_empty']) && 'true' === $_POST['update_alt_not_empty']) ? true : false;
        $renameFile = (isset($_POST['rename_file']) && 'true' === $_POST['rename_file']) ? true : false;
        $currentAlt = $this->altService->getAlt($attachmentId);
        $currentFile = wp_get_attachment_image_src($attachmentId, 'small');
        $altGenerate = $this->altService->getAltValueAttachmentWithReport($report);

        $currentNameFile = '';
        if (!empty($currentFile)) {
            $currentNameFile = basename($currentFile[0]);
        }

        if ($updateAlt || $updateAltNotEmpty) {
            if (($updateAlt && !$currentAlt) || $updateAltNotEmpty) {
                $this->altService->updateAltAttachmentWithReport($attachmentId, $report);
            }
        }

        $newFilePath = false;
        if ($renameFile) {
            $this->renameFileService->renameAttachment($attachmentId);
            $file = wp_get_attachment_image_src($attachmentId, 'small');
            if (!empty($file)) {
                $newFilePath = basename($file[0]);
            }
        }

        $file = wp_get_attachment_image_src($attachmentId, 'small');

        if ($currentBulk + 1 < $total) {
            update_option('_imageseo_current_processed', $currentBulk);
        } elseif ($currentBulk + 1 === $total) {
            delete_option('_imageseo_current_processed');
        }

        $srcFile = '';
        $nameFile = '';
        if (!empty($file)) {
            $srcFile = $file[0];
            $nameFile = basename($srcFile);
        }

        if (!$newFilePath) {
            $basenameWithoutExt = explode('.', $nameFile)[0];
            try {
                $newFilePath = sprintf('%s.%s', $this->renameFileService->getNameFileWithAttachmentId($attachmentId), explode('.', $nameFile)[1]);
            } catch (NoRenameFile $e) {
                $newFilePath = $nameFile;
            }
        }

        wp_send_json_success([
            'src'               => $report['src'],
            'current_alt'       => $currentAlt,
            'alt_generate'      => $altGenerate,
            'file_generate'     => $newFilePath,
            'file'              => $srcFile,
            'current_name_file' => $currentNameFile,
            'name_file'         => $nameFile,
        ]);
    }
}
