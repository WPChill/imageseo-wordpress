<?php

if (! defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Helpers\AttachmentMeta;

$queryAlreadyAttachmentsOptimization = apply_filters('imageseo_query_already_attachments_optimization', [
    'post_type' => 'attachment',
    'post_status' => 'inherit',
    'posts_per_page' => -1,
    'meta_key' => AttachmentMeta::REPORT,
    'meta_compare' => 'EXISTS',
    'post_mime_type' => 'image/jpeg,image/gif,image/jpg,image/png',
    'fields' => 'ids',
    'orderby' => 'id',
    'order'   => 'ASC',
]);

$attachmentsAlreadyReport = new WP_Query($queryAlreadyAttachmentsOptimization);


$queryAttachmentsOptimization = apply_filters('imageseo_query_attachments_optimization', [
    'post_type' => 'attachment',
    'post_status' => 'inherit',
    'post_mime_type' => 'image/jpeg,image/gif,image/jpg,image/png',
    'posts_per_page' => -1,
    'fields' => 'ids',
    'orderby' => 'id',
    'order'   => 'ASC',
]);
$attachments = new WP_Query($queryAttachmentsOptimization);

$total = count($attachments->posts);
$totalAlreadyReport = count($attachmentsAlreadyReport->posts);

$currentProcess = get_option('_imageseo_current_processed', 0);
?>

<script>
    var IMAGESEO_ATTACHMENTS_ALREADY_REPORT_IDS =<?php echo wp_json_encode($attachmentsAlreadyReport->posts); ?>;
	var IMAGESEO_ATTACHMENTS = <?php echo wp_json_encode($attachments->posts); ?>;
	var IMAGESEO_CURRENT_PROCESS = <?php echo $currentProcess; ?>;
</script>

<div id="wrap-imageseo">
    <div class="wrap">
        <h3><?php esc_html_e('ImageSEO - Bulk Optimization', 'imageseo'); ?></h3>
        <hr />
        <p>
            <strong><?php esc_html_e('Total attachment(s) : ', 'imageseo'); ?></strong> <?php echo $total; ?>
        </p>
        <p>
			<strong><?php esc_html_e('Total already report(s) : ', 'imageseo'); ?></strong> <?php echo $totalAlreadyReport; ?>
			<br />
			<span><?php _e('Reports already made are not included in your image limitation', 'imageseo'); ?></span>
        </p>
        <p>
            <strong><?php esc_html_e('Your image limitation : ', 'imageseo'); ?></strong> <?php echo $this->owner['current_request_images']; ?> /<?php echo $this->owner['plan']['limit_images']; ?>
		</p>

		<?php if ($this->owner['current_request_images'] >= $this->owner['plan']['limit_images']) {
    $images_available = 0;
} else {
    $images_available = $this->owner['plan']['limit_images'] - $this->owner['current_request_images'];
}

        ?>
		<?php if ($images_available <= 0): ?>
			<div class="imageseo-account-info imageseo-account-info--warning">
				<p><?php _e('Be careful, you do not have enough credits left to complete the report of all your images', 'imageseo'); ?></p>
				<p><?php _e('Remember to increase your plan by <a href="https://app.imageseo.io/" target="_blank">logging into your account </a>', 'imageseo'); ?></p>
			</div>
		<?php endif; ?>

      	<h2><?php _e('Method', 'imageseo'); ?></h2>

		<div class="option">
			<label>
				<input type="radio" name="method" value="new" checked>
				<?php _e('Start a new process', 'imageseo'); ?>
			</label>
		</div>
		<?php if ($currentProcess !== 0): ?>
		<br />
		<div class="option">
			<label>
				<input type="radio" name="method" value="old">
				<?php _e('Continue the last process', 'imageseo'); ?>
				<span>(<?php echo sprintf('%s/%s', $currentProcess+1, $total); ?>)</span>
			</label>
		</div>
		<?php endif; ?>

        <h3>
           	<?php _e('Options', 'imageseo'); ?>
		</h3>
		<div class="option">
			<label>
				<input type="checkbox" name="update_alt" id="option-update-alt" />
				<?php _e('Update alternative texts automatically', 'imageseo'); ?>
			</label>
		</div>
		<br />
		<div class="option">
			<label>
				<input type="checkbox" name="rename_file" id="option-rename-file" />
				<?php _e('Rename files automatically', 'imageseo'); ?>
			</label>
		</div>
		<br />
        <button class="button button-primary" id="imageseo-bulk-reports--start">
			<span><?php esc_html_e('Run (may take a while)', 'imageseo'); ?></span>
			<div class="imageseo-loading" style="display:none;"></div>
        </button>
        <button class="button button-secondary" id="imageseo-bulk-reports--stop" disabled>
			<span><?php esc_html_e('Stop', 'imageseo'); ?></span>
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
