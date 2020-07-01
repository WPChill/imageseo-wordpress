<?php

namespace ImageSeoWP\Async;

defined('ABSPATH') or die('Cheatin&#8217; uh?');

use ImageSeoWP\Exception\NoRenameFile;

class BulkImageBackgroundProcess extends WPBackgroundProcess
{
    protected $action = 'imageseo_bulk_image_background_process';

    protected function getFilenameForPreview($attachmentId, $excludeFilenames = [])
    {
        try {
            $filename = imageseo_get_service('RenameFile')->getNameFileWithAttachmentId($attachmentId, $excludeFilenames);
        } catch (NoRenameFile $e) {
            $filename = imageseo_get_service('RenameFile')->getFilenameByAttachmentId($attachmentId);
        }

        $splitFilename = explode('.', $filename);
        if (1 === count($splitFilename)) { // Need to retrieve current extension
            $currentFilename = wp_get_attachment_image_src($attachmentId, 'full');
            $splitCurrentFilename = explode('.', $currentFilename[0]);
            $extension = $splitCurrentFilename[count($splitCurrentFilename) - 1];
        } else {
            $extension = $splitFilename[count($splitFilename) - 1];
            array_pop($splitFilename);
            $filename = implode('.', $splitFilename);
        }

        return [
            $filename,
            $extension,
        ];
    }

    /**
     * Task.
     *
     * @param mixed $item Queue item to iterate over
     *
     * @return mixed
     */
    protected function task($item)
    {
        $optionBulkProcess = get_option('_imageseo_bulk_process');

        global $wpdb;
        $needToStopProcess = $wpdb->get_row($wpdb->prepare("SELECT option_value FROM $wpdb->options WHERE option_name = %s LIMIT 1", '_imageseo_need_to_stop_process'));

        if ($needToStopProcess) {
            return false;
        }

        $limitExcedeed = imageseo_get_service('UserInfo')->hasLimitExcedeed();
        if ($limitExcedeed) {
            return false;
        }

        try {
            $response = imageseo_get_service('ReportImage')->generateReportByAttachmentId($item, ['force' => true], $optionBulkProcess['settings']['language']);
        } catch (\Exception $e) {
            update_post_meta($item, '_imageseo_bulk_report', [
                'success' => false,
            ]);

            return false;
        }

        $alt = '';
        $filename = '';
        $extension = '';

        if ($optionBulkProcess['settings']['optimizeAlt']) {
            $format = 'CUSTOM_FORMAT' === $optionBulkProcess['settings']['formatAlt'] ? $optionBulkProcess['settings']['formatAltCustom'] : $optionBulkProcess['settings']['formatAlt'];

            $alt = imageseo_get_service('TagsToString')->replace($format, $item);

            if (!$optionBulkProcess['settings']['wantValidateResult']) {
                imageseo_get_service('Alt')->updateAlt($item, $alt);
            }
        }

        if ($optionBulkProcess['settings']['optimizeFile']) {
            $renameFileService = imageseo_get_service('RenameFile');
            $excludeFilenames = get_option('_imageseo_bulk_exclude_filenames');
            if (!$excludeFilenames) {
                $excludeFilenames = [];
            }

            list($filename, $extension) = $this->getFilenameForPreview($item, $excludeFilenames);
            $excludeFilenames[] = $filename;
            update_option('_imageseo_bulk_exclude_filenames', $excludeFilenames);

            if (!$optionBulkProcess['settings']['wantValidateResult']) {
                if (empty($filename)) {
                    $renameFileService->removeFilename($item);
                } else {
                    try {
                        $extension = $renameFileService->getExtensionFilenameByAttachmentId($item);
                        $filename = $renameFileService->validateUniqueFilename($item, $filename);

                        $renameFileService->updateFilename($item, sprintf('%s.%s', $filename, $extension));
                    } catch (\Exception $e) {
                    }
                }
            }
        }

        ++$optionBulkProcess['current_index_image'];
        $optionBulkProcess['id_images_optimized'][] = $item;
        update_option('_imageseo_bulk_process', $optionBulkProcess);

        update_post_meta($item, '_imageseo_bulk_report', [
            'success'   => true,
            'filename'  => $filename,
            'extension' => $extension,
            'alt'       => $alt,
        ]);

        return false;
    }

    protected function complete()
    {
        parent::complete();

        $optionBulkProcess = get_option('_imageseo_bulk_process');

        if ($optionBulkProcess['current_index_image'] + 1 == $optionBulkProcess['total_images']) {
            delete_option('_imageseo_last_bulk_process');
        }

        global $wpdb;

        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM $wpdb->postmeta WHERE meta_key = %s",
                '_imageseo_bulk_report'
            )
        );

        delete_option('_imageseo_bulk_exclude_filenames');
        delete_option('_imageseo_need_to_stop_process');
        delete_option('_imageseo_bulk_process');
    }
}
