<?php

if (! defined('ABSPATH')) {
    exit;
}

use SeoImageWP\Helpers\TabsAdminSeoImage;

?>

<div id="wrap-seoimage">
    <div class="wrap">

        <?php include_once SEOIMAGE_TEMPLATES_ADMIN_PAGES . '/nav.php'; ?>

        <form method="post" id="mainform" action="<?php echo esc_url(admin_url('options.php')); ?>">
            <?php
            switch ($this->tab_active) {
                case TabsAdminSeoImage::SETTINGS_ALT:
                    include_once SEOIMAGE_TEMPLATES_ADMIN_PAGES . '/tabs/alt.php';
                    break;
                case TabsAdminSeoImage::SETTINGS:
                default:
                    include_once SEOIMAGE_TEMPLATES_ADMIN_PAGES . '/tabs/settings.php';
                    break;
            }

            settings_fields(SEOIMAGE_OPTION_GROUP);
            submit_button();
            ?>
            <input type="hidden" name="tab" value="<?php echo esc_attr($this->tab_active); ?>">
        </form>
        <hr>
    </div>
</div>

