<?php

if (!defined('ABSPATH')) {
	exit;
}

use ImageSeoWP\Context;

/**
 * Get a service.
 *
 *
 * @param string $service
 *
 * @return object
 */
function imageseo_get_service($service)
{
	return Context::getContext()->getService($service);
}
/**
 * Get an action.
 *
 *
 * @param string $service
 *
 * @return object
 */
function imageseo_get_action($action)
{
	return Context::getContext()->getAction($action);
}

/**
 * Get all options.
 *
 *
 * @return array
 */
function imageseo_get_options()
{
	return Context::getContext()->getService('Option')->getOptions();
}

/**
 * Get option.
 *
 *
 * @param string $key
 *
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
	try {
		$response = $reportImageService->generateReportByAttachmentId($attachmentId);
	} catch (\Exception $e) {
		return;
	}

	if (!$response['success']) {
		return;
	}

	$reportImageService->updateAltAttachmentWithReport($attachmentId);
}

function imageseo_rename_file_attachment_id($attachmentId, $metadata = null)
{
	_deprecated_function(__FUNCTION__, '1.1', 'imageseo_rename_file_attachment_id()');

	return null;
}

function imageseo_get_api_key()
{
	return imageseo_get_option('api_key');
}
