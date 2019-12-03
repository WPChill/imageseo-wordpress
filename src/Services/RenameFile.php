<?php

namespace ImageSeoWP\Services;

if (!defined('ABSPATH')) {
    exit;
}

use Cocur\Slugify\Slugify;
use ImageSeoWP\Exception\NoRenameFile;
use ImageSeoWP\Helpers\CleanURL;
use ImageSeoWP\Helpers\ServerSoftware;

class RenameFile
{
    /**
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->reportImageServices = imageseo_get_service('ReportImage');
        $this->optionServices = imageseo_get_service('Option');
        $this->htaccessServices = imageseo_get_service('Htaccess');
    }

    protected function getDelimiter()
    {
        return apply_filters('imageseo_rename_delimiter', '-');
    }

    protected function generateNameFromReport($attachmentId, $params = [])
    {
        $value = $this->reportImageServices->getNameFileAttachmentWithId($attachmentId, $params);
        $slugify = new Slugify(['separator' => $this->getDelimiter()]);

        return $slugify->slugify($value);
    }

    /**
     * @since 1.0.0
     *
     * @param int $attachmentId
     *
     * @return string
     */
    public function getNameFileWithAttachmentId($attachmentId)
    {
        $newName = $this->generateNameFromReport($attachmentId);

        $filePath = get_attached_file($attachmentId);
        $splitName = explode('.', basename($filePath));
        $oldName = $splitName[0];

        if ($oldName === $newName) {
            throw new NoRenameFile('No need to change');
        }

        return $this->generateUniqueFilename([
            trailingslashit(dirname($filePath)), // Directory
            $splitName[count($splitName) - 1], // Ext
            $this->getDelimiter(), // Delimiter,
            $attachmentId,
        ], $newName);
    }

    /**
     * @since 1.0.0
     *
     * @param string $name
     *
     * @return string
     */
    public function generateUniqueFilename($data, $name, $counter = 1)
    {
        list($directory, $ext, $delimiter, $attachmentId) = $data;

        $number_try_name = apply_filters('imageseo_number_try_name_file', 7);

        if (!file_exists(sprintf('%s%s.%s', $directory, $name, $ext))) {
            return $name;
        }

        if ($counter < $number_try_name) {
            $name = $this->generateNameFromReport($attachmentId, [
                'max_percent' => 100 - ($counter * 10),
            ]);

            if ($name === get_bloginfo('title')) {
                $name = $this->generateNameFromReport($attachmentId);
            }
        } elseif ($counter >= $number_try_name) {
            $name = $this->generateNameFromReport($attachmentId);
        }

        if (!file_exists(sprintf('%s%s.%s', $directory, $name, $ext))) {
            return $name;
        }

        if ($counter < $number_try_name) {
            return $this->generateUniqueFilename($data, $name, ++$counter);
        } else {
            return $this->generateUniqueFilename($data, sprintf('%s%s%s', $name, $delimiter, ($number_try_name + 2) - $counter), ++$counter);
        }

        return $name;
    }

    /**
     * @since 1.0.0
     *
     * @param int $attachmentId
     *
     * @return bool
     */
    public function renameAttachment($attachmentId, $metadata = null)
    {
        $report = $this->reportImageServices->getReportByAttachmentId($attachmentId);
        if (!$report) {
            $this->reportImageServices->generateReportByAttachmentId($attachmentId);
        }

        $sourceUrl = wp_get_attachment_url($attachmentId);
        $sourceMetadata = wp_get_attachment_metadata($attachmentId);

        $filePath = get_attached_file($attachmentId);

        if (!wp_mkdir_p(dirname($filePath))) {
            return [
                'success' => false,
            ];
        }

        try {
            $newFilename = $this->getNameFileWithAttachmentId($attachmentId);
        } catch (NoRenameFile $e) {
            return [
                'success' => true,
            ];
        }

        if (null === $metadata) {
            $metadata = wp_get_attachment_metadata($attachmentId);
        }
        $post = get_post($attachmentId, ARRAY_A);
        $basename = basename($filePath);
        $splitName = explode('.', $basename);
        unset($splitName[count($splitName) - 1]);
        $basenameWithoutExt = implode('.', $splitName);
        $directory = trailingslashit(dirname($filePath));
        $newFilePath = str_replace($basenameWithoutExt, $newFilename, $filePath);

        // Rename file
        @rename($filePath, $newFilePath);

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


        $targetUrl = wp_get_attachment_url($attachmentId);
        $this->searchReplaceInDB([
            'source_url'      => $sourceUrl,
            'source_metadata' => $sourceMetadata,
        ], [
            'target_url'       => $targetUrl,
            'target_metadata'  => wp_get_attachment_metadata($attachmentId),
        ]);

        $this->updateRedirect($sourceUrl, $targetUrl);

        return [
            'success'  => true,
            'metadata' => $metadata,
            'post'     => $post,
        ];
    }

