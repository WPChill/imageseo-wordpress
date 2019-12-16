<?php

if (!defined('ABSPATH')) {
    exit;
}

$totalAltNoOptimize = imageseo_get_service('QueryImages')->getNumberImageNonOptimizeAlt();
$totalAltOptimize = imageseo_get_service('QueryImages')->getNumberImageOptimizeAlt();

$attachmentsIdsAlreadyReport = imageseo_get_service('QueryImages')->getIdsAttachmentOptimized();
$attachmentsIdsWithAltEmpty = imageseo_get_service('QueryImages')->getIdsAttachmentWithAltEmpty();

$percentLoose = imageseo_get_service('ImageLibrary')->getPercentLooseTraffic($totalAltNoOptimize);
$conditionsFilters = imageseo_get_service('FiltersSpecification')->getConditions();
$metasFilters = imageseo_get_service('FiltersSpecification')->getMetas();

$currentProcess = get_option('_imageseo_current_processed', 0);
$limitImages = $this->owner['plan']['limit_images'] + $this->owner['bonus_stock_images'];
?>

<div class="wrap">
    <h2 class="imageseo__page-title"><?php esc_html_e('ImageSEO - Bulk Optimization', 'imageseo'); ?></h2>
    <hr />
    <div id="js-module-optimization"></div>

    <script>
        const IMAGESEO_DATA = {
            TOTAL_ALT_NO_OPTIMIZE : <?php echo $totalAltNoOptimize; ?>,
            TOTAL_ALT_OPTIMIZE : <?php echo $totalAltOptimize; ?>,
            PERCENT_TRAFFIC_LOOSE : <?php echo $percentLoose; ?>,
            ATTACHMENT_IDS_ALREADY_REPORT : <?php echo wp_json_encode($attachmentsIdsAlreadyReport->posts); ?>,
            ATTACHMENT_IDS_WITH_ALT_EMPTY : <?php echo wp_json_encode($attachmentsIdsWithAltEmpty->posts); ?>,
            LIMIT_IMAGES: <?php echo $limitImages; ?>,
            CURRENT_PROCESS : <?php echo $currentProcess; ?>,
            USER_INFOS: <?php echo wp_json_encode($this->owner); ?>,
            CONDITIONS_FILTERS : <?php echo wp_json_encode($conditionsFilters); ?>,
            METAS_FILTERS : <?php echo wp_json_encode($metasFilters); ?>
        }
    </script>
</div>