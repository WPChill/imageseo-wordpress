<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<div id="js-imageseo-register" class="imageseo-block">
    <div class="imageseo-block__inner imageseo-block__inner--head">
        <h3><?php _e('Create an account!', 'imageseo'); ?></h3>
        <p><?php _e("It's free!", 'imageseo'); ?></p>    
    </div>
    
    <div class="imageseo-block__inner imageseo-block__inner">
        <div id="form-register">
            <div>
                <label for="email" class="imageseo-label">
                    <?php _e('Email', 'imageseo'); ?> :
                </label>
                <input type="email" name="email" class="imageseo-input" id="register-email">
            </div>
            <br />
            <div>
                <label for="password" class="imageseo-label">
                    <?php _e('Password', 'imageseo'); ?> : 
                </label>
                <input type="password" name="password" class="imageseo-input" id="register-password">
            </div>
            <button id="submit-form-register" class="imageseo-btn imageseo-btn--primary">
                <span class="text"><?php _e('Register', 'imageseo'); ?></span>
                <div class="loader lds-dual-ring" style="display:none;"></div>
            </button>
        </div>
        <div class="imageseo-separator">
            <span><?php _e('or', 'imageseo'); ?></span>
        </div>
        <?php include_once __DIR__ . '/api-key.php'; ?>
    </div>
</div>
