<?php

namespace ImageSeoWP\Services;

if (! defined('ABSPATH')) {
    exit;
}

use ImageSeo\Client\Client;

use ImageSeoWP\Helpers\AttachmentMeta;
use ImageSeoWP\Helpers\AltTags;
use ImageSeoWP\Helpers\RenameTags;

class ReportImage
{
    public function __construct()
    {
        $this->clientServices = imageseo_get_service('ClientApi');
        $this->optionServices = imageseo_get_service('Option');
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
        $mimeType = get_post_mime_type($attachmentId);
        if (strpos($mimeType, 'image') === false) {
            return;
        }

        $filePath = get_attached_file($attachmentId);
        $metadata = wp_get_attachment_metadata($attachmentId);

        $reportImages = $this->clientServices->getClient()->getResource('report_images');

        $result = $reportImages->generateReportFromFile([
            'filePath' => $filePath,
            'width' => (is_array($metadata) && !empty($metadata)) ?  $metadata['width'] : '',
            'height' => (is_array($metadata) && !empty($metadata)) ?  $metadata['height'] : '',
        ]);

        if ($result && !$result['success']) {
            return $result;
        }

        $report = $result['result'];
        update_post_meta($attachmentId, AttachmentMeta::DATE_REPORT, time());
        update_post_meta($attachmentId, AttachmentMeta::REPORT, $report);

        return $report;
    }

    /**
     * @param int $attachmentId
     * @param array $report
     * @return void
     */
    public function updateAltAttachmentWithReport($attachmentId, $report)
    {
        $alt = $this->getValueAttachmentWithReport($report);
        update_post_meta($attachmentId, '_wp_attachment_image_alt', $alt);
    }

    /**
     * @param array $report
     * @return string
     */
    public function getValueAttachmentWithReport($report, $fromOption = 'alt_value')
    {
        $value = $this->optionServices->getOption($fromOption);

        $value = str_replace(AltTags::SITE_TITLE, get_bloginfo('title'), $value);
        $value = str_replace(AltTags::ALT_AUTO_CONTEXT, $this->getAutoContextFromReport($report), $value);
        $value = str_replace(AltTags::ALT_AUTO_REPRESENTATION, $this->getAutoRepresentationFromReport($report), $value);

        return $value;
    }

    /**
     * @param int $attachmentId
     * @return string
     */
    public function getNameFileAttachmentWithId($attachmentId)
    {
        $report = $this->getReportByAttachmentId($attachmentId);
        $value = $this->getValueAttachmentWithReport($report, 'rename_value');
        // $value = str_replace(RenameTags::ATTACHED_POST_TITLE, $this->getAutoRepresentationFromReport($report), $value);

        return $value;
    }


    /**
     * @param array $report
     * @return string
     */
    public function getAutoContextFromReport($report)
    {
        return $this->getValueAuto('alts', $report);
    }

    /**
     * @param array $report
     * @return string
     */
    public function getAutoRepresentationFromReport($report)
    {
        return $this->getValueAuto('labels', $report);
    }

    /**
     * @param string $type
     * @param array $report
     * @return string
     */
    protected function getValueAuto($type, $report)
    {
        $altPercent = $this->optionServices->getOption('alt_auto_percent');

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

    /**
     * @param int $id
     * @return string
     */
    public function getAlt($id)
    {
        return get_post_meta($id, '_wp_attachment_image_alt', true);
    }
}
