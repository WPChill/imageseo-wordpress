<?php

if (! defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Helpers\AttachmentMeta;

$altValue = $this->optionServices->getOption('alt_value');

$queryAlreadyAttachmentsOptimization = apply_filters('imageseo_query_already_attachments_optimization', [
    'post_type' => 'attachment',
    'post_status' => 'inherit',
    'posts_per_page' => -1,
    'meta_key' => AttachmentMeta::REPORT,
    'meta_compare' => 'EXISTS',
    'post_mime_type' => 'image/jpeg,image/gif,image/jpg,image/png',
    'fields' => 'ids'
]);

$attachmentsAlreadyReport = new WP_Query($queryAlreadyAttachmentsOptimization);


$queryAttachmentsOptimization = apply_filters('imageseo_query_attachments_optimization', [
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
    var IMAGESEO_ATTACHMENTS_ALREADY_REPORT_IDS =<?php echo wp_json_encode($attachmentsAlreadyReport->posts); ?>;
    var IMAGESEO_ATTACHMENTS = <?php echo wp_json_encode($attachments->posts); ?>;
</script>

<div id="wrap-imageseo">
    <div class="wrap">
        <h3><?php esc_html_e('ImageSeo - Bulk Optimization', 'imageseo'); ?></h3>
        <hr />
        <p>
            <strong><?php esc_html_e('Alternative text configuration : ', 'imageseo'); ?></strong> <?php echo $altValue; ?>
        </p>
        <p>
            <strong><?php esc_html_e('Total attachment(s) : ', 'imageseo'); ?></strong> <?php echo $total; ?>
        </p>
        <p id="imageseo-total-already-reports">
            <strong><?php esc_html_e('Total report(s) : ', 'imageseo'); ?></strong> <span><?php echo $totalAlreadyReport; ?></span>
        </p>
        <button class="button button-primary button-hero" id="imageseo-bulk-reports">
			<span><?php esc_html_e('(Re) Generate all reports', 'imageseo'); ?></span>
			<div class="imageseo-loading" style="display:none;"></div>
        </button>
        <hr>
        <div id="imageseo-percent-bulk" class="imageseo-percent">
            <div class="imageseo-percent--item">0%</div>
		</div>
		<div id="imageseo-reports-js" class="imageseo-reports">
			<div class="imageseo-reports-header">
			<div class="imageseo-reports--status"><?php _e('Status', 'imageseo'); ?></div>
				<div class="imageseo-reports--image"><?php _e('Image', 'imageseo'); ?></div>
				<div class="imageseo-reports--src"><?php _e('Image name', 'imageseo'); ?></div>
				<div class="imageseo-reports--alt"><?php _e('Alternative text', 'imageseo'); ?></div>
			</div>
			<div class="imageseo-reports-body"></div>
		</div>
    </div>
</div>
