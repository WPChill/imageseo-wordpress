<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<div id="wrap-imageseo">
    <div class="wrap">
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