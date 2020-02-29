<?php

namespace ImageSeoWP\Async;

defined('ABSPATH') or die('Cheatin&#8217; uh?');

class RenameFileBackgroundProcess extends WPBackgroundProcess
{
    protected $action = 'imageseo_rename_file_background_process';

    /**
     * Task.
     *
     * @param mixed $item Queue item to iterate over
     *
     * @return mixed
     */
    protected function task($item)
    {
        if (!isset($item['attachment_id'])) {
            return false;
        }

        $attachmentId = $item['attachment_id'];
        $filename = (isset($item['filename'])) ? $item['filename'] : null;
        $metadata = (isset($item['metadata'])) ? $item['metadata'] : null;
        $options = (isset($item['options'])) ? $item['options'] : [];

        if (isset($item['need_generate_report']) && $item['need_generate_report']) {
            $filename = imageseo_get_service('ReportImage')->generateReportByAttachmentId($attachmentId, ['force' => true]);
        }

        if (!$filename || empty($filename)) {
            return false;
        }

        imageseo_get_service('RenameFile')->updateFilename($attachmentId, $filename, $metadata, $options);

        return false;
    }
}
