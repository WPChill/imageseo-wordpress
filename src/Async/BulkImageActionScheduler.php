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

function bulk_image_process_action_scheduler()
{

	$optionBulkProcess = get_option('_imageseo_bulk_process_settings');

	$limitExcedeed = imageseo_get_service('UserInfo')->hasLimitExcedeed();
	if ($limitExcedeed) {
		return false;
	}

	$sizeImages = 5;
	if(isset($optionBulkProcess['size_indexes_image'])){
		$sizeImages = $optionBulkProcess['size_indexes_image'];
	}

	// exclude the names of files in use during bulk
	$excludeFilenames = get_option('_imageseo_bulk_exclude_filenames');
	if (!$excludeFilenames) {
		$excludeFilenames = [];
	}



	for ($i=0; $i < $sizeImages ; $i++) {
		@set_time_limit(0);

		if(!isset($optionBulkProcess['id_images'][$i])){
			continue;
		}

		$attachmentId = array_shift($optionBulkProcess['id_images']);
		error_log("Attachment : " . $attachmentId);
		try {
			$response = imageseo_get_service('ReportImage')->generateReportByAttachmentId($attachmentId, ['force' => true], $optionBulkProcess['settings']['language']);
		} catch (\Exception $e) {
			error_log($e->getMessage());
			update_post_meta($attachmentId, '_imageseo_bulk_report', [
				'success' => false,
			]);

			continue;
		}

		$alt = '';
		$filename = '';
		$extension = '';

		// Optimize Alt
		if ($optionBulkProcess['settings']['optimizeAlt']) {
			$format = 'CUSTOM_FORMAT' === $optionBulkProcess['settings']['formatAlt'] ? $optionBulkProcess['settings']['formatAltCustom'] : $optionBulkProcess['settings']['formatAlt'];

			$alt = imageseo_get_service('TagsToString')->replace($format, $attachmentId);

			imageseo_get_service('Alt')->updateAlt($attachmentId, $alt);
		}

		// Optimize file
		if ($optionBulkProcess['settings']['optimizeFile']) {
			$renameFileService = imageseo_get_service('RenameFile');

			list($filename, $extension) = bulk_image_get_filename_for_preview($attachmentId, $excludeFilenames);

			$excludeFilenames[] = $filename;

			if (empty($filename)) {
				$renameFileService->removeFilename($attachmentId);
			} else {
				try {
					$extension = $renameFileService->getExtensionFilenameByAttachmentId($attachmentId);
					$filename = $renameFileService->validateUniqueFilename($attachmentId, $filename);

					$renameFileService->updateFilename($attachmentId, sprintf('%s.%s', $filename, $extension));
				} catch (\Exception $e) {
					error_log($e->getMessage());
				}
			}
		}

		$optionBulkProcess['id_images_optimized'][] = $attachmentId;


		update_post_meta($attachmentId, '_imageseo_bulk_report', [
			'success'   => true,
			'filename'  => $filename,
			'extension' => $extension,
			'alt'       => $alt,
		]);

	}

	$optionBulkProcess['id_images'] = $optionBulkProcess['id_images'];

	update_option('_imageseo_bulk_exclude_filenames', $excludeFilenames);
	update_option('_imageseo_bulk_process_settings', $optionBulkProcess);

	// Next batch
	if(count($optionBulkProcess['id_images']) > 0){
		as_schedule_single_action( time() + 60, 'action_bulk_image_process_action_scheduler', [], "group_bulk_image");
	}
	// Finish
	else {
		update_option('_imageseo_last_bulk_process', $optionBulkProcess);
		delete_option('_imageseo_bulk_process_settings');
	}

}