<?php

namespace ImageSeoWP\Processes;

use Exception;
use ImageSeoWP\Async\WPAsyncRequest;
use ImageSeoWP\Helpers\AttachmentMeta;
use ImageSeoWP\Traits\ApiHandler;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class OnUploadImage extends WPAsyncRequest {

	use ApiHandler;

	protected $prefix = 'imageseo';
	protected $action = 'on_upload_image';

	public static $instance;

	public static function getInstance(): OnUploadImage {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof OnUploadImage ) ) {
			self::$instance = new OnUploadImage();
		}

		return self::$instance;
	}

	protected function handle() {
		$this->setServices();

		if ( imageseo_get_service( 'UserInfo' )->hasLimitExcedeed() ) {
			return;
		}

		$attachmentId     = $_POST['attachmentId'];
		$optimizeAlt      = $_POST['activeAltWriteUpload'];
		$optimizeFilename = $_POST['activeRenameWriteUpload'];
		$optimizeTitle    = $_POST['activeOptimizeTitle'];
		$optimizeCaption  = $_POST['activeOptimizeCaption'];

		if ( empty( $attachmentId ) ) {
			return;
		}

		if ( empty( $optimizeAlt )
		&& empty( $optimizeFilename )
		&& empty( $optimizeTitle )
		&& empty( $optimizeCaption )
		) {
			return;
		}

		$extension = $this->generateFilename->getExtensionFilenameByAttachmentId( $attachmentId );
		if ( ! in_array( $extension, array( 'png', 'jpg', 'jpeg', 'webp' ) ) ) {
			return;
		}

		$images = array(
			$this->createApiImage( $attachmentId ),
		);

		$response = $this->sendRequestToApi( $images, true );

		if ( $response instanceof Exception ) {
			error_log( $response->getMessage() );

			return;
		}

		if ( ! isset( $response['batchId'] ) ) {
			return;
		}

		$batchId = $response['batchId'];
		$items   = $this->getItemsByBatchId( $batchId );
		if ( $items instanceof Exception ) {
			error_log( $items->getMessage() );

			return;
		}

		$image        = $items[0];
		$attachmentId = $image['internalId'];

		if ( $optimizeAlt ) {
			$this->altService->updateAlt( $image['internalId'], $image['altText'] );
		}

		if ( $optimizeFilename ) {
			$extension = $this->generateFilename->getExtensionFilenameByAttachmentId( $attachmentId );
			$this->fileService->updateFilename(
				$image['internalId'],
				sprintf( '%s.%s', $image['filename'], $extension )
			);
		}

		if ( $optimizeTitle &&
		isset( $image['title'] ) &&
		! empty( $image['title'] )
		) {
			$post_data = array(
				'ID'         => $attachmentId,
				'post_title' => $image['title'],
			);

			wp_update_post( $post_data );
		}

		if ( $optimizeCaption &&
		isset( $image['caption'] ) &&
		! empty( $image['caption'] ) ) {
			$post_data = array(
				'ID'           => $attachmentId,
				'post_excerpt' => $image['caption'],
			);

			wp_update_post( $post_data );
		}

		update_post_meta( $attachmentId, AttachmentMeta::REPORT, $image );
	}
}
