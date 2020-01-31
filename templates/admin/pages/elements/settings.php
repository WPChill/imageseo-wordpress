<?php

if (!defined('ABSPATH')) {
    exit;
}

$options_available = [
    'active_alt_write_upload' => [
        'key'         => 'active_alt_write_upload',
        'label'       => __('Automatically fill out ALT Texts when you upload an image', 'imageseo'),
        'description' => __('If you tick this box, the plugin will automatically write an alternative to the images you will upload. ', 'imageseo'),
    ],
    'active_rename_write_upload' => [
        'key'         => 'active_rename_write_upload',
        'label'       => __('Automatically rename your files when you upload a media', 'imageseo'),
        'description' => __('If you tick this box, the plugin will automatically rewrite with SEO friendly content the name of the images you will upload.', 'imageseo'),
    ],
    'default_language_ia' => [
        'key'         => 'default_language_ia',
        'label'       => __('Language', 'imageseo'),
        'description' => __('In which language should we write your filenames and alternative texts.', 'imageseo'),
    ],
];

$currentLanguage = ('free' === $this->owner['plan']['slug'] && 0 === $this->owner['bonus_stock_images']) ? 'en' : $this->options[$options_available['default_language_ia']['key']];

?>



<div class="imageseo-block">
    <div class="imageseo-block__inner imageseo-block__inner--head">
        <h3><?php _e('Settings - On-upload optimization', 'imageseo'); ?></h3>
        <p><?php echo sprintf(__('These settings concern the optimization of your images on upload. If you want to optimize ALL your images %sgo on bulk optimization.%s', 'imageseo'), '<a href="' . admin_url('admin.php?page=imageseo-optimization') . '"">', '</a>'); ?></p>    
    </div>
    <div class="imageseo-block__inner">
        <div class="imageseo-flex">
            <div class="imageseo-mr-2">
                <input
                    name="<?php echo esc_attr(sprintf('%s[%s]', IMAGESEO_SLUG, $options_available['active_alt_write_upload']['key'])); ?>"
                    type="checkbox"
                    id="<?php echo esc_attr($options_available['active_alt_write_upload']['key']); ?>"
                    <?php checked($this->options[$options_available['active_alt_write_upload']['key']], 1); ?>
                >

            </div>
            <div class="fl-1">
                <label for="<?php echo esc_attr($options_available['active_alt_write_upload']['key']); ?>" class="imageseo-label">
                    <?php echo esc_html($options_available['active_alt_write_upload']['label']); ?>
                </label>
                <p><?php echo $options_available['active_alt_write_upload']['description']; //phpcs:ignore?></p>
            </div>
        </div>

        <div class="imageseo-flex imageseo-mt-3">
            <div class="imageseo-mr-2">
                <input
                    name="<?php echo esc_attr(sprintf('%s[%s]', IMAGESEO_SLUG, $options_available['active_rename_write_upload']['key'])); ?>"
                    type="checkbox"
                    id="<?php echo esc_attr($options_available['active_rename_write_upload']['key']); ?>"
                    <?php checked($this->options[$options_available['active_rename_write_upload']['key']], 1); ?>
                >

            </div>
            <div class="fl-1">
                <label for="<?php echo esc_attr($options_available['active_rename_write_upload']['key']); ?>" class="imageseo-label">
                    <?php echo esc_html($options_available['active_rename_write_upload']['label']); ?>
                </label>
            
                <p><?php echo $options_available['active_rename_write_upload']['description']; //phpcs:ignore?></p>
            </div>
        </div>
        
        
        <div class="imageseo-flex imageseo-mt-3">
            <div class="imageseo-mr-2">
                <input
                    type="checkbox"
                    style="visibility:hidden"
                >
            </div>
            <div class="fl-1">
                <label for="<?php echo esc_attr($options_available['default_language_ia']['key']); ?>" class="imageseo-label">
                    <?php echo esc_html($options_available['default_language_ia']['label']); ?>
                </label>
                <select
                    name="<?php echo esc_attr(sprintf('%s[%s]', IMAGESEO_SLUG, $options_available['default_language_ia']['key'])); ?>"
                >
                    <?php foreach ($this->languages as $language): ?>
                        <option
                            <?php selected($currentLanguage, $language['code']); ?>
                            value="<?php echo esc_attr($language['code']); ?>"
                        >
                            <?php echo esc_html($language['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p><?php echo $options_available['default_language_ia']['description']; //phpcs:ignore?></p>
            </div>
        </div>
        <div class="imageseo-block imageseo-block--secondary imageseo-mt-1">
            <div class="imageseo-block__inner" style="padding:15px;">
                <p style="margin:0px;">
                    <?php _e('You will consume one credit for each image optimized.', 'imageseo'); ?>
                </p>
            </div>
        </div>
    </div>
</div>

