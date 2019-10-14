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
     * @param array $query
     * @return array
     */
    public function generateReportByAttachmentId($attachmentId, $query = [])
    {
        if (!apply_filters('imageseo_authorize_generate_report_attachment_id', true, $attachmentId)) {
            return;
        }

        $mimeType = get_post_mime_type($attachmentId);
        if (strpos($mimeType, 'image') === false) {
            return;
        }

        $filePath = get_attached_file($attachmentId);
        $metadata = wp_get_attachment_metadata($attachmentId);

        $reportImages = $this->clientServices->getClient()->getResource('ImageReports', $query);

        if (file_exists($filePath)) {
            try {
                $result = $reportImages->generateReportFromFile([
                    'lang' => $this->optionServices->getOption('default_language_ia'),
                    'filePath' => $filePath,
                    'width' => (is_array($metadata) && !empty($metadata)) ?  $metadata['width'] : '',
                    'height' => (is_array($metadata) && !empty($metadata)) ?  $metadata['height'] : '',
                ], $query);
            } catch (\Exception $th) {
                $result = $reportImages->generateReportFromUrl([
                    'lang' => $this->optionServices->getOption('default_language_ia'),
                    'src' => $filePath,
                    'width' => (is_array($metadata) && !empty($metadata)) ?  $metadata['width'] : '',
                    'height' => (is_array($metadata) && !empty($metadata)) ?  $metadata['height'] : '',
                ], $query);
            }
        } else {
            $result = $reportImages->generateReportFromUrl([
                'lang' => $this->optionServices->getOption('default_language_ia'),
                'src' => $filePath,
                'width' => (is_array($metadata) && !empty($metadata)) ?  $metadata['width'] : '',
                'height' => (is_array($metadata) && !empty($metadata)) ?  $metadata['height'] : '',
            ], $query);
        }

        if ($result && !$result['success']) {
            return $result;
        }

        $report = $result['result'];
        update_post_meta($attachmentId, AttachmentMeta::DATE_REPORT, time());
        update_post_meta($attachmentId, AttachmentMeta::REPORT, $report);

        return $result;
    }

    /**
     * @param int $attachmentId
     * @param array $report
     * @return void
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
     * @return string
     */
    public function getAltValueAttachmentWithReport($report)
    {
        $keysContext = $this->getAutoContextFromReport($report);

        $alt = $keysContext;
        if (array_key_exists('captions', $report) && !empty($report['captions'])) {
            $alt = sprintf('%s - %s', $report['a_vision']['description']['captions'][0]['text'], $alt);
        }

        return apply_filters('imageseo_get_alt_value_attachment_with_report', ucfirst($alt), $report);
    }

    /**
     * @param int $attachmentId
     * @return string
     */
    public function getNameFileAttachmentWithId($attachmentId, $params = [])
    {
        $report = $this->getReportByAttachmentId($attachmentId);
        $nameAuto = $this->getAutoContextFromReport($report, $params);

        return apply_filters('imageseo_get_name_file_attachment_id', $nameAuto, $attachmentId);
    }


    /**
     * @param array $report
     * @return string
     */
    public function getAutoContextFromReport($report, $params = [])
    {
        return $this->getValueAuto('alts', $report, $params);
    }

    /**
     * @param array $report
     * @return string
     */
    public function getAutoRepresentationFromReport($report, $params =[])
    {
        return $this->getValueAuto('labels', $report, $params);
    }

    /**
     * @param string $type
     * @param array $report
     * @param array $params
     * @return string
     */
    protected function getValueAuto($type, $report, $params = [])
    {
        if (!isset($params['min_percent'])) {
            $params['min_percent'] = apply_filters('imageseo_get_value_auto_min_percent', 1, $type, $report);
        }
        if (!isset($params['max_percent'])) {
            $params['max_percent'] = apply_filters('imageseo_get_value_auto_max_percent', 101, $type, $report);
        }

        $altReplace = '';
        $i = 0;
        $total = count($report[$type]);

        while (empty($altReplace) && $i < $total) {
            if (null === $report[$type][$i]['score']) {
                $altReplace = $report[$type][$i]['name'];
                $i++;
                continue;
            }

            if ($params['min_percent'] >= round($report[$type][$i]['score']) || $params['max_percent'] <= round($report[$type][$i]['score'])) {
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
