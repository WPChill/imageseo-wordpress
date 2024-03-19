<?php

namespace ImageSeoWP\Services;

use ImageSeoWP\Helpers\AttachmentMeta;
use ImageSeoWP\Traits\ApiHandler;

if (!defined('ABSPATH')) {
	exit;
}

class BulkOptimizer
{
	use ApiHandler;
	public $batchSize = 10;
	public $defaultLastReport = [
		'total' => 0,
		'optimized' => 0,
		'failed' => 0,
		'skipped' => 0,
		'errors' => []
	];

	public static $instance;

	public static function getInstance()
	{
		if (!isset(self::$instance) && !(self::$instance instanceof BulkOptimizer)) {
			self::$instance = new BulkOptimizer();
		}

		return self::$instance;
	}

	public function __construct()
	{
		$this->setServices();

		add_action('process_image_batch', [$this, 'processImageBatch'], 10, 2);
		add_action('check_image_batch', [$this, 'checkImageBatch'], 10, 1);
	}

	public function getStatus()
	{
		$image_data = get_option('imageseo_bulk_image_data', false);
		$report = get_option('imageseo_bulk_optimizer_last_report', $this->defaultLastReport);

		if ($image_data === false) {
			return [
				'status' => 'idle',
				'report' => $report
			];
		} else {
			$report['total'] = count($image_data['ids']);
			$report['optimized'] = count($image_data['optimizedIds']);
			$report['failed'] = count($image_data['failedIds'] ?? []);
			$report['remaining'] = $report['total'] - $report['optimized'] - $report['failed'];
			$report['skipped'] = $report['failed'];

			return [
				'status' => get_option('imageseo_bulk_optimizer_status', 'idle'),
				'report' => $report
			];
		}


		return [
			'status' => get_option('imageseo_bulk_optimizer_status', 'idle'),
			'report' => get_option('imageseo_bulk_optimizer_last_report', $this->defaultLastReport)
		];
	}

	public function start()
	{
		$images = $this->bulkOptimizerQuery->getImages();
		$options = imageseo_get_options();

		$image_data = [
			'ids' => $images['ids'],
			// This is response from api
			'optimizedIds' => [],
			'failedIds' => [],
			'batchIds' => [],
			'library' => $options['altFilter']
		];

		$report = [
			'total' 	=> count($images['ids']),
			'optimized' => 0,
			'remaining' => count($images['ids']),
			'failed' 	=> 0,
			'skipped' 	=> 0,
			'errors' 	=> []
		];

		update_option('imageseo_bulk_image_data', $image_data);
		update_option('imageseo_bulk_optimizer_status', 'running');
		update_option('imageseo_bulk_optimizer_last_report', $report);

		as_schedule_single_action(
			time(),
			'process_image_batch',
			['batch_number' => 0, 'timestamp' => current_time('timestamp')]
		);

		return [
			'status' => 'running',
			'report' => $report
		];
	}

	public function stop()
	{
		update_option('imageseo_bulk_optimizer_status', 'idle');
		delete_option('imageseo_bulk_image_data');
		return [
			'status' => 'idle',
			'report' => get_option('imageseo_bulk_optimizer_last_report', $this->defaultLastReport)
		];
	}

	public function processImageBatch($batch_number, $timestamp)
	{
		$image_data = get_option('imageseo_bulk_image_data');
		$isNextGen = $image_data['library'] === 'NEXTGEN_GALLERY';

		$totalImages = count($image_data['ids']);
		$startIndex = $batch_number * $this->batchSize;
		$endIndex = min($startIndex + $this->batchSize, $totalImages) - 1;

		$currentBatchIds = array_slice($image_data['ids'], $startIndex, $this->batchSize);

		$images = [];
		foreach ($currentBatchIds as $id) {
			$images[] = $this->createApiImage($id, $isNextGen);
		}

		$result = $this->sendRequestToApi($images);
		if (is_wp_error($result)) {
			$report = get_option('imageseo_bulk_optimizer_last_report', $this->defaultLastReport);
			$report['errors'][] = $result->get_error_message();
			update_option('imageseo_bulk_optimizer_last_report', $report);
			update_option('imageseo_bulk_optimizer_status', 'idle');
			return;
		}

		$image_data['batchIds'][] = $result[0]['batchId'];
		update_option('imageseo_bulk_image_data', $image_data);

		if ($batch_number === 0) {
			as_schedule_single_action(
				time() + 5,
				'check_image_batch',
				['batch_number' => 0]
			);
		}

		if ($endIndex < $totalImages - 1) {
			as_schedule_single_action(
				time() + 10,
				'process_image_batch',
				['batch_number' => $batch_number + 1, 'timestamp' => $timestamp]
			);
		}
	}

