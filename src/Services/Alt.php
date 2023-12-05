<?php

namespace ImageSeoWP\Services;

if (!defined('ABSPATH')) {
	exit;
}

class Alt
{
	public function __construct()
	{
		$this->reportImageService = imageseo_get_service('ReportImage');
		$this->optionServices = imageseo_get_service('Option');
		$this->tagsToStringServices = imageseo_get_service('TagsToString');
	}

	/**
	 * @param int $attachmentId
	 */
	public function generateForAttachmentId($attachmentId, $query = [])
	{
		$report = $this->reportImageService->getReportByAttachmentId($attachmentId);

		if (!$report) {
			try {
				$response = $this->reportImageService->generateReportByAttachmentId($attachmentId, $query);
			} catch (\Exception $e) {
				return [
					'success' => false,
				];
			}

			if (isset($response['error'])) {
				return [
					'success' => false,
					'error' => $response['error']
				];
			}

			$report = $response;
		}

		$this->updateAltAttachmentWithReport($attachmentId);

		return [
			'success' => true,
		];
	}

	/**
	 * @param int   $attachmentId
	 * @param array $report
	 */
	public function updateAltAttachmentWithReport($attachmentId)
	{
		$template = $this->optionServices->getOption('alt_template_default');
		$alt = $this->tagsToStringServices->replace($template, $attachmentId);
		$alt = apply_filters('imageseo_update_alt_attachment_value', $alt, $attachmentId);

		$this->updateAlt($attachmentId, $alt);
	}

	/**
	 * @param int    $attachmentId
	 * @param string $alt
	 */
	public function updateAlt($attachmentId, $alt)
	{
		$currentAlt = $this->getAlt($attachmentId);
		update_post_meta($attachmentId, '_wp_attachment_image_alt', apply_filters('imageseo_update_alt', $alt, $attachmentId));

		// Update counter
		if (empty($currentAlt) && !empty($alt)) { // Empty => Not empty
			$total = get_option('imageseo_get_number_image_non_optimize_alt');
			if ($total) {
				update_option('imageseo_get_number_image_non_optimize_alt', (int) $total - 1, false);
			}
		} elseif (empty($alt)) { // No alt
			$total = get_option('imageseo_get_number_image_non_optimize_alt');
			if ($total) {
				update_option('imageseo_get_number_image_non_optimize_alt', (int) $total + 1, false);
			}
		}
	}

	/**
	 * @param int $id
	 *
	 * @return string
	 */
	public function getAlt($id)
	{
		return get_post_meta($id, '_wp_attachment_image_alt', true);
	}
}
