<?php

namespace ImageSeoWP\Services;

if (! defined('ABSPATH')) {
    exit;
}

use ImageSeo\Client\Client;

use ImageSeoWP\Helpers\AttachmentMeta;
use ImageSeoWP\Helpers\AltTags;

class ReportImage
{
    public function __construct()
    {
        $this->clientService = imageseo_get_service('ClientApi');
        $this->optionService = imageseo_get_service('Option');
    }

    /**
     * @param int $attachmentId
     * @return bool
     */
    public function haveAlreadyReportByAttachmentId($attachmentId)
    {
        return $this->getReportByAttachmentId($attachmentId) ? true : false;
    }

    /**
     * @param int $attachmentId
     * @return array
     */
    public function getReportByAttachmentId($attachmentId)
    {
        return get_post_meta($attachmentId, AttachmentMeta::REPORT, true);
    }

    /**
     * @param int $attachmentId
     * @return array
     */
    public function generateReportByAttachmentId($attachmentId)
    {
        $filePath = get_attached_file($attachmentId);
        $metadata = wp_get_attachment_metadata($attachmentId);

        $reportImages = $this->clientService->getClient()->getResource('api_keys_images');
        $result = $reportImages->generateReportFromFile([
            'filePath' => $filePath,
            'width' => $metadata['width'],
            'height' => $metadata['height'],
        ]);

        if ($result && !$result['success']) {
            return $result;
        }

        $report = $result['result'];
        update_post_meta($attachmentId, AttachmentMeta::DATE_REPORT, time());
        update_post_meta($attachmentId, AttachmentMeta::REPORT, $report);

        $this->updateAltAttachmentWithReport($attachmentId, $report);

        return $result;
    }

    /**
     * @param int $attachmentId
     * @param array $report
     * @return void
     */
    public function updateAltAttachmentWithReport($attachmentId, $report)
    {
        $alt = $this->getAltAttachmentWithReport($report);
        update_post_meta($attachmentId, '_wp_attachment_image_alt', $alt);
    }

    /**
     * @param array $report
     * @return string
     */
    public function getAltAttachmentWithReport($report)
    {
        $altValue = $this->optionService->getOption('alt_value');

        $alt = str_replace(AltTags::SITE_TITLE, get_bloginfo('title'), $altValue);
        $alt = str_replace(AltTags::ALT_AUTO_CONTEXT, $this->getAltAutoContextFromReport($report), $alt);
        $alt = str_replace(AltTags::ALT_AUTO_REPRESENTATION, $this->getAltAutoRepresentationFromReport($report), $alt);

        return $alt;
    }

    /**
     * @param array $report
     * @return string
     */
    public function getAltAutoContextFromReport($report)
    {
        return $this->getAltAuto('alts', $report);
    }

    /**
     * @param array $report
     * @return string
     */
    public function getAltAutoRepresentationFromReport($report)
    {
        return $this->getAltAuto('labels', $report);
    }

    /**
     * @param string $type
     * @param array $report
     * @return string
     */
    protected function getAltAuto($type, $report)
    {
        $altPercent = $this->optionService->getOption('alt_auto_percent');

        $altReplace = '';
        $i = 0;
        $total = count($report[$type]);

        while (empty($altReplace) && $i < $total) {
            if ($altPercent > $report[$type][$i]['score']) {
                $i++;
                continue;
            }

            $altReplace = $report[$type][$i]['name'];
            $i++;
        }

        return $altReplace;
    }
}
