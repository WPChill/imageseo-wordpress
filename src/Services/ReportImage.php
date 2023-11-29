<?php

namespace ImageSeoWP\Services;

if (!defined('ABSPATH')) {
	exit;
}

use ImageSeoWP\Helpers\AttachmentMeta;

class ReportImage
{
	public function __construct()
	{
		$this->clientService = imageseo_get_service('ClientApi');
		$this->optionService = imageseo_get_service('Option');
	}

	/**
	 * @param int $attachmentId
	 *
	 * @return bool
	 */
	public function haveAlreadyReportByAttachmentId($attachmentId)
	{
		return $this->getReportByAttachmentId($attachmentId) ? true : false;
	}

	/**
	 * @param int $attachmentId
	 *
	 * @return array
	 */
	public function getReportByAttachmentId($attachmentId)
	{
		return get_post_meta($attachmentId, AttachmentMeta::REPORT, true);
	}

	/**
	 * @param int   $attachmentId
	 * @param array $query
	 *
	 * @return array
	 */
	public function generateReportByAttachmentIdForNextGen($attachmentId, $query = [], $language = null)
	{
		$storage = \C_Gallery_Storage::get_instance();

		$filePath = $storage->get_image_abspath($attachmentId);
		$metadata = getimagesize($filePath);
		$language = null === $language ? $this->optionService->getOption('default_language_ia') : $language;

		if (!isset($metadata['mime']) || false === strpos($metadata['mime'], 'image')) {
			return;
		}

		$reportImages = $this->clientService->getClient()->getResource('ImageReports', $query);

		if (file_exists($filePath)) {
			try {
				$result = $reportImages->generateReportFromFile([
					'lang'     => $language,
					'filePath' => $filePath,
					'width'    => (is_array($metadata) && !empty($metadata)) ? $metadata[0] : '',
					'height'   => (is_array($metadata) && !empty($metadata)) ? $metadata[1] : '',
				], $query);
			} catch (\Exception $e) {
				$result = $reportImages->generateReportFromUrl([
					'lang'     => $language,
					'src'      => $storage->get_image_url($attachmentId),
					'width'    => (is_array($metadata) && !empty($metadata)) ? $metadata[0] : '',
					'height'   => (is_array($metadata) && !empty($metadata)) ? $metadata[1] : '',
				], $query);
			}
		} else {
			$result = $reportImages->generateReportFromUrl([
				'lang'     => $language,
				'src'      => $storage->get_image_url($attachmentId),
				'width'    => (is_array($metadata) && !empty($metadata)) ? $metadata[0] : '',
				'height'   => (is_array($metadata) && !empty($metadata)) ? $metadata[1] : '',
			], $query);
		}

		if ($result && !$result['success']) {
			return $result;
		}

		$report = $result['result'];

		$postId = imageseo_get_service('QueryNextGen')->getPostIdByNextGenId($attachmentId);

		update_post_meta($postId, AttachmentMeta::REPORT, $report);
		update_post_meta($postId, AttachmentMeta::LANGUAGE, $language);

		return $result;
	}

	/**
	 * @param int   $attachmentId
	 * @param array $query
	 *
	 * @return array
	 */
	public function generateReportByAttachmentId($attachmentId, $query = [], $language = null)
	{
		if (!apply_filters('imageseo_authorize_generate_report_attachment_id', true, $attachmentId)) {
			return;
		}

		$mimeType = get_post_mime_type($attachmentId);
		if (false === strpos($mimeType, 'image')) {
			return;
		}

		$filePath = get_attached_file($attachmentId);
		$metadata = wp_get_attachment_metadata($attachmentId);
		$language = null === $language ? $this->optionService->getOption('default_language_ia') : $language;

		$reportImages = $this->clientService->getClient()->getResource('ImageReports', $query);

		if (file_exists($filePath)) {
			try {
				$result = $reportImages->generateReportFromFile([
					'lang'     => $language,
					'filePath' => $filePath,
					'width'    => (is_array($metadata) && !empty($metadata)) ? $metadata['width'] : 0,
					'height'   => (is_array($metadata) && !empty($metadata)) ? $metadata['height'] : 0,
				], $query);
			} catch (\Exception $e) {
				$result = $reportImages->generateReportFromUrl([
					'lang'     => $language,
					'src'      => $filePath,
					'width'    => (is_array($metadata) && !empty($metadata)) ? $metadata['width'] : 0,
					'height'   => (is_array($metadata) && !empty($metadata)) ? $metadata['height'] : 0,
				], $query);
			}
		} else {
			$result = $reportImages->generateReportFromUrl([
				'lang'     => $language,
				'src'      => $filePath,
				'width'    => (is_array($metadata) && !empty($metadata)) ? $metadata['width'] : 0,
				'height'   => (is_array($metadata) && !empty($metadata)) ? $metadata['height'] : 0,
			], $query);
		}

		if ($result && !isset($result['alts'])) {
			return $result;
		}

		$report = $result;
		update_post_meta($attachmentId, AttachmentMeta::REPORT, $report);
		update_post_meta($attachmentId, AttachmentMeta::LANGUAGE, $language);

		return $result;
	}

	/**
	 * Get name file for an attachment id from a report.
	 *
	 * @param int $attachmentId
	 *
	 * @return string
	 */
	public function getNameFileAttachmentWithId($attachmentId, $params = [])
	{
		$report = $this->getReportByAttachmentId($attachmentId);
		$nameAuto = $this->getAutoContextFromReport($report, $params);

		return apply_filters('imageseo_get_name_file_attachment_id', $nameAuto, $attachmentId);
	}

	/**
	 * @param array $report
	 *
	 * @return string
	 */
	public function getAutoContextFromReport($report, $params = [])
	{
		return $this->getValueAuto('alts', $report, $params);
	}

	public function getAltsFromReport($report)
	{
		$alts = [];

		foreach ($report['alts'] as $alt) {
			if (empty($alt['name'])) {
				continue;
			}

			$alts[] = $alt;
		}

		return $alts;
	}

	/**
	 * @param string $type
	 * @param array  $report
	 * @param array  $params
	 *
	 * @return string
	 */
	protected function getValueAuto($type, $report, $params = [])
	{
		if (!isset($params['min_percent'])) {
			$params['min_percent'] = apply_filters('imageseo_get_value_auto_min_percent', 10, $type, $report);
		}
		if (!isset($params['max_percent'])) {
			$params['max_percent'] = apply_filters('imageseo_get_value_auto_max_percent', 101, $type, $report);
		}

		$altReplace = '';
		$i = 0;
		$total = count($report[$type]);

		while (empty($altReplace) && $i < $total) {
			if (null === $report[$type][$i]['score']) {
				$altReplace = $report[$type][$i]['name'];
				++$i;
				continue;
			}

			if ($params['min_percent'] >= round($report[$type][$i]['score']) || $params['max_percent'] <= round($report[$type][$i]['score'])) {
				++$i;
				continue;
			}

			$altReplace = $report[$type][$i]['name'];
			++$i;
		}

		return $altReplace;
	}
}
