<?php

namespace ImageSeoWP\Services;

if (!defined('ABSPATH')) {
    exit;
}

use Cocur\Slugify\Slugify;
use ImageSeoWP\Exception\NoRenameFile;

class RenameFile
{
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
        $report = $this->reportImageServices->getReportByAttachmentId($attachmentId);

        if (!$report) {
            throw new NoRenameFile('No need to change');
        }

        $alts = $this->reportImageServices->getAltsFromReport($report);
        $key = isset($params['key']) ? $params['key'] : 0;

        $value = '';
        if (isset($alts[$key])) {
            $value = $alts[$key]['name'];
        }

        $slugify = new Slugify(['separator' => $this->getDelimiter()]);

        return $slugify->slugify($value);
    }

    /**
     * @param int $attachmentId
     *
     * @return string
     */
    public function getFilenameByAttachmentId($attachmentId)
    {
        $file = wp_get_attachment_image_src($attachmentId, 'small');

        if (!$file) {
            $file = wp_get_attachment_image_src($attachmentId);
        }

        if (!$file) {
            return '';
        }

        $srcFile = $file[0];

        return basename($srcFile);
    }

    /**
     * @param int $attachmentId
     *
     * @return string
     */
    public function getNameFileWithAttachmentId($attachmentId, $excludeFilenames = [])
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
            $excludeFilenames,
        ], $newName);
    }

    /**
     * @param array  $data    (directory|extension|delimiter|attachmentId|excludeFilenames)
     * @param string $name
     * @param int    $counter
     *
     * @return string
     */
    public function generateUniqueFilename($data, $name, $counter = 1)
    {
        list($directory, $ext, $delimiter, $attachmentId, $excludeFilenames) = $data;

        $numberTryName = apply_filters('imageseo_number_try_name_file', 7);

        if (!file_exists(sprintf('%s%s.%s', $directory, $name, $ext)) && !in_array($name, $excludeFilenames, true)) {
            return $name;
        }

        if ($counter < $numberTryName) {
            $name = $this->generateNameFromReport($attachmentId, [
                'key' => $counter,
            ]);
        } elseif ($counter >= $numberTryName) {
            $name = $this->generateNameFromReport($attachmentId);
            $name = sprintf('%s%s%s', get_bloginfo('title'), $delimiter, $name);
        }

        if (!file_exists(sprintf('%s%s.%s', $directory, $name, $ext)) && !in_array($name, $excludeFilenames, true)) {
            return $name;
        }

        if ($counter < $numberTryName) {
            return $this->generateUniqueFilename($data, $name, ++$counter);
        } else {
            return $this->generateUniqueFilename($data, sprintf('%s%s%s', $name, $delimiter, ($numberTryName + 2) - $counter), ++$counter);
        }

        return $name;
    }

    /**
     * @param int    $attachmentId
     * @param string $newFilename
     *
     * @return bool
     */
    public function updateFilename($attachmentId, $newFilename)
    {
        $urls = [$newFilename => [
            'size'     => 'full',
            'url'      => site_url(sprintf('/medias/images/%s', $newFilename)),
        ]];

        list($filenameWithoutExtension, $extension) = explode('.', $newFilename);

        $metadata = wp_get_attachment_metadata($attachmentId);
        if (isset($metadata['sizes'])) {
            foreach ($metadata['sizes'] as $key => $size) {
                if (!isset($size['file'])) {
                    continue;
                }
                $filenameSize = sprintf('%s-%sx%s.%s', $filenameWithoutExtension, $size['width'], $size['height'], $extension);
                $urls[$filenameSize] = [
                    'size'     => $key,
                    'url'      => site_url(sprintf('/medias/images/%s', $filenameSize)),
                ];
            }
        }

        update_post_meta($attachmentId, '_imageseo_new_filename', $urls);
    }

    public function getFilenameByImageSEOWithAttachmentId($attachmentId)
    {
        return get_post_meta($attachmentId, '_imageseo_new_filename', true);
    }

    public function getAttachmentIdByFilenameImageSeo($filename)
    {
        global $wpdb;

        $sqlQuery = "SELECT {$wpdb->posts}.*
            FROM {$wpdb->posts} 
            INNER JOIN {$wpdb->postmeta} ON ( {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id AND  {$wpdb->postmeta}.meta_key = '_imageseo_new_filename' ) 
            WHERE 1=1 
            AND {$wpdb->postmeta}.meta_value LIKE '%{$filename}%' 
            LIMIT 1
        ";

        $posts = $wpdb->get_results($sqlQuery);
        if (empty($posts)) {
            return null;
        }

        return $posts[0];
    }
}
