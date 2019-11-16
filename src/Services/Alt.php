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
            $response = $this->reportImageService->generateReportByAttachmentId($attachmentId, $query);

            if (!$response['success']) {
                return $response;
            }

            $report = $response['result'];
        }

        $this->updateAltAttachmentWithReport($attachmentId, $report);

        return [
            'success' => true,
        ];
    }

    /**
     * @param int   $attachmentId
     * @param array $report
     */
    public function updateAltAttachmentWithReport($attachmentId, $report)
    {
        $alt = $this->getAltValueAttachmentWithReport($report);

        $postId = wp_get_post_parent_id($attachmentId);
        if ($postId) {
            $alt = sprintf('%s - %s', get_the_title($postId), $alt);
        }

        $alt = apply_filters('imageseo_update_alt_attachment_value', $alt, $attachmentId, $report);

        update_post_meta($attachmentId, '_wp_attachment_image_alt', $alt);
    }

    /**
     * @param array $report
     *
     * @return string
     */
    public function getAltValueAttachmentWithReport($report)
    {
        $keysContext = $this->reportImageService->getAutoContextFromReport($report);

        $alt = $keysContext;
        if (array_key_exists('captions', $report) && !empty($report['captions'])) {
            $alt = sprintf('%s - %s', $report['a_vision']['description']['captions'][0]['text'], $alt);
        }

        return apply_filters('imageseo_get_alt_value_attachment_with_report', ucfirst($alt), $report);
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
