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
        $this->reportImageServices = imageseo_get_service('ReportImage');
        $this->optionServices = imageseo_get_service('Option');
    }

    public function hooks()
    {
        if (!imageseo_allowed()) {
            return;
        }

        add_action('wp_ajax_imageseo_generate_report', [$this, 'generateReport']);
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
        $language = isset($_POST['language']) ? sanitize_text_field($_POST['language']) : $this->optionServices->getOption('default_language_ia');

        $report = $this->reportImageServices->getReportByAttachmentId($attachmentId);

        if ($report) {
            $report['ID'] = $attachmentId;
            wp_send_json_success([
                'need_update_counter' => false,
                'report'              => $report,
            ]);

            return;
        }

        try {
            $response = $this->reportImageServices->generateReportByAttachmentId($attachmentId, ['force' => true], $language);
        } catch (\Exception $e) {
            wp_send_json_error([
                'code'    => 'error_generate_report',
                'message' => $e->getMessage(),
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
}
