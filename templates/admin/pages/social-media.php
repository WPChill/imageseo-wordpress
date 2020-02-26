<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<div id="wrap-imageseo">
    <div class="wrap">
        <h1 class="imageseo__page-title"><?php _e('Social Card Optimizer', 'imageseo'); ?></h1>
        <div class="imageseo-flex imageseo-mb-3">
            <div class="fl-1 imageseo-mr-3">
                <div class="imageseo-block imageseo-block--secondary imageseo-block--introduce">
                    <h2><?php _e('What are Social Cards?', 'imageseo'); ?></h2>
                    <p><?php _e('Social cards are used by Twitter, LinkedIn and Facebook to give a preview or your pages and articles. Articles with optimized Social Cards are 92% more likely to be retweeted studies say.', 'imageseo'); ?></p>
                </div>
            </div>
        </div>
        <div id="js-module-social-media"></div>
    </div>
</div>


<script>
    const IMAGESEO_URL_DIST = "<?php echo IMAGESEO_URL_DIST; ?>"
    const IMAGESEO_DATA = {
        USER_INFOS: <?php echo wp_json_encode($this->owner); ?>,
        SETTINGS : <?php echo wp_json_encode($this->options['social_media_settings']); ?>
    }
</script>