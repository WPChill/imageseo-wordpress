<?php

namespace ImageSeoWP\Async;

defined('ABSPATH') or die('Cheatin&#8217; uh?');

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
        error_log(serialize($item));
        $optionBulkProcess = get_option('_imageseo_bulk_process');
        if (3 === $optionBulkProcess['current_index_image']) {
            return false;
        }

        try {
            $response = imageseo_get_service('ReportImage')->generateReportByAttachmentId($item['id'], ['force' => true], $item['settings']['language']);
        } catch (\Exception $e) {
            update_post_meta($item['id'], '_imageseo_bulk_report', [
                'success' => false,
            ]);

            return false;
        }

        $alt = '';
        $filename = '';
        $extension = '';
        error_log($item['settings']['formatAlt']);
        if ($item['settings']['optimizeAlt']) {
            $alt = imageseo_get_service('TagsToString')->replace($item['settings']['formatAlt'], $item['id']);
            if (!$item['settings']['wantValidateResult']) {
                imageseo_get_service('Alt')->updateAlt($item['id'], $alt);
            }
        }

        if ($item['settings']['optimizeFile']) {
            $renameFileService = imageseo_get_service('RenameFile');
            $excludeFilenames = get_option('_imageseo_bulk_exclude_filenames');
            if (!$excludeFilenames) {
                $excludeFilenames = [];
            }

            list($filename, $extension) = $this->getFilenameForPreview($item['id'], $excludeFilenames);
            $excludeFilenames[] = $filename;
            update_option('_imageseo_bulk_exclude_filenames', $excludeFilenames);
            error_log('Filename : ' . $filename);
            error_log('extension : ' . $extension);
            if (!$item['settings']['wantValidateResult']) {
                if (empty($filename)) {
                    $renameFileService->removeFilename($item['id']);
                } else {
                    try {
                        $extension = $renameFileService->getExtensionFilenameByAttachmentId($item['id']);
                        $filename = $renameFileService->validateUniqueFilename($item['id'], $filename);

                        $renameFileService->updateFilename($item['id'], sprintf('%s.%s', $filename, $extension));
                    } catch (\Exception $e) {
                    }
                }
            }
        }

        ++$optionBulkProcess['current_index_image'];
        update_option('_imageseo_bulk_process', $optionBulkProcess);

        update_post_meta($item['id'], '_imageseo_bulk_report', [
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
            delete_option('_imageseo_bulk_process');
        }

        delete_option('_imageseo_bulk_exclude_filenames');
        update_option('_imageseo_last_bulk_process', [
            'current_index_image' => $optionBulkProcess['current_index_image'],
            'id_images'           => $optionBulkProcess['id_images'],
            'total_images'        => $optionBulkProcess['total_images'],
        ]);
    }
}
