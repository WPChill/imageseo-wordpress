<?php 
if(imageseo_allowed()){
    return;
}

?>

<div id="js-imageseo-register" class="imageseo-block">
    <h3><?php _e("Don't have an account and API Key? Create one quickly !", 'imageseo'); ?></h3>

    <div id="form-register">
        <div>
            <label for="email">
                <?php _e('Email', 'imageseo'); ?> :
            </label>
            <input type="email" name="email" class="regular-text" id="register-email">
        </div>
        <br />
        <div>
            <label for="password">
                <?php _e('Password', 'imageseo'); ?> : 
            </label>
            <input type="password" name="password" class="regular-text" id="register-password">
        </div>
        <button id="submit-form-register" class="imageseo-btn imageseo-btn--primary">
            <span class="text"><?php _e('Register', 'imageseo'); ?></span>
            <div class="loader lds-dual-ring" style="display:none;"></div>
        </button>
    </div>
</div>
