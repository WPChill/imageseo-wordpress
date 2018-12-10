<?php

namespace SeoImageWP\Actions\Admin;

if (! defined('ABSPATH')) {
    exit;
}

/**
 *
 * @since 1.0.0
 */
class MediaLibrary
{

    /**
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->reportImageServices   = seoimage_get_service('ReportImageSeoImage');
    }

    /**
     * @return void
     */
    public function hooks()
    {
        add_filter('manage_media_columns', [$this, 'manageMediaColumns']);
        add_filter('attachment_fields_to_edit', [$this, 'fieldsEdit'], 999, 2);
        add_action('manage_media_custom_column', [$this, 'manageMediaCustomColumn'], 10, 2);

        add_action('admin_post_seoimage_report_attachment', [$this, 'ajaxReportAttachment']);
        add_action('wp_ajax_seoimage_report_attachment', [$this, 'ajaxReportAttachment']);

        add_filter('image_send_to_editor', [$this, 'sendToEditor'], 10, 2);
        add_action('wp_ajax_seoimage_media_alt_update', [$this, 'ajaxAltUpdate']);
        add_action('wp_ajax_nopriv_seoimage_media_alt_update', [$this, 'ajaxAltUpdate']);

        add_action('admin_init', [$this, 'metaboxReport']);
    }

    public function ajaxAltUpdate()
    {
        $postId = absint($_POST['post_id']);
        $alt = wp_strip_all_tags($_POST['alt']);

        if (!empty($_POST['alt'])) {
            update_post_meta($postId, '_wp_attachment_image_alt', $alt);
        }
    }


    public function fieldsEdit($formFields, $post)
    {
        global $pagenow;

        if ('post.php' === $pagenow) {
            return $formFields;
        }


        $formFields['seoimage-has-report'] = array(
            'label'         => __('SeoImage Report'),
            'input'         => 'html',
            'html'          => '<a id="seoimage-' . $post->ID . '" href="' . esc_url(admin_url('post.php?post=' . $post->ID . '&action=edit')) . '" class="button">' . __('View report', 'seoimage') . '</a>',
            'show_in_edit'  => true,
            'show_in_modal' => true,
        );

        return $formFields;
    }

    public function metaboxReport()
    {
        add_meta_box(
            'seoimage-report',
            __('Report SeoImage', 'seoimage'),
            [$this, 'viewMetaboxReport'],
            'attachment',
            'normal'
        );
    }

    public function viewMetaboxReport($post)
    {
        include_once SEOIMAGE_TEMPLATES_ADMIN_METABOXES . '/report.php';
    }

    /**
     * Activate array
     *
     * @return void
     */
    public function manageMediaColumns($columns)
    {
        $columns['seoimage'] = __('SeoImage', 'seoimage');

        return $columns;
    }



    /**
     *
     * @since  1.0
     * @param string $columnName   Name of the custom column.
     * @param int    $attachment_id Attachment ID.
     */
    public function manageMediaCustomColumn($columnName, $attachmentId)
    {
        if ($columnName !== 'seoimage') {
            return;
        }

        $haveAlreadyReport = $this->reportImageServices->haveAlreadyReportByAttachmentId($attachmentId); ?>
        <div class="media-column-seoimage">
            <?php
            if ($haveAlreadyReport) {
                ?>
                <p><?php esc_html_e('The media file already has a report', 'seoimage'); ?></p>
                <div class="media-column-seoimage--actions">
                    <a id="seoimage-<?php echo $attachmentId; ?>" href="<?php echo esc_url(get_edit_post_link($attachmentId)); ?>" class="button">
                        <?php echo __('View report', 'seoimage'); ?>
                    </a>
                    <a id="seoimage-<?php echo $attachmentId; ?>" href="<?php echo esc_url(admin_url('admin-post.php?action=seoimage_report_attachment&attachment_id=' . $attachmentId)); ?>" class="button">
                        <?php echo __('Re-Analyze', 'seoimage'); ?>
                    </a>
                </div>
                <?php
            } else {
                ?>
                <div class="media-column-seoimage--actions">
                    <a id="seoimage-<?php echo $attachmentId; ?>" href="<?php echo esc_url(admin_url('admin-post.php?action=seoimage_report_attachment&attachment_id=' . $attachmentId)); ?>" class="button-primary">
                        <?php echo __('Analyze', 'seoimage'); ?>
                    </a>
                </div>
            <?php
            } ?>
            <div id="wrapper-<?php echo $attachmentId; ?>">
                <input type="text" name="seoimage-alt" data-id="<?php echo $attachmentId; ?>" class="seoimage-alt-ajax large-text" id="seoimage-alt-<?php echo $attachmentId; ?>" value="<?php echo wp_strip_all_tags(__(get_post_meta($attachmentId, '_wp_attachment_image_alt', true))); ?>" />
            </div>
        </div>
        <?php
    }

    public function ajaxReportAttachment()
    {
        if (!isset($_GET['attachment_id'])) {
            return;
        }

        $attachmentId = (int) $_GET['attachment_id'];

        $result = $this->reportImageServices->generateReportByAttachmentId($attachmentId);

        if ($result['success']) {
            wp_redirect(get_edit_post_link($attachmentId));
            exit;
        }

        wp_redirect(admin_url('upload.php'));
        exit;
    }

    public function sendToEditor($html, $attachmentId)
    {
        $result = $this->reportImageServices->generateReportByAttachmentId($attachmentId);

        return $html;
    }
}
