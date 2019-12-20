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

$limitImages = $this->owner['plan']['limit_images'] + $this->owner['bonus_stock_images'];

?>


<div id="js-api-key">
    <label for="email" class="imageseo-label">
        <?php _e('Add your API KEY', 'imageseo'); ?> :
    </label>
    <div class="imageseo-flex">
        <div class="fl-2 imageseo-mr-1">
            <input 
                type="text" 
                name="<?php echo esc_attr(sprintf('%s[%s]', IMAGESEO_SLUG, $options_available['api_key']['key'])); ?>"
                id="<?php echo esc_attr($options_available['api_key']['key']); ?>"
                class="imageseo-input imageseo-h100" id="api-key"
                placeholder="xxx"
                value="<?php echo esc_attr($this->options[$options_available['api_key']['key']]); ?>"
            >
        </div>
        <div>
            <button class="imageseo-btn imageseo-btn--simple">
                <?php _e('Add API KEY', 'imageseo'); ?>
            </button>
        </div>
    </div>
</div>
