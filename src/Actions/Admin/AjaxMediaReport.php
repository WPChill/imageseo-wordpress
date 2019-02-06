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
        $this->optionServices   = imageseo_get_service('Option');
        $this->renameFileServices   = imageseo_get_service('RenameFile');
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

        add_action('admin_post_imageseo_rename_attachment', [$this, 'adminPostRenameAttachment']);
        // add_action('wp_ajax_imageseo_rename_attachment', [$this, 'ajaxReportAttachment']);
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

        return $this->reportImageServices->generateReportByAttachmentId($attachmentId);
    }

    /**
     * @return void
     */
    public function adminPostReportAttachment()
    {
        $response = $this->generateReportAttachment();
        $urlRedirect = admin_url('post.php?post=' . $this->getAttachmentId() . '&action=edit');
        if (!$response['success']) {
            wp_redirect($urlRedirect);
            return;
        }

        $activeWriteReport = $this->optionServices->getOption('active_alt_write_with_report');
        if (!$activeWriteReport) {
            wp_redirect($urlRedirect);
            return;
        }

        $this->reportImageServices->updateAltAttachmentWithReport($attachmentId, $response['result']);

        wp_redirect($urlRedirect);
    }

    /**
     * @return void
     */
    public function adminPostRenameAttachment()
    {
        $this->renameFileServices->renameAttachment($this->getAttachmentId());
        wp_redirect(admin_url('post.php?post=' . $this->getAttachmentId() . '&action=edit'));
    }

    /**
     * @return void
     */
    public function ajaxReportAttachment()
    {
        $currentBulk = (int) $_POST['current'];
        $total = (int) $_POST['total'];
        $response = $this->generateReportAttachment();

        if (!$response['success']) {
            wp_send_json_error($result);
            exit;
        }

        $attachmentId = $this->getAttachmentId();
        $report = $response['result'];


        $updateAlt = (isset($_POST['update_alt']) && $_POST['update_alt'] === 'true') ? true : false;
        $renameFile = (isset($_POST['rename_file']) && $_POST['rename_file'] === 'true') ? true : false;
        if ($updateAlt) {
            $this->reportImageServices->updateAltAttachmentWithReport($attachmentId, $report);
        }

        if ($renameFile) {
            $this->renameFileServices->renameAttachment($attachmentId);
        }

        $file =  wp_get_attachment_image_src($attachmentId, 'small');
        $altGenerate = $this->reportImageServices->getValueAttachmentWithReport($report);


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

        wp_send_json_success([
            'src' => $result['result']['src'],
            'alt_generate' => $altGenerate,
            'file' => $srcFile,
            'name_file' => $nameFile

        ]);
    }
}
