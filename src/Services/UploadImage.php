<?php

namespace ImageSeoWP\Services;

if (!defined('ABSPATH')) {
    exit;
}

class UploadImage
{
    public function saveImageFromUrl($url, $title)
    {
        $file = [];
        $file['name'] = $title . '.jpg';
        $file['tmp_name'] = download_url($url);

        if (is_wp_error($file['tmp_name'])) {
            @unlink($file['tmp_name']);

            return;
        }

        $attachmentId = media_handle_sideload($file, $post_id);

        if (is_wp_error($attachmentId)) {
            @unlink($file['tmp_name']);
        } else {
            $image = wp_get_attachment_url($attachmentId);
        }
    }

    /**
     * Save the image on the server.
     */
    public function saveFromBase64($base64, $title)
    {
        // Upload dir.
        $upload_dir = wp_upload_dir();
        $upload_path = str_replace('/', DIRECTORY_SEPARATOR, $upload_dir['path']) . DIRECTORY_SEPARATOR;

        $extension = 'jpeg';
        if (false !== strpos($base64, 'image/jpeg')) {
            $img = str_replace('data:image/jpeg;base64,', '', $base64);
        } elseif (false !== strpos($base64, 'image/png')) {
            $img = str_replace('data:image/png;base64,', '', $base64);
            $extension = 'png';
        }
        $img = str_replace(' ', '+', $img);
        $decoded = base64_decode($img);

        $filename = $title . '.' . $extension;
        $file_type = 'image/' . $extension;

        // Save the image in the uploads directory.
        $upload_file = file_put_contents($upload_path . $filename, $decoded);

        $attachment = [
            'post_mime_type' => $file_type,
            'post_title'     => preg_replace('/\.[^.]+$/', '', basename($filename)),
            'post_content'   => '',
            'post_status'    => 'inherit',
            'guid'           => $upload_dir['url'] . '/' . basename($filename),
        ];

        $pathFilename = $upload_dir['path'] . '/' . $filename;

        $attachId = wp_insert_attachment($attachment, $pathFilename);

        require_once ABSPATH . 'wp-admin/includes/image.php';

        $attach_data = wp_generate_attachment_metadata($attachId, $pathFilename);

        wp_update_attachment_metadata($attachId, $attach_data);

        return true;
    }
}
