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
        add_action('manage_media_custom_column', [$this, 'manageMediaCustomColumn'], 10, 2);

        add_action('admin_post_seoimage_report_attachment', [$this, 'ajaxReportAttachment']);
        add_action('wp_ajax_seoimage_report_attachment', [$this, 'ajaxReportAttachment']);
    }

    /**
     * Activate array
     *
     * @return void
     */
    public function manageMediaColumns($columns)
    {
        if (imagify_current_user_can('optimize')) {
            $columns['seoimage'] = __('SeoImage', 'seoimage');
        }

        return $columns;
    }



    /**
     * Add content to the "Imagify" columns in upload.php.
     *
     * @since  1.0
     * @author Jonathan Buttigieg
     *
     * @param string $column_name   Name of the custom column.
     * @param int    $attachment_id Attachment ID.
     */
    public function manageMediaCustomColumn($column_name, $attachment_id)
    {
        if ($column_name !== 'seoimage') {
            return;
        }

        $button = '<a id="seoimage-' . $attachment_id . '" href="' . esc_url(admin_url('admin-post.php?action=seoimage_report_attachment&attachment_id=' . $attachment_id)) . '" class="button-primary">' . __('Analyze', 'seoimage') . '</a>';
        echo $button;
    }

    public function ajaxReportAttachment()
    {
        if (!isset($_GET['attachment_id'])) {
            return;
        }

        $attachment_id = (int) $_GET['attachment_id'];

        $file = get_attached_file($attachment_id);

        $this->reportImageServices->getReportForImage($file);
        // var_dump($url);
        // die;
    }
}
