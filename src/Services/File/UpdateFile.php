<?php

namespace ImageSeoWP\Services\File;

if (!defined('ABSPATH')) {
    exit;
}

class UpdateFile
{
    /**
     * @param int    $attachmentId
     * @param string $newFilename
     *
     * @return bool
     */
    public function updateFilename($attachmentId, $newFilename)
    {
        list($filenameWithoutExtension, $extension) = explode('.', $newFilename);
        $metadata = wp_get_attachment_metadata($attachmentId);

        $uploadDirectoryData = wp_get_upload_dir();

        $fileRootDirectories = explode('/', $metadata['file']);
        $oldFilename = $basicOldFileName = $fileRootDirectories[count($fileRootDirectories) - 1];
        $fileRoot = str_replace($oldFilename, '', $metadata['file']);

        if (isset($metadata['original_image'])) {
            $oldFilename = $metadata['original_image'];
        }

        $src = sprintf('%s/%s%s', $uploadDirectoryData['basedir'], $fileRoot, $basicOldFileName);

        if (!file_exists($src)) {
            return;
        }

        try {
            $newMetadata = $metadata;

            list($oldFilenameWithoutExtension, $oldExtension) = explode('.', $oldFilename);

            // Basic file
            $newBasicFile = \str_replace($oldFilenameWithoutExtension, $filenameWithoutExtension, $basicOldFileName);
            $destination = \str_replace($oldFilenameWithoutExtension, $filenameWithoutExtension, $src);

            rename($src, $destination);

            $oldUrl = wp_get_attachment_image_src($attachmentId, 'full')[0];
            $newUrl = \str_replace($oldFilenameWithoutExtension, $filenameWithoutExtension, $oldUrl);

            $this->action_update_postmeta($oldUrl, $newUrl);
            $this->action_update_posts($oldUrl, $newUrl);

            $newMetadata['file'] = \sprintf('%s%s', $fileRoot, $newBasicFile);

            // Multiple Sizes
            foreach ($metadata['sizes'] as $key => $size) {
                $srcBySize = sprintf('%s/%s%s', $uploadDirectoryData['basedir'], $fileRoot, $size['file']);
                if (!file_exists($srcBySize)) {
                    continue;
                }

                $newFileBySize = str_replace($oldFilenameWithoutExtension, $filenameWithoutExtension, $size['file']);

                $destinationBySize = \str_replace($oldFilenameWithoutExtension, $filenameWithoutExtension, $srcBySize);

                $newMetadata['sizes'][$key]['file'] = $newFileBySize;
                rename($srcBySize, $destinationBySize);

                $oldUrl = wp_get_attachment_image_src($attachmentId, $key)[0];
                $newUrl = \str_replace($oldFilenameWithoutExtension, $filenameWithoutExtension, $oldUrl);

                $this->action_update_postmeta($oldUrl, $newUrl);
                $this->action_update_posts($oldUrl, $newUrl);
            }

            // Original Image
            if (isset($metadata['original_image'])) {
                $srcOriginal = sprintf('%s/%s%s', $uploadDirectoryData['basedir'], $fileRoot, $metadata['original_image']);
                if (\file_exists($srcOriginal)) {
                    $newFileOriginal = str_replace($oldFilenameWithoutExtension, $filenameWithoutExtension, $metadata['original_image']);
                    $srcOriginal = sprintf('%s/%s%s', $uploadDirectoryData['basedir'], $fileRoot, $metadata['original_image']);
                    $destinationOriginal = \str_replace($oldFilenameWithoutExtension, $filenameWithoutExtension, $srcOriginal);

                    rename($srcOriginal, $destinationOriginal);

                    $newMetadata['original_image'] = $newFileOriginal;

                    $oldUrl = wp_get_original_image_url($attachmentId);
                    $newUrl = \str_replace($oldFilenameWithoutExtension, $filenameWithoutExtension, $oldUrl);

                    $this->action_update_postmeta($oldUrl, $newUrl);
                    $this->action_update_posts($oldUrl, $newUrl);
                }
            }
        } catch (\Exception $th) {
        }

        wp_update_attachment_metadata($attachmentId, $newMetadata);
        update_attached_file($attachmentId, $newMetadata['file']);
        wp_update_post([
            'ID'         => $attachmentId,
            'post_title' => $filenameWithoutExtension,
            'post_name'  => sanitize_title($filenameWithoutExtension),
        ]);

        update_post_meta($attachmentId, '_old_wp_attachment_metadata', $metadata);
        update_post_meta($attachmentId, '_old_wp_attached_file', $metadata['file']);

        exit;
    }

