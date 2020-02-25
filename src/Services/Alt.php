<?php

namespace ImageSeoWP\Services;

if (!defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Async\QueryImagesNoAltBackgroundProcess;

class Alt
{
    public function __construct()
    {
        $this->reportImageService = imageseo_get_service('ReportImage');
        $this->optionServices = imageseo_get_service('Option');
        $this->tagsToStringServices = imageseo_get_service('TagsToString');
        $this->processQueryImagesNoAlt = new QueryImagesNoAltBackgroundProcess();
    }

    /**
     * @param int $attachmentId
     */
    public function generateForAttachmentId($attachmentId, $query = [])
    {
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

        $this->updateAlt($attachmentId, $alt);
    }

    /**
     * @param int    $attachmentId
     * @param string $alt
     */
    public function updateAlt($attachmentId, $alt, $options = ['updateCounter' => true])
    {
        if (!isset($options['updateCounter'])) {
            $options['updateCounter'] = true;
        }

        update_post_meta($attachmentId, '_wp_attachment_image_alt', apply_filters('imageseo_update_alt', $alt, $attachmentId));

        if ($options['updateCounter']) {
            $this->processQueryImagesNoAlt->push_to_queue([
                'query_images_no_alt' => true,
            ]);
            $this->processQueryImagesNoAlt->save()->dispatch();
        }
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
