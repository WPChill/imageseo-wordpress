<?php

use ImageSeoWP\Helpers\AltFormat;
use ImageSeoWP\Helpers\Bulk\AltSpecification;

if (!defined('ABSPATH')) {
    exit;
}

$totalAltNoOptimize = imageseo_get_service('QueryImages')->getNumberImageNonOptimizeAlt();
$percentLoose = imageseo_get_service('ImageLibrary')->getPercentLooseTraffic($totalAltNoOptimize);

$currentProcessed = get_option('_imageseo_bulk_process');
$lastBulkProcess = get_option('_imageseo_last_bulk_process');

$needToStopProcess = get_option('_imageseo_need_to_stop_process');
if (isset($_GET['stopProcess']) && '1' === $_GET['stopProcess']) {
    delete_option('_imageseo_bulk_exclude_filenames');
    delete_option('_imageseo_need_to_stop_process');
    delete_option('_imageseo_bulk_process');
}
$limitImages = 10;
if (null !== $this->owner && isset($this->owner['plan']['limit_images'])) {
    $limitImages = $this->owner['plan']['limit_images'] + $this->owner['bonus_stock_images'];
}

?>

<div id="wrap-imageseo">
    <div class="wrap">
        <h1 class="imageseo__page-title"><?php _e('Alt Optimizer', 'imageseo'); ?></h1>
        <div class="imageseo-flex imageseo-mb-3">
            <div class="fl-1 imageseo-mr-3">
                <div class="imageseo-block imageseo-block--secondary imageseo-block--introduce">
                    <h2><?php _e('What is a Bulk Optimization?', 'imageseo'); ?></h2>
                    <p><?php _e('If you have been lazy with your image alt texts and names, you can start a bulk optimization. The plugin will get through your library content and optimize all the missing alternative texts.', 'imageseo'); ?></p>
                </div>
            </div>
        </div>




		<?php if ($needToStopProcess): ?>
			<?php
                $urlForceStop = admin_url('admin-post.php?action=imageseo_force_stop');
                $urlForceStop = wp_nonce_url($urlForceStop, 'imageseo_force_stop');
            ?>

			<div class="imageseo-block">
				<div class="imageseo-block__inner">
					<h2><?php _e('Your old optimization process is coming to a stop.', 'imageseo'); ?></h2>
					<div class="imageseo-mt-3">
						<div class="imageseo-block__subtitle">
							<?php _e('Please reload the page in a few seconds to resume it or start a new one.', 'imageseo'); ?>
						</div>
					</div>
					<h3 style="font-size:18px; margin-top:30px;"><?php _e("You think it's not working?", 'imageseo'); ?></h3>
					<a href="<?php echo $urlForceStop; ?>" class="imageseo-btn--primary imageseo-btn">
						<?php _e('Forcing the shutdown', 'imageseo'); ?>
					</a>
				</div>
			</div>

		<?php else: ?>

        	<div id="js-module-optimization"></div>
		<?php endif; ?>




    </div>
</div>

<script>
    const IMAGESEO_URL_DIST = "<?php echo IMAGESEO_URL_DIST; ?>"
    const IMAGESEO_DATA = {
		CURRENT_PROCESSED: <?php echo $currentProcessed ? wp_json_encode($currentProcessed) : 'null'; ?>,
		LAST_PROCESSED: <?php echo $lastBulkProcess ? wp_json_encode($lastBulkProcess) : 'null'; ?>,
        TOTAL_ALT_NO_OPTIMIZE : <?php echo $totalAltNoOptimize; ?>,
        PERCENT_TRAFFIC_LOOSE : <?php echo $percentLoose; ?>,
        LIMIT_IMAGES: <?php echo $limitImages; ?>,
        USER_INFOS: <?php echo wp_json_encode($this->owner); ?>,
        LANGUAGES : <?php echo wp_json_encode($this->languages); ?>,
        OPTIONS : <?php echo wp_json_encode($this->options); ?>,
        ALT_FORMATS : <?php echo wp_json_encode(AltFormat::getFormats()); ?>,
        ALT_SPECIFICATION : <?php echo wp_json_encode(AltSpecification::getMetas()); ?>,
        ALT_FILL_TYPE : <?php echo wp_json_encode(AltSpecification::getFillType()); ?>,
    }
</script>
