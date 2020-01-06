<?php

namespace ImageSeoWP\Actions\Admin\Ajax;

if (!defined('ABSPATH')) {
    exit;
}

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
            wp_send_json_success([
                'need_update_counter' => false,
                'report'              => $report,
            ]);

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
        wp_send_json_success([
            'need_update_counter' => true,
            'report'              => $report,
        ]);
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
}
