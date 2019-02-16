<?php

namespace ImageSeoWP\Services;

if (! defined('ABSPATH')) {
    exit;
}

use Cocur\Slugify\Slugify;
use ImageSeoWP\Exception\NoRenameFile;

class RenameFile
{
    /**
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->reportImageServices = imageseo_get_service('ReportImage');
        $this->optionServices = imageseo_get_service('Option');
    }

    /**
     * @since 1.0.0
     *
     * @param integer $attachmentId
     * @return string
     */
    public function getNameFileWithAttachmentId($attachmentId)
    {
        $value = $this->reportImageServices->getNameFileAttachmentWithId($attachmentId);
        $delimiter = apply_filters('imageseo_rename_delimiter', '-');
        $slugify = new Slugify(['separator' => $delimiter]);
        $newName = $slugify->slugify($value);

        $filePath = get_attached_file($attachmentId);
        $splitName = explode('.', basename($filePath));
        $oldName = $splitName[0];

        if ($oldName === $newName) {
            throw new NoRenameFile("No need to change");
        }

        return $this->generateUniqueFilename([
            trailingslashit(dirname($filePath)), // Directory
            $splitName[1], // Ext
            $delimiter // Delimiter
        ], $slugify->slugify($value));
    }


    /**
     * @since 1.0.0
     *
     * @param string $name
     * @return string
     */
    public function generateUniqueFilename($data, $name, $counter = 1)
    {
        list($directory, $ext, $delimiter) = $data;

        if (file_exists(sprintf('%s%s.%s', $directory, $name, $ext))) {
            return $this->generateUniqueFilename($data, sprintf('%s%s%s', $name, $delimiter, $counter), ++$counter);
        }

        return $name;
    }

    /**
     * @since 1.0.0
     *
     * @param integer $attachmentId
     * @return bool
     */
    public function renameAttachment($attachmentId)
    {
        $report = $this->reportImageServices->getReportByAttachmentId($attachmentId);
        if (!$report) {
            $this->reportImageServices->generateReportByAttachmentId($attachmentId);
        }

        $filePath = get_attached_file($attachmentId);

        if (!wp_mkdir_p(dirname($filePath))) {
            return false;
        }

        try {
            $newFilename = $this->getNameFileWithAttachmentId($attachmentId);
        } catch (NoRenameFile $e) {
            return true;
        }

        $metadata = wp_get_attachment_metadata($attachmentId);
        $post = get_post($attachmentId, ARRAY_A);
        $basename = basename($filePath);
        $basenameWithoutExt = explode('.', $basename)[0];
        $directory = trailingslashit(dirname($filePath));

        $newFilePath = str_replace($basenameWithoutExt, $newFilename, $filePath);

        // Rename file
        rename($filePath, $newFilePath);

        // Prepare post
        $post['post_title'] = $newFilename;
        $post['post_name'] = $newFilename;

        // Prepare metdata
        if (isset($metadata['file']) && !empty($metadata['file'])) {
            $metadata['file'] = str_replace($basenameWithoutExt, $newFilename, $metadata['file']);
        }
        if (isset($metadata['url']) && !empty($metadata['url']) && strlen($metadata['url']) > 4) {
            $metadata['url'] = str_replace($basenameWithoutExt, $newFilename, $metadata['url']);
        }

        if (isset($metadata['sizes'])) {
            foreach ($metadata['sizes'] as $key => $size) {
                if (!isset($size['file'])) {
                    continue;
                }

                $oldFile = $metadata['sizes'][$key]['file'];
                $metadata['sizes'][$key]['file'] = str_replace($basenameWithoutExt, $newFilename, $metadata['sizes'][$key]['file']);

                rename($directory . $oldFile, $directory . $metadata['sizes'][$key]['file']);
            }
        }

        $oldAttachedFile = get_post_meta($attachmentId, '_wp_attached_file', true);

        update_attached_file($attachmentId, str_replace($basenameWithoutExt, $newFilename, $oldAttachedFile));
        wp_update_attachment_metadata($attachmentId, $metadata);
        clean_post_cache($attachmentId);
        wp_update_post($post);

        // Update GUID
        $newGuid = str_replace($basenameWithoutExt, $newFilename, $post['guid']);

        global $wpdb;
        $query = $wpdb->prepare("UPDATE $wpdb->posts SET guid = '%s' WHERE ID = '%d'", $newGuid, $attachmentId);
        $wpdb->query($query);
        clean_post_cache($attachmentId);

        return true;
    }
}
