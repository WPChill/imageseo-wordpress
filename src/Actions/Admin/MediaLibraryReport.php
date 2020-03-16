<?php

namespace ImageSeoWP\Actions\Admin;

if (!defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Exception\NoRenameFile;

class MediaLibraryReport
{
    public function __construct()
    {
        $this->renameFileService = imageseo_get_service('RenameFile');
        $this->reportImageService = imageseo_get_service('ReportImage');
        $this->altService = imageseo_get_service('Alt');
    }

    public function hooks()
    {
        if (!imageseo_allowed()) {
            return;
        }
        add_action('add_meta_boxes', [$this, 'renameMetabox']);
        add_action('admin_post_imageseo_generate_alt', [$this, 'adminPostGenerateAlt']);
        add_action('admin_post_imageseo_rename_attachment', [$this, 'adminPostRenameAttachment']);
    }

    public function renameMetabox()
    {
        add_meta_box('imageseo_filename', 'Filename by ImageSEO', [$this, 'attachmentFields'], 'attachment', 'side', 'high');
    }

    public function attachmentFields($post)
    {
        $filenameByImageSEO = $this->renameFileService->getFilenameByImageSEOWithAttachmentId($post->ID);

        $filename = '';
        if (!empty($filenameByImageSEO)) {
            foreach ($filenameByImageSEO as $key => $value) {
                if ('full' !== $value['size']) {
                    continue;
                }

                $filename = $key;
            }
        } ?>
    		<input type="text" readonly class="widefat"  value="<?php echo $filename; ?>" />
		    <a target="_blank" href="<?php echo $this->renameFileService->getLinkFileImageSEO($filename); ?>"><?php _e('View image', 'imageseo'); ?></a>
        <?php
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

        $response = $this->altService->generateForAttachmentId($this->getAttachmentId());
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

        $this->reportImageService->generateReportByAttachmentId($attachmentId);

        try {
            $filename = $this->renameFileService->getNameFileWithAttachmentId($attachmentId);
        } catch (NoRenameFile $e) {
            wp_redirect($redirectUrl);

            return;
        }

        $extension = $this->renameFileService->getExtensionFilenameByAttachmentId($attachmentId);

        $this->renameFileService->updateFilename($attachmentId, sprintf('%s.%s', $filename, $extension));
        wp_redirect($redirectUrl);
    }
}
