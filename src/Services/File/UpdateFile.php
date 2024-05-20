<?php

namespace ImageSeoWP\Services\File;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class UpdateFile
{
	/**
	 * Updates the filename of an attachment.
	 *
	 * @param int $attachmentId The attachment ID.
	 * @param string $newFilename The new filename.
	 * @return bool True if successful, false otherwise.
	 */
	public function updateFilename($attachmentId, $newFilename)
	{
		if (!$newFilename) {
			return false;
		}

		$metadata = wp_get_attachment_metadata($attachmentId);
		if (!$metadata || !isset($metadata['file'])) {
			return false;
		}

		$uploadDirectoryData = wp_get_upload_dir();
		$fileRootDirectories = explode('/', $metadata['file']);
		$oldFilename = end($fileRootDirectories);
		$fileRoot = str_replace($oldFilename, '', $metadata['file']);
		$src = $uploadDirectoryData['basedir'] . '/' . $metadata['file'];

		if (!file_exists($src)) {
			return false;
		}

		$newMetadata = $this->processFileUpdate($attachmentId, $src, $oldFilename, $newFilename, $metadata, $fileRoot, $uploadDirectoryData['basedir']);
		if (!$newMetadata) {
			return false;
		}

		return $this->finalizeUpdate($attachmentId, $newMetadata, $newMetadata['uniqueNewFilename']);
	}

	/**
	 * Handles the processing of the file update, including renaming and metadata adjustment.
	 */
	private function processFileUpdate($attachmentId, $src, $oldFilename, $newFilename, $metadata, $fileRoot, $baseDir)
	{
		$oldFilenameWithoutExtension = pathinfo($oldFilename, PATHINFO_FILENAME);
		$extension = pathinfo($newFilename, PATHINFO_EXTENSION);
		$newFilenameWithoutExtension = pathinfo($newFilename, PATHINFO_FILENAME);

		// Find a unique filename in the target directory
		$uniqueNewFilename = $this->findUniqueFilename($baseDir . '/' . $fileRoot, $newFilenameWithoutExtension, $extension);
		$destination = $baseDir . '/' . $fileRoot . $uniqueNewFilename;

		if (!@rename($src, $destination)) {
			return false;
		}

		$oldUrl = wp_get_attachment_url($attachmentId);
		$newUrl = str_replace($oldFilenameWithoutExtension, pathinfo($uniqueNewFilename, PATHINFO_FILENAME), $oldUrl);

		$this->updatePostmetas($oldUrl, $newUrl);
		$this->updatePosts($oldUrl, $newUrl);

		// Update metadata for all sizes using the unique new filename
		$newMetadata = $metadata;
		$newMetadata['file'] = $fileRoot . $uniqueNewFilename; // Use the unique filename
		$newMetadata['uniqueNewFilename'] = $uniqueNewFilename;
		foreach ($metadata['sizes'] as $key => $size) {
			$srcBySize = $baseDir . '/' . $fileRoot . $size['file'];
			$newFileBySize = str_replace($oldFilenameWithoutExtension, pathinfo($uniqueNewFilename, PATHINFO_FILENAME), $size['file']);
			$destinationBySize = $baseDir . '/' . $fileRoot . $newFileBySize;

			if (file_exists($srcBySize) && @rename($srcBySize, $destinationBySize)) {
				$newMetadata['sizes'][$key]['file'] = $newFileBySize;

				$oldUrl = wp_get_attachment_image_src($attachmentId, $key)[0];
				$newUrl = str_replace($oldFilenameWithoutExtension, pathinfo($uniqueNewFilename, PATHINFO_FILENAME), $oldUrl);

				$this->updatePostmetas($oldUrl, $newUrl);
				$this->updatePosts($oldUrl, $newUrl);
			}
		}

		return $newMetadata;
	}

