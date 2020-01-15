<?php

namespace ImageSeoWP\Services;

if (!defined('ABSPATH')) {
    exit;
}

class UploadImage
{
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

        return wp_insert_attachment($attachment, $upload_dir['path'] . '/' . $filename);
    }
}
