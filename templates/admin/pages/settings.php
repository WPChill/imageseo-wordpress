<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<div id="wrap-imageseo">
    <div class="wrap">
        <h1 class="imageseo__page-title"><?php _e('Image SEO Settings', 'imageseo'); ?></h1>
        <?php include_once IMAGESEO_TEMPLATES_ADMIN_PAGES . '/elements/banner-welcome.php'; ?>
        
        <div class="imageseo-flex imageseo-mt-3 imageseo-mb-3">
            <div class="fl-2 imageseo-mr-3">
                <?php if (imageseo_allowed()): ?>

                    <div class="imageseo-block">
                        <div class="imageseo-block__inner imageseo-block__inner--head">
                            <h3><?php _e('Your API Key', 'imageseo'); ?></h3>
                            <p><?php _e('Lorem ipsum.', 'imageseo'); ?></p>    
                        </div>
                        <div class="imageseo-block__inner">
                            <?php include_once IMAGESEO_TEMPLATES_ADMIN_PAGES . '/elements/api-key.php'; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <?php include_once IMAGESEO_TEMPLATES_ADMIN_PAGES . '/elements/register.php'; ?>
                <?php endif; ?>
            </div>
            <div class="fl-3">
                <?php include_once IMAGESEO_TEMPLATES_ADMIN_PAGES . '/elements/overview.php'; ?>
            </div>
        </div>
        
        <form method="post" id="mainform" action="<?php echo esc_url(admin_url('options.php')); ?>">
            <?php
            include_once IMAGESEO_TEMPLATES_ADMIN_PAGES . '/elements/settings.php';

            settings_fields(IMAGESEO_OPTION_GROUP);
            submit_button();
            ?>
        </form>
        <hr>
    </div>
</div>

