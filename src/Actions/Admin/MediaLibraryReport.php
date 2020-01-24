<?php

namespace ImageSeoWP\Actions\Admin;

if (!defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Exception\NoRenameFile;

/**
 * @since 1.0.0
 */
class MediaLibraryReport
{
    /**
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->renameFileServices = imageseo_get_service('RenameFile');
        $this->reportImageServices = imageseo_get_service('ReportImage');
        $this->altServices = imageseo_get_service('Alt');
    }

    public function hooks()
    {
        if (!imageseo_allowed()) {
            return;
        }

        add_action('admin_post_imageseo_generate_alt', [$this, 'adminPostGenerateAlt']);
        add_action('admin_post_imageseo_rename_attachment', [$this, 'adminPostRenameAttachment']);
    }

    /**
     * @return int
     */
    protected function getAttachmentId()
    {
        if ('GET' === $_SERVER['REQUEST_METHOD']) {
            return (int) $_GET['attachment_id'];
        } elseif ('POST' === $_SERVER['REQUEST_METHOD']) {
            return(int) $_POST['attachment_id'];
        }
    }

    public function adminPostGenerateAlt()
    {
        $redirectUrl = admin_url('post.php?post=' . $this->getAttachmentId() . '&action=edit');

        if (!wp_verify_nonce($_GET['_wpnonce'], 'imageseo_generate_alt')) {
            wp_redirect($redirectUrl);
            exit;
        }

        if (!current_user_can('manage_options')) {
            wp_redirect($redirectUrl);
            exit;
        }

        $response = $this->altServices->generateForAttachmentId($this->getAttachmentId());
        wp_redirect($redirectUrl);
    }

    public function adminPostRenameAttachment()
    {
        $redirectUrl = admin_url('post.php?post=' . $this->getAttachmentId() . '&action=edit');

        if (!wp_verify_nonce($_GET['_wpnonce'], 'imageseo_rename_attachment')) {
            wp_redirect($redirectUrl);
            exit;
        }

        if (!current_user_can('manage_options')) {
            wp_redirect($redirectUrl);
            exit;
        }

        $attachmentId = $this->getAttachmentId();

        $this->reportImageServices->generateReportByAttachmentId($attachmentId);

        try {
            $filename = $this->renameFileServices->getNameFileWithAttachmentId($attachmentId);
        } catch (NoRenameFile $e) {
            wp_redirect(admin_url('post.php?post=' . $this->getAttachmentId() . '&action=edit'));

            return;
        }

        $this->renameFileServices->updateFilename($attachmentId, $filename);
        wp_redirect($redirectUrl);
    }
}
