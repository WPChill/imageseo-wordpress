<?php

namespace ImageSeoWP\Services;

use Exception;
use ImageSeoWP\Helpers\AttachmentMeta;
use ImageSeoWP\Traits\ApiHandler;

if (!defined('ABSPATH')) {
	exit;
}

class Optimizer
{
	use ApiHandler;

	public static $instance;

	public static function getInstance()
	{
		if (!isset(self::$instance) && !(self::$instance instanceof Optimizer)) {
			self::$instance = new Optimizer();
		}

		return self::$instance;
	}

	public function __construct()
	{
		$this->setServices();
	}

	public function getAndSetAlt($attachmentId)
	{
		$imageMeta = get_post_meta($attachmentId, AttachmentMeta::REPORT, true);
		// 86400 = 1 day
		if (!empty($imageMeta) && isset($imageMeta['timestamp']) && (time() - $imageMeta['timestamp']) < 86400) {

			$this->_updateAlt($imageMeta);

			return $imageMeta;
		}

		if (imageseo_get_service('UserInfo')->hasLimitExcedeed()) {
			return ['error' => 'limit exceeded'];
		}

		$image = $this->_requestOptimization($attachmentId);

		if ($image === null) {
			return ['error' => 'error'];
		}

		$this->_updateAlt($image);

		update_post_meta($attachmentId, AttachmentMeta::REPORT, $image);

		return $image;
	}

	public function getAndUpdateFilename($attachmentId)
	{
		$imageMeta = get_post_meta($attachmentId, AttachmentMeta::REPORT, true);
		// 86400 = 1 day
		if (!empty($imageMeta) && isset($imageMeta['timestamp']) && (time() - $imageMeta['timestamp']) < 86400) {

			$filename = $this->_updateFilename($imageMeta['internalId'], $imageMeta);

			return array_merge($imageMeta, ['filename' => $filename]);
		}

		if (imageseo_get_service('UserInfo')->hasLimitExcedeed()) {
			return ['error' => 'limit exceeded'];
		}

		$image = $this->_requestOptimization($attachmentId);

		if ($image === null) {
			return ['error' => 'error'];
		}

		$filename = $this->_updateFilename($image['internalId'], $image);
		update_post_meta($attachmentId, AttachmentMeta::REPORT, $image);

		return array_merge($image, ['filename' => $filename]);
	}

	public function getAndSetOptimizedImage($attachmentId)
	{
		$imageMeta = get_post_meta($attachmentId, AttachmentMeta::REPORT, true);
		// 86400 = 1 day
		if (!empty($imageMeta) && isset($imageMeta['timestamp']) && (time() - $imageMeta['timestamp']) < 86400) {
			return $imageMeta;
		}

		if (imageseo_get_service('UserInfo')->hasLimitExcedeed()) {
			return ['error' => 'limit exceeded'];
		}

		$image = $this->_requestOptimization($attachmentId);

		if ($image === null) {
			return ['error' => 'error'];
		}

		$this->_updateAlt($image);
		$filename = $this->_updateFilename($attachmentId, $image);

		update_post_meta($attachmentId, AttachmentMeta::REPORT, $image);

		return array_merge($image, ['filename' => $filename]);
	}

	private function _requestOptimization($attachmentId)
	{
		$images = [
			$this->createApiImage($attachmentId)
		];

		$response = $this->sendRequestToApi($images, true);

		if ($response instanceof Exception) {
			error_log($response->getMessage());

			return;
		}

		$batchId = $response['batchId'];
		$items   = $this->getItemsByBatchId($batchId);
		if ($items instanceof Exception) {
			error_log($items->getMessage());

			return;
		}

		$image              = $items[0];
		$image['timestamp'] = time();

		return $image;
	}

	public function _updateAlt($image)
	{
		$this->altService->updateAlt($image['internalId'], $image['altText']);
	}

	public function _updateFilename($attachmentId, $image): string
	{
		$extension = $this->generateFilename->getExtensionFilenameByAttachmentId($attachmentId);
		$this->fileService->updateFilename(
			$image['internalId'],
			sprintf('%s.%s', $image['filename'], $extension)
		);

		return sprintf('%s.%s', $image['filename'], $extension);
	}

	public function getAndUpdateMeta($attachmentId, $prop, $val)
	{
		$meta = get_post_meta($attachmentId, AttachmentMeta::REPORT, true);
		if (empty($meta)) {
			$meta = [];
		}
		$meta[$prop] = $val;
		update_post_meta($attachmentId, AttachmentMeta::REPORT, $meta);
	}
}
