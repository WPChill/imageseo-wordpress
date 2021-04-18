<?php

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

add_action('action_bulk_image_process_action_scheduler', 'bulk_image_process_action_scheduler', 10, 2);

function bulk_image_process_action_scheduler()
{
    $optionBulkProcess = get_option('_imageseo_bulk_process_settings');
    if (!$optionBulkProcess) {
        return false;
    }

    $sizeImages = 5;
    if (isset($optionBulkProcess['size_indexes_image'])) {
        $sizeImages = $optionBulkProcess['size_indexes_image'];
    }

    // exclude the names of files in use during bulk
    $excludeFilenames = get_option('_imageseo_bulk_exclude_filenames');
    if (!$excludeFilenames) {
        $excludeFilenames = [];
    }
    global $wpdb;

    for ($i = 0; $i < $sizeImages; ++$i) {
        $limitExcedeed = imageseo_get_service('UserInfo')->hasLimitExcedeed();
        if ($limitExcedeed) {
            continue;
        }

        if (!isset($optionBulkProcess['id_images'][$i])) {
            continue;
        }

        $pauseBulkProcess = $wpdb->get_results("SELECT option_id FROM {$wpdb->prefix}options WHERE option_name = '_imageseo_pause_bulk_process'");
        if (!empty($pauseBulkProcess)) {
            continue;
        }

        $attachmentId = array_shift($optionBulkProcess['id_images']);
        error_log('[attachment id] : ' . $attachmentId);
        try {
            $response = imageseo_get_service('ReportImage')->generateReportByAttachmentId($attachmentId, ['force' => true], $optionBulkProcess['settings']['language']);
        } catch (\Exception $e) {
            update_post_meta($attachmentId, '_imageseo_bulk_report', [
                'success' => false,
            ]);

            continue;
        }

        $alt = '';
        $filename = '';
        $extension = '';
        $oldAlt = imageseo_get_service('Alt')->getAlt($attachmentId);
        $metadata = wp_get_attachment_metadata($attachmentId);
        $oldFilename = '';
        if (isset($metadata['original_image'])) {
            $oldFilename = $metadata['original_image'];
        } else {
            $fileRootDirectories = explode('/', $metadata['file']);
            $oldFilename = $fileRootDirectories[count($fileRootDirectories) - 1];
        }

        // Optimize Alt
        if ($optionBulkProcess['settings']['optimizeAlt']) {
            $format = 'CUSTOM_FORMAT' === $optionBulkProcess['settings']['formatAlt'] ? $optionBulkProcess['settings']['formatAltCustom'] : $optionBulkProcess['settings']['formatAlt'];

            $alt = imageseo_get_service('TagsToString')->replace($format, $attachmentId);

            imageseo_get_service('Alt')->updateAlt($attachmentId, $alt);
        }

        // Optimize file
        if ($optionBulkProcess['settings']['optimizeFile']) {
            $renameFileService = imageseo_get_service('GenerateFilename');

            list($filename, $extension) = $renameFileService->generateFilenameForAttachmentId($attachmentId, $excludeFilenames);
            error_log('new filename : ' . $filename);
            error_log('new extension : ' . $extension);
            $excludeFilenames[] = $filename;

            if (!empty($filename)) {
                try {
                    imageseo_get_service('UpdateFile')->updateFilename($attachmentId, sprintf('%s.%s', $filename, $extension));
                } catch (\Exception $e) {
                    error_log($e->getMessage());
                }
            }
        }

        $optionBulkProcess['id_images_optimized'][] = $attachmentId;

        update_post_meta($attachmentId, '_imageseo_bulk_report', [
            'success'      => true,
            'old_alt'      => $oldAlt,
            'old_filename' => $oldFilename,
            'filename'     => $filename,
            'extension'    => $extension,
            'alt'          => $alt,
        ]);

        $optionBulkProcess['id_images'] = $optionBulkProcess['id_images'];
        update_option('_imageseo_bulk_process_settings', $optionBulkProcess);
    }

    $optionBulkProcess['id_images'] = $optionBulkProcess['id_images'];

    update_option('_imageseo_bulk_process_settings', $optionBulkProcess);
    update_option('_imageseo_bulk_exclude_filenames', $excludeFilenames);

    $pauseBulkProcess = $wpdb->get_results("SELECT option_id FROM {$wpdb->prefix}options WHERE option_name = '_imageseo_pause_bulk_process'");
    if (!empty($pauseBulkProcess)) {
        return false;
    }

    // Next batch
    if (count($optionBulkProcess['id_images']) > 0) {
        $limitExcedeed = imageseo_get_service('UserInfo')->hasLimitExcedeed();
        if ($limitExcedeed) {
            as_unschedule_all_actions('action_bulk_image_process_action_scheduler', [], 'group_bulk_image');
            delete_option('_imageseo_bulk_process_settings');
            update_option('_imageseo_pause_bulk_process', $optionBulkProcess);

            return false;
        }
        as_schedule_single_action(time() + 60, 'action_bulk_image_process_action_scheduler', [], 'group_bulk_image');
    }
    // Finish
    else {
        update_option('_imageseo_finish_bulk_process', $optionBulkProcess);
        delete_option('_imageseo_last_process_settings');
        delete_option('_imageseo_bulk_process_settings');
    }
}
