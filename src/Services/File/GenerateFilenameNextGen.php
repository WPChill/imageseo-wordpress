<?php

namespace ImageSeoWP\Services\File;

if (!defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Exception\NoRenameFile;

class GenerateFilenameNextGen
{
    public function __construct()
    {
        $this->generateFilenameDefault = imageseo_get_service('GenerateFilename');
        $this->queryNextGen = imageseo_get_service('QueryNextGen');
    }

    /**
     * @param int $attachmentId
     *
     * @return string
     */
    public function getFilenameByAttachmentId($attachmentId)
    {
        return $this->queryNextGen->getFilename($attachmentId);
    }

    /**
     * @param int   $attachmentId
     * @param array $excludeFilenames
     *
     * @return string
     */
    public function generateFilenameByReportForAttachmentId($attachmentId, $excludeFilenames = [])
    {
        $postId = $this->queryNextGen->getPostIdByNextGenId($attachmentId);

        try {
            $newFilename = $this->generateFilenameDefault->generateNameFromReport($postId);
        } catch (NoRenameFile $e) {
            throw new NoRenameFile('No need to change');
        }

        $filePath = $this->queryNextGen->getFilepath($attachmentId);

        $splitName = explode('.', basename($filePath));
        $oldFilename = $splitName[0];

        $generateUniqueFilename = $this->generateFilenameDefault->generateUniqueFilename([
            trailingslashit(dirname($filePath)), // Directory
            $splitName[count($splitName) - 1], // Ext
            $this->generateFilenameDefault->getDelimiter(), // Delimiter,
            $postId,
            $excludeFilenames,
        ], $newFilename);

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
        $extension = $this->generateFilenameDefault->getExtensionFilenameByAttachmentId($attachmentId);

        $directory = trailingslashit(dirname($this->queryNextGen->getFilepath($attachmentId)));
        $tryNewFilename = sprintf('%s%s.%s', $directory, $filename, $extension);

        if (!file_exists($tryNewFilename)) {
            return $filename;
        }

        $newFilename = sprintf('%s-%s', $filename, $i);

        return $this->validateUniqueFilename($attachmentId, $newFilename, ++$i);
    }

    /**
     * @param int   $attachmentId
     * @param array $excludeFilenames
     *
     * @return array
     */
    public function generateFilenameForAttachmentId($attachmentId, $excludeFilenames = [])
    {
        $baseFilename = $this->getFilenameByAttachmentId($attachmentId);
        $splitFilename = explode('.', $baseFilename);
        $extension = $splitFilename[count($splitFilename) - 1];

        try {
            $filename = $this->generateFilenameByReportForAttachmentId($attachmentId, $excludeFilenames);
        } catch (NoRenameFile $e) {
            array_pop($splitFilename);
            $filename = implode('.', $splitFilename);
        }

        $filename = sanitize_title(apply_filters('imageseo_generate_nextgen_filename', $filename));

        return [
            $filename,
            $extension,
        ];
    }
}
