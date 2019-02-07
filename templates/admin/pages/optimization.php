<?php

if (! defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Helpers\AttachmentMeta;

$altValue = $this->optionServices->getOption('alt_value');
$renameValue = $this->optionServices->getOption('rename_value');

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
            <strong><?php esc_html_e('Alternative text configuration : ', 'imageseo'); ?></strong> <?php echo $altValue; ?>
        </p>
        <p>
            <strong><?php esc_html_e('Total attachment(s) : ', 'imageseo'); ?></strong> <?php echo $total; ?>
        </p>

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
				<?php _e('Update alternative texts. Your configuration : ', 'imageseo'); ?><?php echo $altValue; ?>
			</label>
		</div>
		<br />
		<div class="option">
			<label>
				<input type="checkbox" name="rename_file" id="option-rename-file" />
				<?php _e('Rename files. Your configuration :', 'imageseo'); ?> <?php echo $renameValue; ?>
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
