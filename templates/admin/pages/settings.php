<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<div id="wrap-imageseo">
    <div class="wrap">
        <h1>ImageSEO Settings</h1>
        <hr>

        <form method="post" id="mainform" action="<?php echo esc_url(admin_url('options.php')); ?>">
            <?php
            include_once IMAGESEO_TEMPLATES_ADMIN_PAGES . '/settings/api.php';
            include_once IMAGESEO_TEMPLATES_ADMIN_PAGES . '/settings/alt.php';

            settings_fields(IMAGESEO_OPTION_GROUP);
            submit_button();
            ?>
        </form>
        <hr>
    </div>
</div>

