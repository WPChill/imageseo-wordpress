<?php

namespace ImageSeoWP\Actions\Admin;

if (!defined('ABSPATH')) {
    exit;
}

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
        $this->generateAltServices = imageseo_get_service('Alt');
    }

    public function hooks()
    {
        if (!imageseo_allowed()) {
            return;
        }

        add_action('admin_post_imageseo_report_attachment', [$this, 'adminPostGenerateAlt']);
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
        $response = $this->generateAltServices->generateForAttachmentId($this->getAttachmentId());
        wp_redirect(admin_url('post.php?post=' . $this->getAttachmentId() . '&action=edit'));
    }

    public function adminPostRenameAttachment()
    {
        $this->renameFileServices->renameAttachment($this->getAttachmentId());
        wp_redirect(admin_url('post.php?post=' . $this->getAttachmentId() . '&action=edit'));
    }
}