	/**
	 * Updates filename for NextGen gallery images.
	 *
	 * @param int $attachmentId The attachment ID.
	 * @param string $newFilename The new filename.
	 * @return bool True if successful, false otherwise.
	 */
	public function updateFilenameForNextGen($attachmentId, $newFilename)
	{
		if (!$newFilename) {
			return false;
		}

		$storage = \C_Gallery_Storage::get_instance();
		$imageObj = imageseo_get_service('QueryNextGen')->getImage($attachmentId);

		if (!$imageObj) {
			return false;
		}

		$oldPaths = [
			'backup' => $storage->get_backup_abspath($imageObj),
			'full'   => $storage->get_image_abspath($imageObj),
			'thumbs' => $storage->get_image_abspath($imageObj, 'thumbs'),
		];

		$oldUrl = $storage->get_image_url($imageObj);
		$imageObj->filename = $newFilename;
		$newUrl = $storage->get_image_url($imageObj);

		foreach ($oldPaths as $type => $srcPath) {
			if (!file_exists($srcPath)) {
				continue;
			}

			$extension = pathinfo($newFilename, PATHINFO_EXTENSION);
			$newFilenameWithoutExtension = pathinfo($newFilename, PATHINFO_FILENAME);
			$newFilenameWithPrefix = $type === 'thumbs' ? sprintf('thumbs_%s', $newFilenameWithoutExtension) : $newFilenameWithoutExtension;

			$uniqueNewFilename = $this->findUniqueFilename(dirname($srcPath), $newFilenameWithPrefix, $extension);
			$destinationPath = dirname($srcPath) . '/' . $uniqueNewFilename;

			if (!@rename($srcPath, $destinationPath)) {
				return false;
			}
		}

		$this->updatePostmetas($oldUrl, $newUrl);
		$this->updatePosts($oldUrl, $newUrl);

		$image_mapper = \C_Image_Mapper::get_instance();
		$image_mapper->save($imageObj);

		return true;
	}

	/**
	 * Updates post meta to reflect the new image URLs.
	 */
	private function updatePostmetas($oldUrl, $newUrl)
	{
		global $wpdb;
		$wpdb->query($wpdb->prepare("UPDATE $wpdb->postmeta SET meta_value = REPLACE(meta_value, %s, %s) WHERE meta_value LIKE %s", $oldUrl, $newUrl, '%' . $wpdb->esc_like($oldUrl) . '%'));
	}

	/**
	 * Updates posts to replace old image URLs with new ones.
	 */
	private function updatePosts($oldUrl, $newUrl)
	{
		global $wpdb;
		$wpdb->query($wpdb->prepare("UPDATE $wpdb->posts SET post_content = REPLACE(post_content, %s, %s) WHERE post_content LIKE %s", $oldUrl, $newUrl, '%' . $wpdb->esc_like($oldUrl) . '%'));
	}

	private function finalizeUpdate($attachmentId, $newMetadata, $newFilenameWithoutExtension)
	{
		// Store the original metadata and file path before updating
		$originalMetadata = wp_get_attachment_metadata($attachmentId);
		$originalFilePath = get_attached_file($attachmentId);

		wp_update_attachment_metadata($attachmentId, $newMetadata);
		update_attached_file($attachmentId, $newMetadata['file']);

		// Update the post title and slug based on the new filename
		wp_update_post([
			'ID' => $attachmentId,
			'post_title' => $newFilenameWithoutExtension,
			'post_name' => sanitize_title($newFilenameWithoutExtension),
		]);

		// Save the original metadata and file path as post meta for potential rollback
		update_post_meta($attachmentId, '_old_wp_attachment_metadata', $originalMetadata);
		update_post_meta($attachmentId, '_old_wp_attached_file', $originalFilePath);

		return true;
	}

	/**
	 * Finds a unique filename by appending a numeric suffix if the original filename already exists.
	 *
	 * @param string $directory The directory to check in.
	 * @param string $filename The initial filename to check.
	 * @param string $extension The file extension.
	 * @return string Returns a unique filename with numeric suffix if necessary.
	 */
	private function findUniqueFilename($directory, $filename, $extension)
	{
		$baseFilename = $filename;
		$i = 1;

		// Check if the full filename exists, and append a suffix until it doesn't
		while (file_exists($directory . '/' . $filename . '.' . $extension)) {
			$filename = $baseFilename . '-' . $i;
			$i++;
		}

		return $filename . '.' . $extension;
	}
}
