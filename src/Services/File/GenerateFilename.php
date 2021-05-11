<?php

namespace ImageSeoWP\Services\File;

if (!defined('ABSPATH')) {
    exit;
}

use Cocur\Slugify\Slugify;
use ImageSeoWP\Exception\NoRenameFile;

class GenerateFilename
{
    public function __construct()
    {
        $this->reportImageService = imageseo_get_service('ReportImage');
    }

    public function getDelimiter()
    {
        return apply_filters('imageseo_rename_delimiter', '-');
    }

    public function generateNameFromReport($attachmentId, $params = [])
    {
        $report = $this->reportImageService->getReportByAttachmentId($attachmentId);

        if (!$report) {
            throw new NoRenameFile('No need to change');

            return;
        }

        $alts = $this->reportImageService->getAltsFromReport($report);
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
    public function getExtensionFilenameByAttachmentId($attachmentId)
    {
        $splitFilename = explode('.', $this->getFilenameByAttachmentId($attachmentId));

        return array_pop($splitFilename);
    }

    /**
     * @param int   $attachmentId
     * @param array $excludeFilenames
     *
     * @return string
     */
    public function generateFilenameByReportForAttachmentId($attachmentId, $excludeFilenames = [])
    {
        try {
            $newFilename = $this->generateNameFromReport($attachmentId);
        } catch (NoRenameFile $e) {
            throw new NoRenameFile('No need to change');
        }

        $filePath = get_attached_file($attachmentId);
        $splitName = explode('.', basename($filePath));

        $generateUniqueFilename = $this->generateUniqueFilename([
            trailingslashit(dirname($filePath)), // Directory
            $splitName[count($splitName) - 1], // Ext
            $this->getDelimiter(), // Delimiter,
            $attachmentId,
            $excludeFilenames,
        ], $newFilename);

        $oldFilename = $splitName[0];

        if ($oldFilename === $newFilename) {
            throw new NoRenameFile('No need to change');
        }

        return $this->validateUniqueFilename($attachmentId, $generateUniqueFilename);
    }

    /**
     * @param int    $attachmentId
     * @param string $filename
     * @param int    $i            counter for unique filename
     *
     * @return string
     */
    public function validateUniqueFilename($attachmentId, $filename, $i = 2)
    {
        $extension = $this->getExtensionFilenameByAttachmentId($attachmentId);

        $directory = trailingslashit(dirname(get_attached_file($attachmentId)));
        $tryNewFilename = sprintf('%s%s.%s', $directory, $filename, $extension);

        if (!file_exists($tryNewFilename)) {
            return $filename;
        }

        $newFilename = sprintf('%s-%s', $filename, $i);

        return $this->validateUniqueFilename($attachmentId, $newFilename, ++$i);
    }

    /**
     * @since 2.0.0
     *
     * @param int   $attachmentId
     * @param array $excludeFilenames
     *
     * @return array
     */
    public function generateFilenameForAttachmentId($attachmentId, $excludeFilenames = [])
    {
        try {
            $filename = $this->generateFilenameByReportForAttachmentId($attachmentId, $excludeFilenames);
        } catch (NoRenameFile $e) {
            $filename = $this->getFilenameByAttachmentId($attachmentId);
        }

        $splitFilename = explode('.', $filename);
        if (1 === count($splitFilename)) { // Need to retrieve current extension
            $currentFilename = wp_get_attachment_image_src($attachmentId, 'full');
            $splitCurrentFilename = explode('.', $currentFilename[0]);
            $extension = $splitCurrentFilename[count($splitCurrentFilename) - 1];
        } else {
            $extension = $splitFilename[count($splitFilename) - 1];
            array_pop($splitFilename);
            $filename = implode('.', $splitFilename);
        }

        $filename = sanitize_title(apply_filters('imageseo_generate_filename', $filename));

        return [
            $filename,
            $extension,
        ];
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
        if (!$excludeFilenames) {
            $excludeFilenames = [];
        }

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
}
