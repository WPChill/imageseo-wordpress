<?php

namespace ImageSeoWP\Actions\Admin;

use ImageSeoWP\Processes\OnUploadImage;

if (!defined('ABSPATH')) {
	exit;
}

class MediaLibrary
{
	public $optionService;
	public $reportImageService;
	public $generateFilename;
	public $altService;

	public function __construct()
	{
		$this->optionService = imageseo_get_service('Option');
		$this->reportImageService = imageseo_get_service('ReportImage');
		$this->generateFilename = imageseo_get_service('GenerateFilename');
		$this->altService = imageseo_get_service('Alt');
	}

	public function hooks()
	{
		if (!imageseo_allowed()) {
			return;
		}

		add_filter('manage_media_columns', [$this, 'manageMediaColumns']);
		add_action('manage_media_custom_column', [$this, 'manageMediaCustomColumn'], 10, 2);

		add_action('wp_ajax_imageseo_media_alt_update', [$this, 'ajaxAltUpdate']);

		add_filter('wp_generate_attachment_metadata', [$this, 'createProcessOnUpload'], 10, 2);
		add_action('delete_attachment', [$this, 'updateDeleteCount'], 100);
		add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
	}

	public function muteOnUpload()
	{
		remove_filter('wp_generate_attachment_metadata', [$this, 'createProcessOnUpload'], 10, 2);
	}

	public function createProcessOnUpload($metadata, $attachmentId)
	{
		if (!wp_attachment_is_image($attachmentId)) {
			return $metadata;
		}

		$activeAltOnUpload = $this->optionService->getOption('activeAltWriteUpload');
		$activeRenameOnUpload = $this->optionService->getOption('activeRenameWriteUpload');

		$total = get_option('imageseo_get_total_images');
		if ($total) {
			update_option('imageseo_get_total_images', (int) $total + 1, false);
		}

		if (!$activeAltOnUpload) {
			$total = get_option('imageseo_get_number_image_non_optimize_alt');
			if ($total) {
				update_option('imageseo_get_number_image_non_optimize_alt', (int) $total + 1, false);
			}
		}

		if (!$activeAltOnUpload && !$activeRenameOnUpload) {
			return $metadata;
		}

		$asyncRequest = OnUploadImage::getInstance();
		$asyncRequest->data([
			'attachment_id' => $attachmentId,
			'active_alt_on_upload' => $activeAltOnUpload,
			'active_rename_on_upload' => $activeRenameOnUpload,
		]);

		$asyncRequest->dispatch();
		return $metadata;
	}

	/**
	 * @param int $attachmentId
	 */
	public function updateDeleteCount($attachmentId)
	{
		if (!wp_attachment_is_image($attachmentId)) {
			return;
		}

		$alt = $this->altService->getAlt($attachmentId);

		$total = get_option('imageseo_get_number_image_non_optimize_alt');
		if ($total) {
			update_option('imageseo_get_number_image_non_optimize_alt', (int) $total - 1, false);
		}

		$total = get_option('imageseo_get_total_images');
		if ($total) {
			update_option('imageseo_get_total_images', (int) $total - 1, false);
		}
	}

	public function ajaxAltUpdate()
	{
		check_ajax_referer('imageseo_upload_nonce', '_nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error([
				'code' => 'not_authorized',
			]);
			exit;
		}

		$postId = absint($_POST['post_id']);
		$alt = wp_strip_all_tags($_POST['alt']);

		imageseo_get_service('Alt')->updateAlt($postId, $alt);
	}

	/**
	 * Activate array.
	 */
	public function manageMediaColumns($columns)
	{
		$columns['imageseo_alt'] = __('Alt (imageseo)', 'imageseo');
		$columns['imageseo_filename'] = __('Filename (imageseo)', 'imageseo');

		return $columns;
	}

	protected function renderAlt($attachmentId)
	{
		$alt = wp_strip_all_tags($this->altService->getAlt($attachmentId)); ?>
		<div class="media-column-imageseo-alt" data-id="<?php echo absint($attachmentId); ?>" data-alt="<?php echo $alt; ?>">
		</div>
	<?php
	}

	public function renderFilename($attachmentId)
	{
		$filename = $this->generateFilename->getFilenameByAttachmentId($attachmentId);
	?>
		<div class="media-column-imageseo-filename" data-id="<?php echo absint($attachmentId); ?>" data-filename="<?php echo esc_html($filename); ?>"></div>
<?php
	}

	/**
	 * @param string $columnName    Name of the custom column.
	 * @param int    $attachment_id Attachment ID.
	 */
	public function manageMediaCustomColumn($columnName, $attachmentId)
	{
		switch ($columnName) {
			case 'imageseo_alt':
				$this->renderAlt($attachmentId);
				break;
			case 'imageseo_filename':
				$this->renderFilename($attachmentId);
				break;
		}
	}

	/**
	 * enqueue scripts and styles for the media library
	 *
	 * @param [type] $page
	 * @return void
	 */
	public function enqueue_scripts($page)
	{
		if ($page !== 'upload.php') {
			return;
		}

		$asset_script_path = IMAGESEO_DIR_DIST . '/adminv2/index.asset.php';
		$asset_file = require $asset_script_path;
		wp_enqueue_media();
		wp_enqueue_script(
			'imageseo-admin-v2',
			IMAGESEO_URL_DIST . '/adminv2/index.js',
			$asset_file['dependencies'],
			$asset_file['version'],
		);
		wp_enqueue_style(
			'imageseo-admin-v2',
			IMAGESEO_URL_DIST . '/adminv2/index.css',
			['wp-components'],
			$asset_file['version'],
		);
	}
}
