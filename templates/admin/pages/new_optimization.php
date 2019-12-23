<?php

use ImageSeoWP\Helpers\AltFormat;

if (!defined('ABSPATH')) {
    exit;
}

$totalAltNoOptimize = imageseo_get_service('QueryImages')->getNumberImageNonOptimizeAlt();
$totalAltOptimize = imageseo_get_service('QueryImages')->getNumberImageOptimizeAlt();

$attachmentsIdsAlreadyReport = imageseo_get_service('QueryImages')->getIdsAttachmentOptimized();

$percentLoose = imageseo_get_service('ImageLibrary')->getPercentLooseTraffic($totalAltNoOptimize);

$currentProcess = get_option('_imageseo_current_processed', 0);
$limitImages = $this->owner['plan']['limit_images'] + $this->owner['bonus_stock_images'];
?>

<div id="wrap-imageseo">
    <div class="wrap">
        <h1 class="imageseo__page-title"><?php _e('Image SEO Settings', 'imageseo'); ?></h1>
        <div class="imageseo-flex imageseo-mb-3">
            <div class="fl-1 imageseo-mr-3">
                <div class="imageseo-block imageseo-block--secondary imageseo-block--introduce">
                    <h2><?php _e('What is a Bulk Optimization ?', 'imageseo'); ?></h2>
                    <p>Start using imageSEO plugin by creating an account or adding an API KEY.</p>
                </div>
            </div>
            <div class="fl-1">
                <div class="imageseo-block imageseo-block--secondary imageseo-block--introduce">
                    <h2>Welcome ! We’re proud to help you optimize your images</h2>
                    <p>Start using imageSEO plugin by creating an account or adding an API KEY.</p>
                </div>
            </div>
        </div>
        
        <div id="js-module-optimization"></div>

    </div>
</div>

<script>
    const IMAGESEO_URL_DIST = "<?php echo IMAGESEO_URL_DIST; ?>"
    const IMAGESEO_DATA = {
        TOTAL_ALT_NO_OPTIMIZE : <?php echo $totalAltNoOptimize; ?>,
        TOTAL_ALT_OPTIMIZE : <?php echo $totalAltOptimize; ?>,
        PERCENT_TRAFFIC_LOOSE : <?php echo $percentLoose; ?>,
        ATTACHMENT_IDS_ALREADY_REPORT : <?php echo wp_json_encode($attachmentsIdsAlreadyReport->posts); ?>,
        LIMIT_IMAGES: <?php echo $limitImages; ?>,
        CURRENT_PROCESS : <?php echo $currentProcess; ?>,
        USER_INFOS: <?php echo wp_json_encode($this->owner); ?>,
        LANGUAGES : <?php echo wp_json_encode($this->languages); ?>,
        OPTIONS : <?php echo wp_json_encode($this->options); ?>,
        ALT_FORMATS : <?php echo wp_json_encode(AltFormat::getFormats()); ?>
    }
</script>