<?php

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

use ImageSeoWP\Helpers\AltFormat;

add_action('action_worker_on_upload_process_action_scheduler', 'imageSeoProcessOnUpload', 10, 1);

function imageseo_get_filename_on_upload($attachmentId, $excludeFilenames = [])
{
    try {
        $filename = imageseo_get_service('GenerateFilename')->getNameFileWithAttachmentId($attachmentId, $excludeFilenames);
    } catch (NoRenameFile $e) {
        $filename = imageseo_get_service('GenerateFilename')->getFilenameByAttachmentId($attachmentId);
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

function imageSeoProcessOnUpload($attachmentId)
{
    error_log('here : ' . $attachmentId);

    $limitExcedeed = imageseo_get_service('UserInfo')->hasLimitExcedeed();
    if ($limitExcedeed) {
        return;
    }

    $activeAltOnUpload = imageseo_get_service('Option')->getOption('active_alt_write_upload');
    $activeRenameOnUpload = imageseo_get_service('Option')->getOption('active_rename_write_upload');

    error_log('[attachment id] : ' . $attachmentId);
    try {
        $response = imageseo_get_service('ReportImage')->generateReportByAttachmentId($attachmentId, ['force' => true]);
    } catch (\Exception $e) {
        error_log($e->getMessage());
        update_post_meta($attachmentId, '_imageseo_bulk_report', [
            'success' => false,
        ]);
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
    if ($activeAltOnUpload) {
        $format = 'CUSTOM_FORMAT' === $optionBulkProcess['settings']['formatAlt'] ? $optionBulkProcess['settings']['formatAltCustom'] : $optionBulkProcess['settings']['formatAlt'];

        $format = apply_filters('imageseo_format_alt_on_upload', AltFormat::ALT_SIMPLE);
        $alt = imageseo_get_service('TagsToString')->replace($format, $attachmentId);

        imageseo_get_service('Alt')->updateAlt($attachmentId, $alt);
    }

    // Optimize file
    if ($activeRenameOnUpload) {
        $renameFileService = imageseo_get_service('GenerateFilename');

        list($filename, $extension) = imageseo_get_filename_on_upload($attachmentId, $excludeFilenames);

        if (!empty($filename)) {
            try {
                $extension = $renameFileService->getExtensionFilenameByAttachmentId($attachmentId);
                $filename = $renameFileService->validateUniqueFilename($attachmentId, $filename);

                imageseo_get_service('UpdateFile')->updateFilename($attachmentId, sprintf('%s.%s', $filename, $extension));
            } catch (\Exception $e) {
                error_log($e->getMessage());
            }
        }
    }

    update_post_meta($attachmentId, '_imageseo_bulk_report', [
        'success'      => true,
        'old_alt'      => $oldAlt,
        'old_filename' => $oldFilename,
        'filename'     => $filename,
        'extension'    => $extension,
        'alt'          => $alt,
    ]);
}
