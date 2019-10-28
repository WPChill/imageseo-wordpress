<?php

namespace ImageSeoWP\Actions\Admin\Ajax;

if (! defined('ABSPATH')) {
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
        $this->reportImageService   = imageseo_get_service('ReportImage');
        $this->altService   = imageseo_get_service('Alt');
        $this->renameFileService   = imageseo_get_service('RenameFile');
    }

    /**
     * @return void
     */
    public function hooks()
    {
        if (!imageseo_allowed()) {
            return;
        }

        add_action('wp_ajax_imageseo_report_attachment', [$this, 'ajaxReport']);

    }

    /**
     * @return int
     */
    protected function getAttachmentId()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return (int) $_GET['attachment_id'];
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return(int) $_POST['attachment_id'];
        }
    }

    /**
     * @param array $query
     * @return array
     */
    protected function generateReportAttachment($query = [])
    {
        if (!isset($_GET['attachment_id']) && !isset($_POST['attachment_id'])) {
            return [
                'success' => false
            ];
        }

        $attachmentId = $this->getAttachmentId();

        $report = $this->reportImageService->getReportByAttachmentId($attachmentId);
        if ($report) {
            return [
                "success" => true,
                "result" => $report
            ];
        }
                
        return $this->reportImageService->generateReportByAttachmentId($attachmentId, $query);
    }
  

    /**
     * @return void
     */
    public function ajaxReport()
    {
        $currentBulk = (int) $_POST['current'];
        $total = (int) $_POST['total'];

        try {
            $response = $this->generateReportAttachment();
        } catch (\Exception $e) {
            wp_send_json_error([
                "code" => "error_generate_report"
            ]);
            exit;
        }

        if (!$response['success']) {
            wp_send_json_error($response);
            exit;
        }

        $attachmentId = $this->getAttachmentId();
        $report = $response['result'];

        $updateAlt = (isset($_POST['update_alt']) && $_POST['update_alt'] === 'true') ? true : false;
        $updateAltNotEmpty = (isset($_POST['update_alt_not_empty']) && $_POST['update_alt_not_empty'] === 'true') ? true : false;
        $renameFile = (isset($_POST['rename_file']) && $_POST['rename_file'] === 'true') ? true : false;
        $currentAlt = $this->altService->getAlt($attachmentId);
        $currentFile =  wp_get_attachment_image_src($attachmentId, 'small');
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
            $file =  wp_get_attachment_image_src($attachmentId, 'small');
            if (!empty($file)) {
                $newFilePath = basename($file[0]);
            }
        }

        $file =  wp_get_attachment_image_src($attachmentId, 'small');

        if ($currentBulk+1 < $total) {
            update_option('_imageseo_current_processed', $currentBulk);
        } elseif ($currentBulk+1 === $total) {
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
            'src' => $report['src'],
            'current_alt' => $currentAlt,
            'alt_generate' => $altGenerate,
            'file_generate' => $newFilePath,
            'file' => $srcFile,
            'current_name_file' => $currentNameFile,
            'name_file' => $nameFile

        ]);
    }
}
