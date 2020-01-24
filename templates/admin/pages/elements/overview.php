<?php

if (!defined('ABSPATH')) {
    exit;
}

$total = imageseo_get_service('QueryImages')->getTotalImages();
$totalNoAlt = imageseo_get_service('QueryImages')->getNumberImageNonOptimizeAlt();

$percentMissing = 0;
if (0 < $total) {
    $percentMissing = ceil(($totalNoAlt * 100) / $total);
}
$percentComplete = 100 - $percentMissing;

$limitImages = ($this->owner['plan']['limit_images'] + $this->owner['bonus_stock_images']) - $this->owner['current_request_images'];
$needCreditForOptimization = 0;

if ($this->owner) {
    $usageCreditPercent = ceil($this->owner['current_request_images'] * 100 / ($this->owner['plan']['limit_images'] + $this->owner['bonus_stock_images']));
    if ($limitImages < $totalNoAlt) {
        $needCreditForOptimization = $totalNoAlt - $limitImages;
    }
}

?>
<div class="imageseo-block">
    <div class="imageseo-block__inner imageseo-block__inner--head imageseo-block__inner--actions">
        <div class="imageseo-block__inner__title">
            <h3><?php _e('Your website overview', 'imageseo'); ?></h3>
            <p><?php _e("You only need to create an account to get your API key. It's totally free.", 'imageseo'); ?></p>
        </div>
        <div class="imagese-block__inner__actions">
            <a href="<?php echo admin_url('admin.php?page=imageseo-optimization'); ?>" class="imageseo-btn imageseo-btn--simple">
                <?php _e('Bulk optimization', 'imageseo'); ?>
            </a>
        </div>
    </div>
    <div class="imageseo-block__inner">
        <div class="imageseo-flex">
            <div class="imageseo-mr-2">
                <div class="imageseo-icons imageseo-icons--oval">
                    <img src="<?php echo IMAGESEO_URL_DIST . '/images/image.svg'; ?>" alt="">
                </div>
            </div>
            <div class="fl-1">
                <p class="imageseo-mb-0"><strong><?php echo sprintf(__('There are %s images in your library.', 'imageseo'), $total); ?></strong></p>
                <?php if (0 != $percentMissing): ?>
                    <p class="imageseo-mb-0"><?php echo sprintf(__('Did you know that %s alternative texts are missing ?', 'imageseo'), "$percentMissing%"); ?></strong></p>
                <?php endif; ?>
                <div class="imageseo-loader imageseo-mt-2">
                    <div class="imageseo-loader__step" style="width: <?php echo $percentComplete; ?>%"></div>
                </div>
                <div class="imageseo-flex imageseo-mt-1">
                    <div class="fl-1 imageseo-mr-2">
                        <strong class="imageseo-color-blue"><?php echo sprintf(__('%s completed', 'imageseo'), "$percentComplete%"); ?></strong>
                    </div>
                    <div>
                        <a href="<?php echo admin_url('admin.php?page=imageseo-optimization'); ?>">
                            <?php _e('Launch a bulk optimization', 'imageseo'); ?>
                        </a>
                        <?php _e('or', 'imageseo'); ?>
                        <a href="#mainform">
                            <?php _e('optimize your images on upload', 'imageseo'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php if ($this->owner): ?>
            <hr class="imageseo-mt-2 imageseo-mb-2" /> 
            <div class="imageseo-flex">
                <div class="imageseo-mr-2">
                    <div class="imageseo-icons imageseo-icons--oval">
                        <img src="<?php echo IMAGESEO_URL_DIST . '/images/star.svg'; ?>" alt="">
                    </div>
                </div>
                <div class="fl-1">
                    <p class="imageseo-mb-0"><strong><?php echo sprintf(__('%s credits left.', 'imageseo'), $limitImages); ?></strong></p>
                    <p class="imageseo-mb-0"><?php echo sprintf(__('You will need %s more to optimize your website', 'imageseo'), $needCreditForOptimization); ?></strong></p>
                    <div class="imageseo-loader imageseo-mt-2">
                        <div class="imageseo-loader__step" style="width: <?php echo $usageCreditPercent; ?>%"></div>
                    </div>
                    <div class="imageseo-flex imageseo-mt-1">
                        <div class="fl-1">
                            <strong class="imageseo-color-blue"><?php echo sprintf(__('%s credits used', 'imageseo'), "$usageCreditPercent%"); ?></strong>
                        </div>				
                        <a href="https://app.imageseo.io/plan" target="_blank">
                            <?php _e('Buy more credit', 'imageseo'); ?>
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
