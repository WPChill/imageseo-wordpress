<?php

if (! defined('ABSPATH')) {
    exit;
}

use SeoImageWP\Helpers\AttachmentMeta;

$queryAlreadyAttachmentsOptimization = apply_filters('seoimage_query_already_attachments_optimization', [
    'post_type' => 'attachment',
    'post_status' => 'inherit',
    'posts_per_page' => -1,
    'meta_key' => AttachmentMeta::REPORT,
    'meta_compare' => 'EXISTS',
    'post_mime_type' => 'image/jpeg,image/gif,image/jpg,image/png',
    'fields' => 'ids'
]);

$attachmentsAlreadyReport = new WP_Query($queryAlreadyAttachmentsOptimization);


$queryAttachmentsOptimization = apply_filters('seoimage_query_attachments_optimization', [
    'post_type' => 'attachment',
    'post_status' => 'inherit',
    'post_mime_type' => 'image/jpeg,image/gif,image/jpg,image/png',
    'posts_per_page' => -1,
    'fields' => 'ids'
]);
$attachments = new WP_Query($queryAttachmentsOptimization);


$total = count($attachments->posts);
$totalAlreadyReport = count($attachmentsAlreadyReport->posts);
?>

<script>
    var SEOIMAGE_ATTACHMENTS_ALREADY_REPORT_IDS =<?php echo wp_json_encode($attachmentsAlreadyReport->posts); ?>;
    var SEOIMAGE_ATTACHMENTS = <?php echo wp_json_encode($attachments->posts); ?>;
</script>

<div id="wrap-seoimage">
    <div class="wrap">
        <h3><?php esc_html_e('SeoImage - Bulk Optimization', 'seoimage'); ?></h3>
        <hr />
        <p>
            <strong><?php esc_html_e('Total attachment(s) : ', 'seoimage'); ?></strong> <?php echo $total; ?>
        </p>
        <p>
            <strong><?php esc_html_e('Total report(s) : ', 'seoimage'); ?></strong> <?php echo $totalAlreadyReport; ?>
        </p>
        <button class="button button-primary button-hero" id="seoimage-bulk-reports">
            <?php esc_html_e('Regenerate all reports', 'seoimage'); ?>
        </button>
        <hr>
        <div id="seoimage-percent-bulk" class="seoimage-percent">
            <div class="seoimage-percent--item">0%</div>
        </div>
    </div>
</div>
