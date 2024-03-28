<?php

namespace ImageSeoWP\Traits;

trait ApiHandler
{
	public $optionService;
	public $altService;
	public $fileService;
	public $generateFilename;
	public $generateFilenameNG;
	public $clientService;
	public $bulkOptimizerQuery;

	public function setServices()
	{
		$this->optionService = imageseo_get_service('Option');
		$this->altService = imageseo_get_service('Alt');
		$this->fileService = imageseo_get_service('UpdateFile');
		$this->generateFilename = imageseo_get_service('GenerateFilename');
		$this->generateFilenameNG = imageseo_get_service('GenerateFilenameNextGen');
		$this->clientService = imageseo_get_service('ClientApi');
		$this->bulkOptimizerQuery = imageseo_get_service('BulkOptimizerQuery');
	}


	/**
	 * Creates an API image object.
	 *
	 * @param int $id The internal ID of the image.
	 * @param bool $isNextGen Whether the image is a NextGen image.
	 * @return array The API image object.
	 */
	public function createApiImage($id, $isNextGen = false)
	{
		$attachmentUrl = $isNextGen ? $this->_getNGUrl($id) : wp_get_attachment_url($id);
		return [
			"internalId" => $id,
			"url" => $attachmentUrl,
			"requestUrl" => get_site_url(),
		];
	}

	private function _getNGUrl($id)
	{
		if (!class_exists('C_Gallery_Storage')) {
			throw new \Exception('C_Gallery_Storage class not found. Maybe NextGen Gallery is not installed.');
		}

		$storage = \C_Gallery_Storage::get_instance();
		return $storage->get_image_url($id);
	}

	/**
	 * Retrieves items by batch ID from the API.
	 *
	 * @param int $batchId The ID of the batch.
	 */
	private function getItemsByBatchId($batchId)
	{
		try {
			$response = wp_remote_get(
				IMAGESEO_API_URL . '/projects/v2/images/' . $batchId,
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

	private function sendRequestToApi($images, $single = false)
	{
		$dataObj = [
			'images' => $images,
			'lang' => $this->optionService->getOption('defaultLanguageIa')
		];

		try {
			$response = wp_remote_post(
				IMAGESEO_API_URL . '/projects/v2/' . ($single ? 'image' : 'images') . '/',
				[
					'headers' => [
						'Content-Type' => 'application/json',
						'Authorization' => 'Bearer ' . $this->optionService->getOption('apiKey')
					],
					'body' => json_encode($dataObj),
					'timeout' => 45,
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
