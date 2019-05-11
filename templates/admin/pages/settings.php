<?php

if (! defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Helpers\TabsAdmin;

?>

<div id="wrap-imageseo">
    <div class="wrap">

        <?php //include_once IMAGESEO_TEMPLATES_ADMIN_PAGES . '/nav.php'; ?>

        <form method="post" id="mainform" action="<?php echo esc_url(admin_url('options.php')); ?>">
            <?php
            switch ($this->tab_active) {
                case TabsAdmin::SETTINGS:
                default:
                    include_once IMAGESEO_TEMPLATES_ADMIN_PAGES . '/tabs/settings.php';
                    include_once IMAGESEO_TEMPLATES_ADMIN_PAGES . '/tabs/alt.php';

                    break;
            }

            settings_fields(IMAGESEO_OPTION_GROUP);
            submit_button();
            ?>
            <input type="hidden" name="tab" value="<?php echo esc_attr($this->tab_active); ?>">
        </form>
        <hr>
    </div>
</div>

