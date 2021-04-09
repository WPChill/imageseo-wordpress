<?php

namespace ImageSeoWP\Services\File;

if (!defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Helpers\ServerSoftware;

class AliasFilename
{
    public function __construct()
    {
        $this->reportImageService = imageseo_get_service('ReportImage');
    }

    /**
     * @param int    $attachmentId
     * @param string $newFilename
     *
     * @return bool
     */
    public function updateFilename($attachmentId, $newFilename)
    {
        $this->deleteOldFilenameImageSeo($attachmentId);

        if (empty($newFilename)) {
            delete_post_meta($attachmentId, '_imageseo_new_filename');

            return;
        }

        delete_option('imageseo_link_rename_files');

        list($filenameWithoutExtension, $extension) = explode('.', $newFilename);

        update_post_meta($attachmentId, '_imageseo_new_filename', $filenameWithoutExtension);

        update_post_meta($attachmentId, sprintf('_imageseo_filename_%s', $filenameWithoutExtension), [
            'size'                        => 'full',
            'extension'                   => $extension,
            'filename_without_extension'  => $filenameWithoutExtension,
            'url'                         => $this->getLinkFileImageSEO($newFilename),
        ]);

        $metadata = wp_get_attachment_metadata($attachmentId);
        if (isset($metadata['sizes'])) {
            foreach ($metadata['sizes'] as $key => $size) {
                if (!isset($size['file'])) {
                    continue;
                }
                $keyFilename = sprintf('%s-%sx%s', $filenameWithoutExtension, $size['width'], $size['height']);
                $filenameSize = sprintf('%s.%s', $keyFilename, $extension);

                update_post_meta($attachmentId, sprintf('_imageseo_filename_%s', $keyFilename), [
                    'size'                        => $key,
                    'extension'                   => $extension,
                    'filename_without_extension'  => $keyFilename,
                    'url'                         => $this->getLinkFileImageSEO($filenameSize),
                ]);
            }
        }
    }

    public function getAllFilenamesByImageSEO($attachmentId)
    {
        global $wpdb;

        $sqlQuery = "SELECT {$wpdb->postmeta}.meta_value
            FROM {$wpdb->postmeta}
            WHERE 1=1
            AND {$wpdb->postmeta}.meta_key LIKE '%_imageseo_filename_%'
            AND {$wpdb->postmeta}.post_id = '$attachmentId'
        ";

        $values = $wpdb->get_results($sqlQuery, ARRAY_N);
        if (empty($values)) {
            return null;
        }

        $values = call_user_func_array('array_merge', $values);
        $values = array_map('unserialize', $values);

        return $values;
    }

    public function getFilenameDataImageSEOWithAttachmentId($attachmentId, $filename)
    {
        return get_post_meta($attachmentId, sprintf('_imageseo_filename_%s', $filename), true);
    }

    public function deleteOldFilenameImageSeo($attachmentId)
    {
        global $wpdb;

        $sqlQuery = "DELETE FROM {$wpdb->postmeta}
            WHERE 1=1
            AND {$wpdb->postmeta}.post_id = '$attachmentId'
            AND {$wpdb->postmeta}.meta_key LIKE '%_imageseo_filename%'
        ";

        $wpdb->query($sqlQuery);
    }

    public function getLinkFileImageSEO($filename)
    {
        $isNginx = apply_filters('imageseo_get_link_file_is_nginx', ServerSoftware::isNginx());

        if ($isNginx) {
            $splitFilename = explode('.', $filename);
            array_pop($splitFilename);
            $filename = implode('.', $splitFilename);
        }

        return site_url(sprintf('/medias/images/%s', str_replace('.webp', '', $filename)));
    }
}
