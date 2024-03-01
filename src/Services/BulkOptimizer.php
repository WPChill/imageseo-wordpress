<?php

namespace ImageSeoWP\Services;

use ImageSeoWP\Helpers\AttachmentMeta;

if (!defined('ABSPATH')) {
	exit;
}

class BulkOptimizer
{
	public $batchSize = 10;
	public $altService;
	public $clientService;
	public $optionService;
	public $fileService;
	public $bulkOptimizerQuery;
	public $generateFilename;

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
		$this->clientService = imageseo_get_service('ClientApi');
		$this->optionService = imageseo_get_service('Option');
		$this->bulkOptimizerQuery = imageseo_get_service('BulkOptimizerQuery');
		$this->altService = imageseo_get_service('Alt');
		$this->fileService = imageseo_get_service('UpdateFile');
		$this->generateFilename = imageseo_get_service('GenerateFilename');

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

		$image_data = [
			'ids' => $images['ids'],
			// This is response from api
			'optimizedIds' => [],
			'failedIds' => [],
			'batchIds' => [],
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
		$totalImages = count($image_data['ids']);
		$startIndex = $batch_number * $this->batchSize;
		$endIndex = min($startIndex + $this->batchSize, $totalImages) - 1;

		$currentBatchIds = array_slice($image_data['ids'], $startIndex, $this->batchSize);

		$images = [];
		foreach ($currentBatchIds as $id) {
			$attachmentUrl = wp_get_attachment_url($id);
			$images[] = [
				"internalId" => $id,
				"url" => $attachmentUrl,
				"requestUrl" => get_site_url(),
			];
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
				$attachmentId = $image['internalId'];
				update_post_meta($attachmentId, AttachmentMeta::REPORT, $image);

				$this->altService->updateAlt($image['internalId'], $image['altText']);

				if ($optimizeFilename) {
					$extension = $this->generateFilename->getExtensionFilenameByAttachmentId($attachmentId);

					$this->fileService->updateFilename(
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

	private function getItemsByBatchId($batchId)
	{
		try {
			$response = wp_remote_get(
				// IMAGESEO_API_URL/projects/v2/images/,
				'http://192.168.1.148:3000/projects/v2/images/' . $batchId,
				[
					'headers' => [
						'Content-Type' => 'application/json',
						'Authorization' => 'Bearer ' . $this->optionService->getOption('apiKey')
					],
				]
			);

			if (is_wp_error($response)) {
				throw new \Exception($response->get_error_message());
			}

			$result = wp_remote_retrieve_body($response);
			return json_decode($result, true);
		} catch (\Exception $e) {
			return $e;
		}
	}

	private function sendRequestToApi($images)
	{
		$dataObj = [
			'images' => $images,
			'lang' => $this->optionService->getOption('defaultLanguageIa')
		];

		try {
			$response = wp_remote_post(
				// IMAGESEO_API_URL/projects/v2/images,
				'http://192.168.1.148:3000/projects/v2/images',
				[
					'headers' => [
						'Content-Type' => 'application/json',
						'Authorization' => 'Bearer ' . $this->optionService->getOption('apiKey')
					],
					'body' => json_encode($dataObj)
				]
			);

			if (is_wp_error($response)) {
				throw new \Exception($response->get_error_message());
			}

			$result = wp_remote_retrieve_body($response);
			return json_decode($result, true);
		} catch (\Exception $e) {
			return $e;
		}
	}
}
