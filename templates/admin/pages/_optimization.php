<?php

$total = imageseo_get_service('ImageLibrary')->getTotalImages();
$totalNoAlt = imageseo_get_service('ImageLibrary')->getNumberImageNonOptimizeAlt();
$totalAlt = imageseo_get_service('ImageLibrary')->getNumberImageOptimizeAlt();
$percentLoose = imageseo_get_service('ImageLibrary')->getPercentLooseTraffic($totalNoAlt);
$minutes = imageseo_get_service('ImageLibrary')->getEstimatedByImagesHuman($totalNoAlt);
$stringTimeEstimated = imageseo_get_service('ImageLibrary')->getStringEstimatedImages($totalNoAlt);

?>
<div class="imageseo-block">
    <p><?php echo sprintf(__('You have <strong class="imageseo-overview__total-images">%s image(s)</strong> in your library', 'imageseo'), $total); ?></p>
    <p><?php echo sprintf(__("<strong>%s images</strong> alternative texts are missing. It's bad for SEO and Accessibility.", 'imageseo'), $totalNoAlt); ?></p>
    <?php if ($percentLoose > 0): ?>
        <p>
            <?php esc_html_e('On 100 users coming from Google, 20% are coming from image researches.', 'imageseo'); ?>
            <?php echo sprintf(__('If you optimize all your images, you could probably <strong class="imageseo-overview__gain">grow your research traffic by %s%s</strong>', 'imageseo'), $percentLoose, '%'); ?>
        </p>
    <?php endif; ?>

    <p><?php echo sprintf(__('Estimated time if you had to fill in your alternative texts and replace your file names <strong>%s</strong> minutes. ', 'imageseo'), $minutes); ?>
    <p><?php echo sprintf(__('We could certainly do it in <strong>%s</strong>. ', 'imageseo'), $stringTimeEstimated); ?>
</div>