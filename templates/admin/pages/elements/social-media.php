<?php

if (!defined('ABSPATH')) {
    exit;
}

$options_available = [
    'social_media_post_types' => [
        'key'         => 'social_media_post_types',
        'label'       => __('On which type of posts do you want to enable automatic generation of a social media image? ', 'imageseo'),
        'description' => __('Check the publications you are interested in.', 'imageseo'),
    ],
];

$postTypes = imageseo_get_service('WordPressData')->getAllPostTypesSocialMedia();

?>



<div class="imageseo-block">
    <div class="imageseo-block__inner imageseo-block__inner--head">
        <h3><?php _e('Settings - Social media', 'imageseo'); ?></h3>
        <p><?php echo sprintf(__('These options concern the generation of images for social media. If you want to configure your images, %sgo to the social media option page.%s', 'imageseo'), '<a href="' . admin_url('admin.php?page=imageseo-social-media') . '"">', '</a>'); ?></p>    
    </div>
    <div class="imageseo-block__inner">        
        <div class="imageseo-flex">
            <div class="fl-1">
                <label class="imageseo-label">
                    <?php echo esc_html($options_available['social_media_post_types']['label']); ?>
                </label>
                <p><?php echo $options_available['social_media_post_types']['description']; //phpcs:ignore?></p>

                <?php foreach ($postTypes as $postType): ?>
                    <label for="imageseo_post_type_<?php echo $postType->name; ?>" class="imageseo-label imageseo-mb-1">
                        <input
                            name="<?php echo esc_attr(sprintf('%s[%s]', IMAGESEO_SLUG, $options_available['social_media_post_types']['key'])); ?>[]"
                            type="checkbox"
                            id="imageseo_post_type_<?php echo $postType->name; ?>"
                            value="<?php echo $postType->name; ?>"
                            <?php checked(in_array($postType->name, $this->options[$options_available['social_media_post_types']['key']], true), 1); ?>
                        >
                        <?php echo $postType->label; ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

