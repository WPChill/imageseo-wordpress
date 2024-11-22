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
		if (
			!empty($imageMeta)
			&& isset($imageMeta['timestamp'])
			&& (time() - $imageMeta['timestamp']) < 86400
		) {
			$this->_updateAlt($imageMeta);
			$this->setOptimizationFlag($attachmentId, 'altText');

			return $imageMeta;
		}

		if (imageseo_get_service('UserInfo')->hasLimitExcedeed()) {
			return ['error' => 'limit exceeded'];
		}

		$image = $this->_requestOptimization($attachmentId);

		if ($image === null || $image['failed']) {
			return ['error' => 'error', 'message' => isset($image['failureDetails']) ? $image['failureDetails'] : ''];
		}

		$this->_updateAlt($image);
		$this->setOptimizationFlag($attachmentId, 'altText');

		if (isset($imageMeta['forced_altText'])) {
			$imageMeta['forced_altText'] = false;
		}

		update_post_meta($attachmentId, AttachmentMeta::REPORT, $image);

		return $image;
	}

	public function getAndUpdateFilename($attachmentId)
	{
		$imageMeta = get_post_meta($attachmentId, AttachmentMeta::REPORT, true);
		// 86400 = 1 day
		if (
			!empty($imageMeta)
			&& isset($imageMeta['timestamp'])
			&& (time() - $imageMeta['timestamp']) < 86400
		) {
			$filename = $this->_updateFilename($imageMeta['internalId'], $imageMeta);
			$this->setOptimizationFlag($attachmentId, 'filename');

			return array_merge($imageMeta, ['filename' => $filename]);
		}

		if (imageseo_get_service('UserInfo')->hasLimitExcedeed()) {
			return ['error' => 'limit exceeded'];
		}

		$image = $this->_requestOptimization($attachmentId);

		if ($image === null || $image['failed']) {
			return ['error' => 'error', 'message' => $image['failureDetails'] || ''];
		}

		$filename = $this->_updateFilename($image['internalId'], $image);
		$this->setOptimizationFlag($attachmentId, 'filename');

		if (isset($imageMeta['forced_filename'])) {
			$imageMeta['forced_filename'] = false;
		}

		update_post_meta($attachmentId, AttachmentMeta::REPORT, $image);

		return array_merge($image, ['filename' => $filename]);
	}

	private function setOptimizationFlag($attachmentId, $type)
	{
		$meta = get_post_meta($attachmentId, AttachmentMeta::REPORT, true);
		if (empty($meta)) {
			$meta = [];
		}
		if (!isset($meta['optimizedParts'])) {
			$meta['optimizedParts'] = [];
		}

		if (is_array($type)) {
			foreach ($type as $t) {
				$meta['optimizedParts'][$t] = true;
			}
		} else {
			$meta['optimizedParts'][$type] = true;
		}

		$meta['fullyOptimized'] = isset($meta['optimizedParts']['altText']) && isset($meta['optimizedParts']['filename']);

		update_post_meta($attachmentId, AttachmentMeta::REPORT, $meta);
	}

	public function getAndSetOptimizedImage($attachmentId)
	{
		$imageMeta = get_post_meta($attachmentId, AttachmentMeta::REPORT, true);
		// 86400 = 1 day
		if (
			!empty($imageMeta)
			&& isset($imageMeta['timestamp'])
			&& (time() - $imageMeta['timestamp']) < 86400
		) {
			$this->setOptimizationFlag($attachmentId, ['altText', 'filename']);
			return $imageMeta;
		}

		if (imageseo_get_service('UserInfo')->hasLimitExcedeed()) {
			return ['error' => 'limit exceeded'];
		}

		$image = $this->_requestOptimization($attachmentId);

		if ($image === null || $image['failed']) {
			return ['error' => 'error', 'message' => isset($image['failureDetails']) ? $image['failureDetails'] : ''];
		}

		$this->_updateAlt($image);
		$filename = $this->_updateFilename($attachmentId, $image);
		$this->setOptimizationFlag($attachmentId, ['altText', 'filename']);
		if (isset($imageMeta['forced'])) {
			$imageMeta['forced'] = false;
		}

		update_post_meta($attachmentId, AttachmentMeta::REPORT, $image);

		return array_merge($image, ['filename' => $filename]);
	}

	private function _requestOptimization($attachmentId)
	{
		$extension = $this->generateFilename->getExtensionFilenameByAttachmentId($attachmentId);
		if (!in_array($extension, ['png', 'jpg', 'jpeg', 'webp'])) {
			return;
		}

		$images = [
			$this->createApiImage($attachmentId)
		];

		$response = $this->sendRequestToApi($images, true);

		if ($response instanceof Exception) {
			error_log($response->getMessage());

			return;
		}

		if (!isset($response['batchId'])) {
			return;
		}

		if ($response['failed']) {
			error_log($response['failureDetails']);
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
		if (empty($image['filename'])) {
			return '';
		}

		$extension = $this->generateFilename->getExtensionFilenameByAttachmentId($attachmentId);
		$filename = $image['filename'];

		if (strpos($filename, '.') !== false) {
			$filename = pathinfo($filename, PATHINFO_FILENAME);
		}

		$this->fileService->updateFilename(
			$image['internalId'],
			sprintf('%s.%s', $filename, $extension)
		);

		return sprintf('%s.%s', $filename, $extension);
	}

	public function getAndUpdateMeta($attachmentId, $prop, $val)
	{
		$meta = get_post_meta($attachmentId, AttachmentMeta::REPORT, true);
		if (empty($meta)) {
			$forcedProp = 'forced_' . $prop;
			$meta = [
				$forcedProp => true,
			];
		}
		$meta[$prop] = $val;
		update_post_meta($attachmentId, AttachmentMeta::REPORT, $meta);
	}
}
