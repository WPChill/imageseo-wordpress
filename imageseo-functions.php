<?php

if (! defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Context;

/**
 * Get a service
 * @since 1.0.0
 *
 * @param string $service
 * @return object
 */
function imageseo_get_service($service)
{
    return Context::getContext()->getService($service);
}

/**
 * Get all options
 * @since 1.0.0
 *
 * @return array
 */
function imageseo_get_options()
{
    return Context::getContext()->getService('Option')->getOptions();
}

/**
 * Get option
 * @since 1.0.0
 * @param string $key
 * @return any
 */
function imageseo_get_option($key)
{
    return Context::getContext()->getService('Option')->getOption($key);
}

/**
 * @return bool
 */
function imageseo_allowed()
{
    return imageseo_get_option('allowed');
}


function imageseo_generate_alt_attachment_id($attachmentId)
{
    $reportImageService = imageseo_get_service('ReportImage');
    $response = $reportImageServices->generateReportByAttachmentId($attachmentId);
    if (!$response['success']) {
        return;
    }

    $reportImageServices->updateAltAttachmentWithReport($attachmentId, $response['result']);
}

function imageseo_rename_file_attachment_id($attachmentId, $metadata = null)
{
    return imageseo_get_service('RenameFile')->renameAttachment($attachmentId);
}
