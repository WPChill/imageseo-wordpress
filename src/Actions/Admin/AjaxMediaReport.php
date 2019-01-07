<?php

namespace ImageSeoWP\Actions\Admin;

if (! defined('ABSPATH')) {
    exit;
}

/**
 * @since 1.0.0
 */
class AjaxMediaReport
{

    /**
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->reportImageServices   = imageseo_get_service('ReportImage');
    }

    /**
     * @return void
     */
    public function hooks()
    {
        if (!imageseo_allowed()) {
            return;
        }

        add_action('admin_post_imageseo_report_attachment', [$this, 'adminPostReportAttachment']);
        add_action('wp_ajax_imageseo_report_attachment', [$this, 'ajaxReportAttachment']);
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
     * @return array
     */
    protected function generateReportAttachment()
    {
        if (!isset($_GET['attachment_id']) && !isset($_POST['attachment_id'])) {
            return [
                'success' => false
            ];
        }

        $attachmentId = $this->getAttachmentId();


        $report = $this->reportImageServices->generateReportByAttachmentId($attachmentId);
        $activeWriteReport = $this->optionServices->getOption('active_alt_write_with_report');

        if ($report && $activeWriteReport) {
            $this->reportImageServices->updateAltAttachmentWithReport($attachmentId, $report);
        }

        return $report;
    }

    /**
     * @return void
     */
    public function adminPostReportAttachment()
    {
        $result = $this->generateReportAttachment();
        wp_redirect(admin_url('post.php?post=' . $this->getAttachmentId() . '&action=edit'));
    }

    /**
     * @return void
     */
    public function ajaxReportAttachment()
    {
        $result = $this->generateReportAttachment();

        if ($result['success']) {
            $attachmentId = $this->getAttachmentId();
            $file =  wp_get_attachment_image_src($attachmentId, 'small');
            $altGenerate = $this->reportImageServices->getAltValueAttachmentWithReport($result['result']);
            wp_send_json_success([
                'src' => $result['result']['src'],
                'alt_generate' => $altGenerate,
                'file' => (!empty($file)) ? $file[0] : ''

            ]);
            exit;
        }

        wp_send_json_error($result);
        exit;
    }
}
