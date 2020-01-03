<?php

if (!defined('ABSPATH')) {
    exit;
}

$totalNoAlt = imageseo_get_service('QueryImages')->getNumberImageNonOptimizeAlt();
$totalAlt = imageseo_get_service('QueryImages')->getNumberImageOptimizeAlt();
$percentLoose = imageseo_get_service('ImageLibrary')->getPercentLooseTraffic($totalNoAlt);
$minutes = imageseo_get_service('ImageLibrary')->getEstimatedByImagesHuman($totalNoAlt);
$stringTimeEstimated = imageseo_get_service('ImageLibrary')->getStringEstimatedImages($totalNoAlt);

if ($percentLoose < 2) {
    return;
}
?>

<div class="imageseo-block">
    <div class="imageseo-block__inner">
        <div class="imageseo-flex">
            <div class="imageseo-mr-2">
                <div class="imageseo-icons imageseo-icons--oval">
                    <img src="<?php echo IMAGESEO_URL_DIST . '/images/clock.svg'; ?>" alt="">
                </div>
            </div>
            <div class="fl-1">
                <p class="imageseo-mb-0"><?php echo sprintf(__("<strong>%s images</strong> alternative texts are missing. It's bad for SEO and Accessibility.", 'imageseo'), $totalNoAlt); ?></p>
                <p><?php echo sprintf(__('Estimated time if you had to fill in your alternative texts and replace your file names <strong>%s</strong> minutes. ', 'imageseo'), $minutes); ?>
                <p><?php echo sprintf(__('We could certainly do it in <strong>%s</strong>. ', 'imageseo'), $stringTimeEstimated); ?>
            </div>
        </div>
    </div>
</div>