    /**
     * Update redirection server
     *
     * @param string $sourceUrl
     * @param string $targetUrl
     * @return void
     */
    public function updateRedirect($sourceUrl, $targetUrl){

        $data = get_transient('_imageseo_redirect_images');
        if($data === false){
            $data = [];
        }


        $sourceParse = wp_parse_url($sourceUrl);
        if(!array_key_exists('path', $sourceParse)){
            return;
        }

        $data[$sourceParse['path']] = ['target' => $targetUrl, 'date_add' => time()];
        set_transient('_imageseo_redirect_images', $data, WEEK_IN_SECONDS * 2);


        if(ServerSoftware::isApache() && $this->htaccessServices->isWritable()){
            $content = $this->htaccessServices->generate();
            $this->htaccessServices->save($content);
        }
    }


    /**
     * Build an array of search or replace URLs for given attachment GUID and its metadata.
     *
     * @param string $guid
     * @param array  $metadata
     *
     * @return array
     */
    public function getFileUrls($guid, $metadata)
    {
        $urls = [];

        $guid = CleanURL::removeScheme($guid);
        $guid = CleanURL::removeDomainFromFilename($guid);

        $urls['guid'] = $guid;

        if (empty($metadata)) {
            return $urls;
        }

        $base_url = dirname($guid);

        if (!empty($metadata['file'])) {
            $urls['file'] = trailingslashit($base_url) . wp_basename($metadata['file']);
        }

        if (!empty($metadata['sizes'])) {
            foreach ($metadata['sizes'] as $key => $value) {
                $urls[$key] = trailingslashit($base_url) . wp_basename($value['file']);
            }
        }

        return $urls;
    }

    /**
     * Ensure new search URLs cover known sizes for old attachment.
     * Falls back to full URL if size not covered (srcset or width/height attributes should compensate).
     *
     * @param array $old
     * @param array $new
     *
     * @return array
     */
    public function normalizeFileUrls($old, $new)
    {
        $result = [];

        if (empty($new['guid'])) {
            return $result;
        }

        $guid = $new['guid'];

        foreach ($old as $key => $value) {
            $result[$key] = empty($new[$key]) ? $guid : $new[$key];
        }

        return $result;
    }

    public function searchReplaceInDB($source, $target)
    {
        global $wpdb;

        $sourceUrl = $source['source_url'];
        $sourceMetadata = $source['source_metadata'];

        $targetUrl = $target['target_url'];
        $targetMetadata = $target['target_metadata'];

        // Search-and-replace filename in post database
        $currentBaseUrl = CleanURL::getMatchUrl($sourceUrl);

        /* Fail-safe if base_url is a whole directory, don't go search/replace */
        if (is_dir($currentBaseUrl)) {
            //Fail Safe :: Source Location seems to be a directory
            return;
        }

        /* Search and replace in WP_POSTS */
        // Removed $wpdb->remove_placeholder_escape from here, not compatible with WP 4.8
        $postsSQL = $wpdb->prepare(
            "SELECT ID, post_content 
            FROM $wpdb->posts 
            WHERE post_status = 'publish' 
            AND post_content LIKE %s;",
            '%' . $currentBaseUrl . '%');

        $postmetaSQL = 'SELECT meta_id, post_id, meta_value 
            FROM ' . $wpdb->postmeta . '
            WHERE post_id in (SELECT ID from ' . $wpdb->posts . ' where post_status = "publish") 
            AND meta_value like %s  ';

        $postmetaSQL = $wpdb->prepare($postmetaSQL, '%' . $currentBaseUrl . '%');

        $rsmeta = $wpdb->get_results($postmetaSQL, ARRAY_A);
        $rs = $wpdb->get_results($postsSQL, ARRAY_A);

        $numberOfUpdates = 0;

        $search_urls = $this->getFileUrls($sourceUrl, $sourceMetadata);
        $replace_urls = $this->getFileUrls($targetUrl, $targetMetadata);
        $replace_urls = array_values($this->normalizeFileUrls($search_urls, $replace_urls));

        if (!empty($rs)) {
            foreach ($rs as $rows) {
                $numberOfUpdates = $numberOfUpdates + 1;
                // replace old URLs with new URLs.
                $post_content = $rows['post_content'];
                $post_content = str_replace($search_urls, $replace_urls, $post_content);

                $sql = $wpdb->prepare(
                    "UPDATE $wpdb->posts SET post_content = %s WHERE ID = %d;",
                    [$post_content, $rows['ID']]
                );

                $wpdb->query($sql);
            }
        }
        if (!empty($rsmeta)) {
            foreach ($rsmeta as $row) {
                ++$numberOfUpdates;
                $content = $row['meta_value'];
                $content = str_replace($search_urls, $replace_urls, $content);

                $sql = $wpdb->prepare('UPDATE ' . $wpdb->postmeta . ' SET meta_value = %s WHERE meta_id = %d', $content, $row['meta_id']);
                $wpdb->query($sql);
            }
        }
    }
}
