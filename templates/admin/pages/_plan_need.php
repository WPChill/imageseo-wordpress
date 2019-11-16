<?php
use ImageSeoWP\Helpers\Plan;
$imageLibraryService = imageseo_get_service('ImageLibrary');
$total = $imageLibraryService->getTotalImages();
$plan = Plan::getPlanByImages($imageLibraryService->getImagesNeedByMonth());
$oneShot = Plan::getOneShotByImages($imageLibraryService->getNumberImageNonOptimizeAlt());
?>

<div class="imageseo-plan-need">
    <div class="imageseo-plan-need__title">
        <?php esc_html_e("You're new to Image SEO?", 'imageseo'); ?>
    </div>

    <p>
        <?php esc_html_e("Our plugin can help you to understand your needs and determine the best plan for you.", 'imageseo'); ?>
    </p>
    <p class="imageseo-plan-need__choice">
        <?php echo sprintf(__("You have %s images. We recommend you : ", "imageseo"), $total); ?> <strong><?php echo $plan['name']; ?> (<?php echo $plan['price']; ?>€ <?php esc_html_e('by month', 'imageseo'); ?>)</strong>
    </p>
    <?php if($oneShot != null): ?>
    <p class="imageseo-plan-need__choice">
        <?php echo sprintf(__("If you want to bulk all your library you should buy this one shoot plan : ", "imageseo"), $oneShot['images']); ?> <strong><?php echo $oneShot['price']; ?>€</strong>
    </p>
    <?php endif; ?>

    <div style="text-align:center;">
        <a href="https://app.imageseo.io/plan?from=wordpress_plan"><?php esc_html_e('Buy more credits', 'imageseo'); ?></a>
    </div>

</div>