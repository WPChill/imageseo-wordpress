<?php

if (!defined('ABSPATH')) {
    exit;
}

$options_available = [
    'api_key' => [
        'key'         => 'api_key',
        'label'       => __('API Key', 'imageseo'),
        'description' => '',
    ],
];

?>


<div id="js-api-key">
    <label for="email" class="imageseo-label">
        <?php _e('Add your API KEY', 'imageseo'); ?> :
    </label>
    <div class="imageseo-flex">
        <div class="fl-2 imageseo-mr-1">
            <input 
                type="<?php if (imageseo_allowed()): ?>password<?php else: ?>text<?php endif; ?>"
                name="<?php echo esc_attr(sprintf('%s[%s]', IMAGESEO_SLUG, $options_available['api_key']['key'])); ?>"
                id="<?php echo esc_attr($options_available['api_key']['key']); ?>"
                class="imageseo-input imageseo-h100" id="api-key"
                placeholder="xxx"
                value="<?php echo esc_attr($this->options[$options_available['api_key']['key']]); ?>"
            >
        </div>
        <div>
            <button class="imageseo-btn imageseo-btn--simple" style="display:flex; align-items:center;" id="js-api-key-button">
                <img
                    src="<?php echo IMAGESEO_URL_DIST; ?>/images/rotate-cw.svg"
                    style="animation:imageseo-rotation 1s infinite linear; margin-right:10px; display:none;"
                />
                <?php _e('Validate your API KEY', 'imageseo'); ?>
            </button>
        </div>
    </div>
</div>
