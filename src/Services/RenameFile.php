<?php

namespace ImageSeoWP\Services;

if (! defined('ABSPATH')) {
    exit;
}

use Cocur\Slugify\Slugify;

class RenameFile
{
    public function __construct()
    {
        $this->reportImageServices = imageseo_get_service('ReportImage');
        $this->optionServices = imageseo_get_service('Option');
    }

    public function getNameFileWithAttachmentId($attachmentId)
    {
        $value = $this->reportImageServices->getNameFileAttachmentWithId($attachmentId);
        $delimiter = $this->optionServices->getOption('rename_delimiter');
        $slugify = new Slugify(['separator' => $delimiter]);
        return $slugify->slugify($value);
    }

    public function renameAttachment($attachmentId)
    {
        $filePath = get_attached_file($attachmentId);
        if (!wp_mkdir_p(dirname($filePath))) {
            return false;
        }

        $newFilename = $this->getNameFileWithAttachmentId($attachmentId);

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
