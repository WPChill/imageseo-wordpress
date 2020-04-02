<?php

use ImageSeoWP\Helpers\AltFormat;
use ImageSeoWP\Helpers\Bulk\AltSpecification;

if (!defined('ABSPATH')) {
    exit;
}

$currentProcessed = get_option('_imageseo_current_processed');

$totalAltNoOptimize = imageseo_get_service('QueryImages')->getNumberImageNonOptimizeAlt();

$percentLoose = imageseo_get_service('ImageLibrary')->getPercentLooseTraffic($totalAltNoOptimize);

$currentProcess = get_option('_imageseo_current_processed', 0);

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
        
        <div id="js-module-optimization"></div>

    </div>
</div>

<script>
    const IMAGESEO_URL_DIST = "<?php echo IMAGESEO_URL_DIST; ?>"
    const IMAGESEO_DATA = {
        CURRENT_PROCESSED: <?php echo wp_json_encode($currentProcessed); ?>,
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