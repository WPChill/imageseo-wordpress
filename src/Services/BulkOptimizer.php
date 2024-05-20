<?php

namespace ImageSeoWP\Services;

use ImageSeoWP\Helpers\AttachmentMeta;
use ImageSeoWP\Helpers\Bulk\AltSpecification;
use ImageSeoWP\Traits\ApiHandler;
use ImageSeoWP\Traits\Debug;
use ImageSeoWP\Traits\SimpleLock;

if (!defined('ABSPATH')) {
	exit;
}

class BulkOptimizer
{
	use ApiHandler;
	use Debug;
	use SimpleLock;

	// Internal
	public bool $debug = true;
	public string $debugOption = 'imageseo_bulk_optimizer_debug';
	public string $lockName = 'imageseo_bulk_optimizer_lock';

	// Usability
	public int $batchSize = 10;

	// Default report
	public array $defaultLastReport = [
		'total' => 0,
		'optimized' => 0,
		'failed' => 0,
		'skipped' => 0,
		'errors' => [],
	];

	public static $instance;

	public static function getInstance(): BulkOptimizer
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
		add_action('check_optimizer_finished', [$this, 'checkOptimizerFinished'], 10, 0);
	}

	public function getDebug(): array
	{
		return get_option($this->debugOption, []);
	}

	public function getErrors(): array
	{
		$report = get_option('imageseo_bulk_optimizer_last_report', $this->defaultLastReport);
		return $report['errors'] ?? [];
	}

	public function getStatus(): array
	{
		$imageData = get_option('imageseo_bulk_image_data', false);
		$report = get_option('imageseo_bulk_optimizer_last_report', $this->defaultLastReport);

		if ($imageData === false) {
			return [
				'status' => 'idle',
				'report' => $report
			];
		}

		$report['total'] = count($imageData['ids']);
		$report['optimized'] = count($imageData['optimizedIds']);
		$report['failed'] = count($imageData['failedIds'] ?? []);
		$report['remaining'] = $report['total'] - $report['optimized'] - $report['failed'];
		$report['skipped'] = $report['failed'];

		return [
			'status' => get_option('imageseo_bulk_optimizer_status', 'idle'),
			'report' => $report
		];
	}

	public function start(): array
	{
		$this->resetDebug();
		$images = $this->bulkOptimizerQuery->getImages();
		$options = imageseo_get_options();
		$ids = $options['altFill'] === AltSpecification::FILL_ALL ? $images['ids'] : $images['nonOptimized'];

		$imageData = [
			'ids' => $options['altFill'] === AltSpecification::FILL_ALL ? $images['ids'] : $images['nonOptimized'],
			// This is response from api
			'optimizedIds' => [],
			'failedIds' => [],
			'batchIds' => [],
			'totalBatches' => ceil(count($ids) / $this->batchSize),
			'batchSentToProcessing' => [],
			'batchProcessedAndReceived' => [],
			'library' => $options['altFilter']
		];

		$this->writeDebug([
			__('Total images to optimize:', 'imageseo') . ' ' . count($images['ids']),
			__('Total batches:', 'imageseo') . ' ' . $imageData['totalBatches']
		]);

		$report = [
			'total' 	=> count($ids),
			'optimized' => 0,
			'remaining' => count($ids),
			'failed' 	=> 0,
			'skipped' 	=> 0,
			'errors' 	=> [],
		];

		if (empty($imageData['ids'])) {
			$report['errors'][] = __('No images to optimize', 'imageseo');
			$report['debug'][] = __('No images to optimize', 'imageseo');

			update_option('imageseo_bulk_optimizer_last_report', $report);
			return [
				'status' => 'idle',
				'report' => $report
			];
		}

		update_option('imageseo_bulk_image_data', $imageData);
		update_option('imageseo_bulk_optimizer_status', 'running');
		update_option('imageseo_bulk_optimizer_last_report', $report);

		$this->writeDebug(__('Schedule first batch with index 0', 'imageseo'));

		as_schedule_single_action(
			time(),
			'process_image_batch',
			['batchNumber' => 0, 'timestamp' => current_time('timestamp')]
		);

		return [
			'status' => 'running',
			'report' => $report
		];
	}

	public function stop(): array
	{
		update_option('imageseo_bulk_optimizer_status', 'idle');
		delete_option('imageseo_bulk_image_data');
		$report = get_option('imageseo_bulk_optimizer_last_report', $this->defaultLastReport);
		$this->writeDebug(__('Bulk optimizer stopped manually', 'imageseo'));

		return [
			'status' => 'idle',
			'report' => $report
		];
	}

	public function checkOptimizerFinished()
	{
		if (!$this->hasProcessedAllBatches()) {
			as_schedule_single_action(
				time() + 10,
				'check_optimizer_finished',
				[]
			);
			return;
		}

		$this->writeDebug(__('All batches processed', 'imageseo'));
		$this->finalizeProcessing();
	}

	public function processImageBatch($batchNumber, $timestamp)
	{
		// Check if the batch can be processed
		if (!$this->acquireLock($this->lockName)) {
			$this->writeDebug(__('Lock already acquired', 'imageseo'));
			as_schedule_single_action(
				time() + 10,
				'process_image_batch',
				['batchNumber' => $batchNumber, 'timestamp' => $timestamp]
			);
			return;
		}

		// Check if the API limits have been reached
		if ($this->checkApiLimits()) {
			$this->writeDebug(__('API limits reached', 'imageseo'));
			$this->releaseLock($this->lockName);
			return;
		}

		// Check if the batch can be processed
		$batchData = $this->prepareBatchData($batchNumber);
		if (empty($batchData)) {
			$this->writeDebug(__('No images to optimize', 'imageseo'));
			$this->releaseLock($this->lockName);
			return;
		}

		// Send the batch to the API
		$result = $this->sendBatchToApi($batchData);
		if ($result instanceof \Exception) {
			$this->logApiError($result);
			$this->releaseLock($this->lockName);
			return;
		}

		// Update the batch status and schedule the next batch
		$this->updateBatchStatus($result, $batchNumber);
		$this->scheduleNextBatch($batchNumber, $timestamp);
		// Release the lock
		$this->releaseLock($this->lockName);
	}

	public function checkImageBatch($batchNumber)
	{
		if (!$this->acquireLock($this->lockName)) {
			$this->writeDebug(__('Lock already acquired', 'imageseo'));
			$this->rescheduleBatch($batchNumber);
			return;
		}

		if (!$this->isImageDataAvailable()) {
			$this->writeDebug(__('No images to optimize', 'imageseo'));
			$this->finalizeProcessing();
			$this->releaseLock($this->lockName);
			return;
		}

		if (!$this->isBatchSentToProcessing($batchNumber)) {
			$this->writeDebug(__('Batch not sent to processing:', 'imageseo') . ' ' . $batchNumber);
			$this->rescheduleBatch($batchNumber);
			$this->releaseLock($this->lockName);
			return;
		}

		$batchId = $this->getBatchId($batchNumber);
		if (is_null($batchId)) {
			$this->writeDebug(__('Batch ID not found for batch number:', 'imageseo') . ' ' . $batchNumber);
			$this->handleMissingBatchId($batchNumber);
			$this->releaseLock($this->lockName);
			return;
		}

		$batchData = $this->getItemsByBatchId($batchId);
		if ($batchData instanceof \Exception) {
			$this->writeDebug(__('Error retrieving batch data:', 'imageseo') . ' ' . $batchData->getMessage());
			$this->handleBatchDataError($batchData);
			$this->releaseLock($this->lockName);
			return;
		}

		if (!$this->allImagesProcessed($batchData)) {
			$this->writeDebug(__('Not all images processed for batch number:', 'imageseo') . ' ' . $batchNumber);
			$this->rescheduleBatch($batchNumber);
			$this->releaseLock($this->lockName);
			return;
		}

		$this->updateImageStatuses($batchData, $batchNumber);
		$this->writeDebug(__('Finalizing batch processing for batch number: ', 'imageseo') . $batchNumber);
		$this->releaseLock($this->lockName);

		as_schedule_single_action(
			time() + 10,
			'check_optimizer_finished',
			[]
		);
	}

	private function isImageDataAvailable()
	{
		return get_option('imageseo_bulk_image_data', false) !== false;
	}

	private function hasProcessedAllBatches()
	{
		$imageData = get_option('imageseo_bulk_image_data');
		return count($imageData['batchProcessedAndReceived']) >= $imageData['totalBatches'];
	}

	private function isBatchSentToProcessing($batchNumber)
	{
		$imageData = get_option('imageseo_bulk_image_data');
		return in_array($batchNumber, $imageData['batchSentToProcessing']);
	}

	private function getBatchId($batchNumber)
	{
		$imageData = get_option('imageseo_bulk_image_data');
		return $imageData['batchIds'][$batchNumber] ?? null;
	}

	private function handleMissingBatchId($batchNumber)
	{
		$report = get_option('imageseo_bulk_optimizer_last_report', $this->defaultLastReport);
		$report['errors'][] = __('Batch ID not found for batch number: ', 'imageseo') . $batchNumber;
		update_option('imageseo_bulk_optimizer_last_report', $report);
		$this->writeDebug(__('Batch ID not found for batch number: ', 'imageseo') . $batchNumber);
		update_option('imageseo_bulk_optimizer_status', 'idle');
	}

	private function handleBatchDataError($exception)
	{
		$report = get_option('imageseo_bulk_optimizer_last_report', $this->defaultLastReport);
		$report['errors'][] = $exception->getMessage();
		update_option('imageseo_bulk_optimizer_last_report', $report);
		$this->writeDebug($exception->getMessage());
		update_option('imageseo_bulk_optimizer_status', 'idle');
	}

	private function allImagesProcessed($batchData)
	{
		foreach ($batchData as $image) {
			if (!$image['resolved'] && !$image['failed']) {
				return false;
			}
		}
		return true;
	}

	private function updateImageStatuses($batchData, $batchNumber)
	{
		$isNextGen = $this->isNextGenGallery();
		$optimizeFilename = $this->optionService->getOption('optimizeFile');
		$optimized = [];
		$failed = [];

		$report = get_option('imageseo_bulk_optimizer_last_report');
		foreach ($batchData as $image) {
			if ($image['resolved']) {
				$this->handleResolvedImage($image, $isNextGen, $optimizeFilename, $optimized);
				continue;
			}

			if ($image['failed']) {
				$this->handleFailedImage($image, $report, $failed);
				continue;
			}
		}

		$imageData = get_option('imageseo_bulk_image_data');
		$imageData['optimizedIds'] = array_merge($imageData['optimizedIds'], $optimized);
		$imageData['failedIds'] = array_merge($imageData['failedIds'], $failed);
		$imageData['batchProcessedAndReceived'][] = $batchNumber;

		$this->writeDebug([
			__('Batch processed and received:', 'imageseo') . ' ' . $batchNumber,
			__('Optimized:', 'imageseo') . ' ' . count($optimized),
			__('Failed:', 'imageseo') . ' ' . count($failed)
		]);

		$report['optimized'] += count($optimized);
		$report['failed'] += count($failed);

		update_option('imageseo_bulk_image_data', $imageData);
		update_option('imageseo_bulk_optimizer_last_report', $report);
	}

	private function handleResolvedImage($image, $isNextGen, $optimizeFilename, &$optimized)
	{
		$attachmentId = $isNextGen
			? imageseo_get_service('QueryNextGen')->getPostIdByNextGenId($image['internalId']) : $image['internalId'];

		$postMeta = get_post_meta($attachmentId, AttachmentMeta::REPORT, true);


		$skipAlt = false;
		$skipFilename = false;

		if ($postMeta) {
			if (isset($postMeta['forced_altText']) && $postMeta['forced_altText']) {
				$this->writeDebug(__('Alt text was manually added for this image, skipping', 'imageseo'));
				$skipAlt = true;
				$image['altText'] = $postMeta['altText'];
			}

			if (isset($postMeta['forced_filename']) && $postMeta['forced_filename']) {
				$this->writeDebug(__('Filename was manually added for this image, skipping', 'imageseo'));
				$skipFilename = true;
				$image['filename'] = $postMeta['filename'];
			}
		}

		update_post_meta($attachmentId, AttachmentMeta::REPORT, $image);
		if (!$skipAlt) {
			$isNextGen
				? imageseo_get_service('QueryNextGen')->updateAlt($image['internalId'], $image['altText'])
				: $this->altService->updateAlt($image['internalId'], $image['altText']);
			$this->writeDebug($image['internalId'] . ': ' . $image['altText']);
		}

		if ($optimizeFilename && !$skipFilename) {
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
			$this->writeDebug($image['internalId'] . ': ' . $image['filename'] . '.' . $extension);
		}

		$optimized[] = $image['internalId'];
	}

	private function handleFailedImage($image, &$report, &$failed)
	{
		$report['errors'][] = $image['failureDetails'];
		$this->writeDebug([
			__('Failed image:', 'imageseo') . ' ' . $image['internalId'],
			$image['failureDetails']
		]);
		$failed[] = $image['internalId'];
	}

	private function finalizeProcessing()
	{
		update_option('imageseo_bulk_optimizer_status', 'idle');
		$this->writeDebug(__('Finalizing entire batch processing.', 'imageseo'));
	}

	private function checkApiLimits()
	{
		if (imageseo_get_service('UserInfo')->hasLimitExcedeed()) {
			$this->handleApiLimitExceeded();
			return true;
		}
		return false;
	}

	private function prepareBatchData($batchNumber)
	{
		$imageData = get_option('imageseo_bulk_image_data');
		$startIndex = $batchNumber * $this->batchSize;
		$endIndex = min($startIndex + $this->batchSize, count($imageData['ids'])) - 1;

		$currentBatchIds = array_slice($imageData['ids'], $startIndex, $this->batchSize);

		$this->writeDebug([
			__('Processing batch number:', 'imageseo') . ' ' . $batchNumber,
			__('Start index:', 'imageseo') . ' ' . $startIndex,
			__('End index:', 'imageseo') . ' ' . $endIndex,
			__('Current batch IDs:', 'imageseo') . ' ' . implode(', ', $currentBatchIds)
		]);

		return array_filter($currentBatchIds, function ($id) {
			return $this->isValidImageType($id);
		});
	}

	private function sendBatchToApi($batchData)
	{
		$nextGen = $this->isNextGenGallery();
		$images = array_map(function ($id) use ($nextGen) {
			return $this->createApiImage($id, $nextGen);
		}, $batchData);

		return $this->sendRequestToApi($images);
	}

	private function scheduleNextBatch($batchNumber, $timestamp)
	{
		$imageData = get_option('imageseo_bulk_image_data');
		$totalImages = count($imageData['ids']);
		$endIndex = min(($batchNumber + 1) * $this->batchSize, $totalImages) - 1;
		$this->writeDebug(__('Schedule check image batch with index 0', 'imageseo'));

		as_schedule_single_action(
			time() + 5,
			'check_image_batch',
			['batchNumber' => $batchNumber]
		);

		if ($endIndex < $totalImages - 1) {
			$this->writeDebug(__('Schedule next batch with index:', 'imageseo') . ' ' . ($batchNumber + 1));

			as_schedule_single_action(
				time() + 10,
				'process_image_batch',
				['batchNumber' => $batchNumber + 1, 'timestamp' => $timestamp]
			);
		}
	}

	private function handleApiLimitExceeded()
	{
		$report = get_option('imageseo_bulk_optimizer_last_report', $this->defaultLastReport);
		$report['errors'][] = __('You have reached the limit of images to optimize', 'imageseo');
		update_option('imageseo_bulk_optimizer_last_report', $report);
		update_option('imageseo_bulk_optimizer_status', 'idle');
		$this->writeDebug(__('Limit exceeded, processing stopped.', 'imageseo'));
	}

	private function logApiError($exception)
	{
		$report = get_option('imageseo_bulk_optimizer_last_report', $this->defaultLastReport);
		$report['errors'][] = $exception->getMessage();
		update_option('imageseo_bulk_optimizer_last_report', $report);
		update_option('imageseo_bulk_optimizer_status', 'idle');
		$this->writeDebug(__('API Error: ', 'imageseo') . $exception->getMessage());
	}

	private function updateBatchStatus($result, $batchNumber)
	{
		$imageData = get_option('imageseo_bulk_image_data');
		$imageData['batchIds'][] = $result[0]['batchId'];
		$imageData['batchSentToProcessing'][] = $batchNumber;

		update_option('imageseo_bulk_image_data', $imageData);
		$this->writeDebug(__('Batch ' . $batchNumber . ' updated with ID ' . $result[0]['batchId'], 'imageseo'));
	}

	private function rescheduleBatch($batchNumber)
	{
		$this->writeDebug(__('Rescheduling batch number:', 'imageseo') . ' ' . $batchNumber);
		as_schedule_single_action(time() + 5, 'check_image_batch', ['batchNumber' => $batchNumber]);
	}

	private function isValidImageType($id)
	{
		$extension = $this->generateFilename->getExtensionFilenameByAttachmentId($id);
		if (!in_array($extension, ['png', 'jpg', 'jpeg'])) {
			$this->writeDebug(__('Unsupported file format:', 'imageseo') . ' ' . $extension);
			return false;
		}
		return true;
	}

	private function isNextGenGallery()
	{
		$imageData = get_option('imageseo_bulk_image_data');
		return $imageData['library'] === 'NEXTGEN_GALLERY';
	}

	private function canProcessBatch($batchNumber)
	{
		$imageData = get_option('imageseo_bulk_image_data', false);
		return isset($imageData['batchIds'][$batchNumber]);
	}

	public function extractExtension($url): string
	{
		return pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
	}
}