	public function checkImageBatch($batch_number)
	{
		$optimizeFilename = $this->optionService->getOption('optimizeFile');
		$isNextGen = $this->optionService->getOption('altFilter') === 'NEXTGEN_GALLERY';
		$image_data = get_option('imageseo_bulk_image_data', false);
		if ($image_data && !isset($image_data['batchIds'][$batch_number])) {
			$totalImages = count($image_data['ids']);
			$totalBatches = ceil($totalImages / $this->batchSize);

			if ($batch_number >= $totalBatches) {
				update_option('imageseo_bulk_optimizer_status', 'idle');
				return;
			}

			as_schedule_single_action(
				time() + 5,
				'check_image_batch',
				['batch_number' => $batch_number]
			);
			return;
		}

		$batchId = $image_data['batchIds'][$batch_number];
		$batchData = $this->getItemsByBatchId($batchId);
		$report = get_option('imageseo_bulk_optimizer_last_report');

		if (is_wp_error($batchData)) {
			$report['errors'][] = $batchData->get_error_message();
			update_option('imageseo_bulk_optimizer_last_report', $report);
			update_option('imageseo_bulk_optimizer_status', 'idle');
			return;
		}

		$allDone = false;
		foreach ($batchData as $image) {
			if ($image['resolved'] === null || $image['failed'] === null) {
				$allDone = false;
				break;
			}

			$allDone = true;
		}

		if (!$allDone) {
			as_schedule_single_action(
				time() + 5,
				'check_image_batch',
				['batch_number' => $batch_number]
			);
			return;
		}

		$report = get_option('imageseo_bulk_optimizer_last_report');
		$optimized = [];
		$failed = [];
		foreach ($batchData as $image) {
			if ($image['resolved']) {
				$attachmentId = $isNextGen
					? imageseo_get_service('QueryNextGen')->getPostIdByNextGenId($image['internalId']) : $image['internalId'];

				update_post_meta($attachmentId, AttachmentMeta::REPORT, $image);

				$isNextGen
					? imageseo_get_service('QueryNextGen')->updateAlt($image['internalId'], $image['altText'])
					: $this->altService->updateAlt($image['internalId'], $image['altText']);

				if ($optimizeFilename) {
					$extension = $this->extractExtension($image['imageUrl']);

					$isNextGen
						? $this->fileService->updateFilenameForNextGen(
							$image['internalId'],
							sprintf('%s.%s', $image['filename'], $extension)
						)
						: $this->fileService->updateFilename(
							$image['internalId'],
							sprintf('%s.%s', $image['filename'], $extension)
						);
				}

				$optimized[] = $image['internalId'];
				continue;
			}

			if ($image['failed']) {
				$report['errors'][] = $image['failureDetails'];
				$failed[] = $image['internalId'];
				continue;
			}
		}

		$image_data = get_option('imageseo_bulk_image_data');
		$image_data['optimizedIds'] = array_merge($image_data['optimizedIds'], $optimized);
		$image_data['failedIds'] = array_merge($image_data['failedIds'], $failed);

		$report['optimized'] += count($optimized);
		$report['failed'] += count($failed);

		update_option('imageseo_bulk_image_data', $image_data);
		update_option('imageseo_bulk_optimizer_last_report', $report);
		$args = ['batch_number' => $batch_number + 1];

		as_schedule_single_action(
			time() + 5,
			'check_image_batch',
			$args
		);
	}

	/**
	 * Extracts the file extension from a given URL.
	 *
	 * @param string $url The URL from which to extract the file extension.
	 * @return string The file extension.
	 */
	public function extractExtension($url)
	{
		return pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
	}
}
