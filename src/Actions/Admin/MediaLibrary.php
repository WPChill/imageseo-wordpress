<?php

namespace ImageSeoWP\Actions\Admin;

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
        $this->reportImageServices   = imageseo_get_service('ReportImage');
    }

    /**
     * @return void
     */
    public function hooks()
    {
        if (!imageseo_allowed()) {
            return;
        }

        add_filter('manage_media_columns', [$this, 'manageMediaColumns']);
        add_filter('attachment_fields_to_edit', [$this, 'fieldsEdit'], 999, 2);
        add_action('manage_media_custom_column', [$this, 'manageMediaCustomColumn'], 10, 2);

        add_action('wp_ajax_imageseo_media_alt_update', [$this, 'ajaxAltUpdate']);

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


        $formFields['imageseo-has-report'] = array(
            'label'         => __('ImageSEO Report'),
            'input'         => 'html',
            'html'          => '<a id="imageseo-' . $post->ID . '" href="' . esc_url(admin_url('post.php?post=' . $post->ID . '&action=edit')) . '" class="button">' . __('View report', 'imageseo') . '</a>',
            'show_in_edit'  => true,
            'show_in_modal' => true,
        );

        return $formFields;
    }

    public function metaboxReport()
    {
        add_meta_box(
            'imageseo-report',
            __('Report ImageSEO', 'imageseo'),
            [$this, 'viewMetaboxReport'],
            'attachment',
            'normal'
        );
    }

    public function viewMetaboxReport($post)
    {
        include_once IMAGESEO_TEMPLATES_ADMIN_METABOXES . '/report.php';
    }

    /**
     * Activate array
     *
     * @return void
     */
    public function manageMediaColumns($columns)
    {
        $columns['imageseo'] = __('ImageSEO', 'imageseo');

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
        if ($columnName !== 'imageseo') {
            return;
        }

        $alt = wp_strip_all_tags(__(get_post_meta($attachmentId, '_wp_attachment_image_alt', true)));

        $haveAlreadyReport = $this->reportImageServices->haveAlreadyReportByAttachmentId($attachmentId); ?>
        <div class="media-column-imageseo">
            <?php
            if (empty($alt)) {
                ?>
                <div class="media-column-imageseo--no_alt">
                    <span class="dashicons dashicons-dismiss"></span>
                    <span class="text"><?php esc_html_e('This image has not alt attribute !', 'imageseo'); ?>
                </div>
                <?php
            }
        if ($haveAlreadyReport) {
            ?>
					<p><?php esc_html_e('The media file already has a report', 'imageseo'); ?></p>
					<div class="media-column-imageseo--actions">
						<a id="imageseo-<?php echo $attachmentId; ?>" href="<?php echo esc_url(get_edit_post_link($attachmentId)); ?>" class="button">
							<?php echo __('View report', 'imageseo'); ?>
						</a>
						<a id="imageseo-<?php echo $attachmentId; ?>" href="<?php echo esc_url(admin_url('admin-post.php?action=imageseo_report_attachment&attachment_id=' . $attachmentId)); ?>" class="button">
							<?php echo __('Re-Analyze', 'imageseo'); ?>
						</a>
					</div>
					<?php
        } else {
            ?>
					<div class="media-column-imageseo--actions">
						<a id="imageseo-<?php echo $attachmentId; ?>" href="<?php echo esc_url(admin_url('admin-post.php?action=imageseo_report_attachment&attachment_id=' . $attachmentId)); ?>" class="button-primary">
							<?php echo __('Analyze', 'imageseo'); ?>
						</a>
					</div>
				<?php
        } ?>
				<div id="wrapper-imageseo-<?php echo $attachmentId; ?>" class="wrapper-imageseo-input-alt">
					<input
						type="text"
						name="imageseo-alt"
						data-id="<?php echo $attachmentId; ?>"
						class="imageseo-alt-ajax large-text"
						id="imageseo-alt-<?php echo $attachmentId; ?>"
						value="<?php echo $alt; ?>"
						placeholder="<?php echo esc_html('Enter alt attribute', 'imageseo'); ?>"
					/>
					<button class="button" data-id="<?php echo $attachmentId; ?>">
						<span><?php _e('Submit', 'imageseo'); ?></span>
						<div class="imageseo-loading imageseo-loading--library" style="display:none"></div>
					</button>
				</div>
			</div>
        <?php
    }


    public function sendToEditor($html, $attachmentId)
    {
        $result = $this->reportImageServices->generateReportByAttachmentId($attachmentId);

        return $html;
    }
}
