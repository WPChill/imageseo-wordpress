<?php


defined('ABSPATH') or die('Cheatin&#8217; uh?');

use ImageSeoWP\Exception\NoRenameFile;

function bulk_image_get_filename_for_preview($attachmentId, $excludeFilenames = [])
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


add_action( 'action_bulk_image_process_action_scheduler', 'bulk_image_process_action_scheduler', 10, 2 );

function bulk_image_process_action_scheduler($index)
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

	if(!isset($optionBulkProcess['id_images'][$index])){
		return false;
	}

	$attachmentId = $optionBulkProcess['id_images'][$index];

	try {
		$response = imageseo_get_service('ReportImage')->generateReportByAttachmentId($attachmentId, ['force' => true], $optionBulkProcess['settings']['language']);
	} catch (\Exception $e) {
		update_post_meta($attachmentId, '_imageseo_bulk_report', [
			'success' => false,
		]);

		return false;
	}

	$alt = '';
	$filename = '';
	$extension = '';

	if ($optionBulkProcess['settings']['optimizeAlt']) {
		$format = 'CUSTOM_FORMAT' === $optionBulkProcess['settings']['formatAlt'] ? $optionBulkProcess['settings']['formatAltCustom'] : $optionBulkProcess['settings']['formatAlt'];

		$alt = imageseo_get_service('TagsToString')->replace($format, $attachmentId);

		if (!$optionBulkProcess['settings']['wantValidateResult']) {
			imageseo_get_service('Alt')->updateAlt($attachmentId, $alt);
		}
	}

	if ($optionBulkProcess['settings']['optimizeFile']) {
		$renameFileService = imageseo_get_service('RenameFile');
		$excludeFilenames = get_option('_imageseo_bulk_exclude_filenames');
		if (!$excludeFilenames) {
			$excludeFilenames = [];
		}

		list($filename, $extension) = bulk_image_get_filename_for_preview($attachmentId, $excludeFilenames);
		$excludeFilenames[] = $filename;
		update_option('_imageseo_bulk_exclude_filenames', $excludeFilenames);

		if (!$optionBulkProcess['settings']['wantValidateResult']) {
			if (empty($filename)) {
				$renameFileService->removeFilename($attachmentId);
			} else {
				try {
					$extension = $renameFileService->getExtensionFilenameByAttachmentId($attachmentId);
					$filename = $renameFileService->validateUniqueFilename($attachmentId, $filename);

					$renameFileService->updateFilename($attachmentId, sprintf('%s.%s', $filename, $extension));
				} catch (\Exception $e) {
				}
			}
		}
	}

	++$optionBulkProcess['current_index_image'];
	$optionBulkProcess['id_images_optimized'][] = $attachmentId;
	update_option('_imageseo_bulk_process', $optionBulkProcess);

	update_post_meta($attachmentId, '_imageseo_bulk_report', [
		'success'   => true,
		'filename'  => $filename,
		'extension' => $extension,
		'alt'       => $alt,
	]);


	if($optionBulkProcess['current_index_image'] < $optionBulkProcess['total_images']){
		as_schedule_single_action( time(), 'action_bulk_image_process_action_scheduler', ["current_index_image" => $optionBulkProcess['current_index_image']], "group_bulk_image");
	}

	if($optionBulkProcess['current_index_image'] + 1 > $optionBulkProcess['total_images']){
		$optionBulkProcess = get_option('_imageseo_bulk_process');

		if ($optionBulkProcess['current_index_image'] + 1 == $optionBulkProcess['total_images']) {
			delete_option('_imageseo_last_bulk_process');
		}

		update_option('_imageseo_bulk_is_finish', true);
		delete_option('_imageseo_bulk_exclude_filenames');
		delete_option('_imageseo_need_to_stop_process');
	}
}
