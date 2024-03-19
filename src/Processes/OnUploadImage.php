<?php

namespace ImageSeoWP\Processes;

use ImageSeoWP\Async\WPAsyncRequest;
use ImageSeoWP\Helpers\AttachmentMeta;
use ImageSeoWP\Traits\ApiHandler;

if (!defined('ABSPATH')) {
	exit;
}

class OnUploadImage extends WPAsyncRequest
{
	use ApiHandler;
	protected $prefix = 'imageseo';
	protected $action = 'on_upload_image';

	public static $instance;
	public static function getInstance()
	{
		if (!isset(self::$instance) && !(self::$instance instanceof OnUploadImage)) {
			self::$instance = new OnUploadImage();
		}

		return self::$instance;
	}

	protected function handle()
	{
		$this->setServices();

		$attachmentId = $_POST['attachment_id'];
		$fillAlt = $_POST['active_alt_on_upload'];
		$renameFile = $_POST['active_rename_on_upload'];

		if (empty($attachmentId)) {
			return;
		}

		if (empty($fillAlt) && empty($renameFile)) {
			return;
		}

		$images =  [
			$this->createApiImage($attachmentId)
		];

		$response = $this->sendRequestToApi($images);

		if (is_wp_error($response)) {
			error_log($response->get_error_message());
			return;
		}

		$batchId = $response['batchId'];
		$items = $this->getItemsByBatchId($batchId);
		if (is_wp_error($items)) {
			error_log($items->get_error_message());
			return;
		}

		$image = $items[0];
		$attachmentId = $image['internalId'];

		if ($fillAlt) {
			$this->altService->updateAlt($image['internalId'], $image['altText']);
		}

		if ($renameFile) {
			$extension = $this->generateFilename->getExtensionFilenameByAttachmentId($attachmentId);
			$this->fileService->updateFilename(
				$image['internalId'],
				sprintf('%s.%s', $image['filename'], $extension)
			);
		}

		update_post_meta($attachmentId, AttachmentMeta::REPORT, $image);
	}
}
