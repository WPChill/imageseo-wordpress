<?php

namespace ImageSeoWP\Services;

if (!defined('ABSPATH')) {
    exit;
}

class Alt
{
    public function __construct()
    {
        $this->reportImageService = imageseo_get_service('ReportImage');
        $this->optionServices = imageseo_get_service('Option');
        $this->tagsToStringServices = imageseo_get_service('TagsToString');
    }

    /**
     * @param int $attachmentId
     */
    public function generateForAttachmentId($attachmentId, $query = [])
    {
        if (!isset($_GET['attachment_id']) && !isset($_POST['attachment_id'])) {
            return [
                'success' => false,
            ];
        }

        $report = $this->reportImageService->getReportByAttachmentId($attachmentId);
        if (!$report) {
            try {
                $response = $this->reportImageService->generateReportByAttachmentId($attachmentId, $query);
            } catch (\Exception $e) {
                return [
                    'success' => false,
                ];
            }

            if (!$response['success']) {
                return $response;
            }

            $report = $response['result'];
        }

        $this->updateAltAttachmentWithReport($attachmentId);

        return [
            'success' => true,
        ];
    }

    /**
     * @param int   $attachmentId
     * @param array $report
     */
    public function updateAltAttachmentWithReport($attachmentId)
    {
        $template = $this->optionServices->getOption('alt_template_default');
        $alt = $this->tagsToStringServices->replace($template, $attachmentId);
        $alt = apply_filters('imageseo_update_alt_attachment_value', $alt, $attachmentId);

        update_post_meta($attachmentId, '_wp_attachment_image_alt', $alt);
    }

    public function updateAlt($attachmentId, $alt)
    {
        update_post_meta($attachmentId, '_wp_attachment_image_alt', apply_filters('imageseo_update_alt', $alt, $attachmentId));
    }

    /**
     * @param int $id
     *
     * @return string
     */
    public function getAlt($id)
    {
        return get_post_meta($id, '_wp_attachment_image_alt', true);
    }
}