    // Mass update of all the meta with the new filenames
    public function action_update_postmeta($orig_image_url, $new_image_url)
    {
        global $wpdb;
        $query = $wpdb->prepare("UPDATE $wpdb->postmeta
			SET meta_value = '%s'
			WHERE meta_key <> '_original_filename'
			AND (TRIM(meta_value) = '%s'
			OR TRIM(meta_value) = '%s'
		);", $new_image_url, $orig_image_url, str_replace(' ', '%20', $orig_image_url));
        $query_revert = $wpdb->prepare("UPDATE $wpdb->postmeta
			SET meta_value = '%s'
			WHERE meta_key <> '_original_filename'
			AND meta_value = '%s';
		", $orig_image_url, $new_image_url);
        $wpdb->query($query);
    }

    // Mass update of all the articles with the new filenames
    public function action_update_posts($orig_image_url, $new_image_url)
    {
        global $wpdb;

        // Content
        $query = $wpdb->prepare("UPDATE $wpdb->posts
			SET post_content = REPLACE(post_content, '%s', '%s')
			WHERE post_status != 'inherit'
			AND post_status != 'trash'
			AND post_type != 'attachment'
			AND post_type NOT LIKE '%acf-%'
			AND post_type NOT LIKE '%edd_%'
			AND post_type != 'shop_order'
			AND post_type != 'shop_order_refund'
			AND post_type != 'nav_menu_item'
			AND post_type != 'revision'
			AND post_type != 'auto-draft'", $orig_image_url, $new_image_url);
        $query_revert = $wpdb->prepare("UPDATE $wpdb->posts
			SET post_content = REPLACE(post_content, '%s', '%s')
			WHERE post_status != 'inherit'
			AND post_status != 'trash'
			AND post_type != 'attachment'
			AND post_type NOT LIKE '%acf-%'
			AND post_type NOT LIKE '%edd_%'
			AND post_type != 'shop_order'
			AND post_type != 'shop_order_refund'
			AND post_type != 'nav_menu_item'
			AND post_type != 'revision'
			AND post_type != 'auto-draft'", $new_image_url, $orig_image_url);
        $wpdb->query($query);

        // Excerpt
        $query = $wpdb->prepare("UPDATE $wpdb->posts
			SET post_excerpt = REPLACE(post_excerpt, '%s', '%s')
			WHERE post_status != 'inherit'
			AND post_status != 'trash'
			AND post_type != 'attachment'
			AND post_type NOT LIKE '%acf-%'
			AND post_type NOT LIKE '%edd_%'
			AND post_type != 'shop_order'
			AND post_type != 'shop_order_refund'
			AND post_type != 'nav_menu_item'
			AND post_type != 'revision'
			AND post_type != 'auto-draft'", $orig_image_url, $new_image_url);
        $query_revert = $wpdb->prepare("UPDATE $wpdb->posts
			SET post_excerpt = REPLACE(post_excerpt, '%s', '%s')
			WHERE post_status != 'inherit'
			AND post_status != 'trash'
			AND post_type != 'attachment'
			AND post_type NOT LIKE '%acf-%'
			AND post_type NOT LIKE '%edd_%'
			AND post_type != 'shop_order'
			AND post_type != 'shop_order_refund'
			AND post_type != 'nav_menu_item'
			AND post_type != 'revision'
			AND post_type != 'auto-draft'", $new_image_url, $orig_image_url);
        $wpdb->query($query);
    }
}
