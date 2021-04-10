<?php

namespace ImageSeoWP\Actions\Admin;

if (!defined('ABSPATH')) {
    exit;
}

class BackupFilename
{
    public function hooks()
    {
        add_action('admin_post_imageseo_backup_rename_file', [$this, 'process']);
    }

    /**
     * @return int
     */
    protected function getAttachmentId()
    {
        if ('GET' === $_SERVER['REQUEST_METHOD']) {
            return (int) $_GET['attachment_id'];
        } elseif ('POST' === $_SERVER['REQUEST_METHOD']) {
            return (int) $_POST['attachment_id'];
        }
    }

    /**
     * @since 2.0
     *
     * @return void
     */
    public function process()
    {
        $redirectUrl = admin_url('post.php?post=' . $this->getAttachmentId() . '&action=edit');

        if (!wp_verify_nonce($_GET['_wpnonce'], 'imageseo_backup_rename_file')) {
            wp_redirect($redirectUrl);
            exit;
        }

        if (!current_user_can('manage_options')) {
            wp_redirect($redirectUrl);
            exit;
        }

        $attachmentId = $this->getAttachmentId();

        $oldMetadata = get_post_meta($attachmentId, '_old_wp_attachment_metadata', true);
        $filename = '';
        if (isset($oldMetadata['original_image'])) {
            $filename = $oldMetadata['original_image'];
        } else {
            $oldFilename = explode('/', $oldMetadata['file']);
            $filename = $oldFilename[count($oldFilename) - 1];
        }

        if (!empty($filename)) {
            imageseo_get_service('UpdateFile')->updateFilename($attachmentId, $filename);
        }

        wp_redirect($redirectUrl);
    }
}
