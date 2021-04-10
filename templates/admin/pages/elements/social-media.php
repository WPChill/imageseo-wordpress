<?php

if (!defined('ABSPATH')) {
    exit;
}

$options_available = [
    'social_media_post_types' => [
        'key'         => 'social_media_post_types',
        'label'       => __('Do you want to enable the automatic generation of Social Media Card for your:', 'imageseo'),
        'description' => __('', 'imageseo'),
    ],
    'social_media_type' => [
        'key'         => 'social_media_type',
        'label'       => __('Which social media do you want to manage?', 'imageseo'),
        'description' => __('You will consume one image credit for each social media.', 'imageseo'),
    ],
];

$postTypes = imageseo_get_service('WordPressData')->getAllPostTypesSocialMedia();

?>



<div class="imageseo-block">
    <div class="imageseo-block__inner imageseo-block__inner--head">
        <h3><?php _e('Settings – Social Media Cards Generator', 'imageseo'); ?></h3>
        <p><?php echo sprintf(__('These settings concern the generation of nice preview cards for Facebook, Twitter and LinkedIn. To define the template of your Social Media Cards, %sgo on Social Media Cards.%s', 'imageseo'), '<a href="' . admin_url('admin.php?page=imageseo-social-media') . '"">', '</a>'); ?></p>
    </div>
    <div class="imageseo-block__inner">
        <div>
            <p>
                <strong>
                    <?php echo esc_html($options_available['social_media_post_types']['label']); ?>
                </strong>
            </p>
            <p><?php echo $options_available['social_media_post_types']['description']; //phpcs:ignore ?></p>

            <?php foreach ($postTypes as $postType) { ?>
                <label for="imageseo_post_type_<?php echo $postType->name; ?>" class="imageseo-label imageseo-mb-2">
                    <input
                        name="<?php echo esc_attr(sprintf('%s[%s]', IMAGESEO_SLUG, $options_available['social_media_post_types']['key'])); ?>[]"
                        type="checkbox"
                        id="imageseo_post_type_<?php echo $postType->name; ?>"
                        value="<?php echo $postType->name; ?>"
                        <?php checked(in_array($postType->name, $this->options[$options_available['social_media_post_types']['key']], true), 1); ?>
                    >
                    <?php echo $postType->label; ?>
                </label>
            <?php } ?>
            <p><?php echo sprintf(__('Please make sure that %syou have created a template%s and that your post and or page have a picture!', 'imageseo'), '<a href="' . admin_url('admin.php?page=imageseo-social-media') . '"">', '</a>'); ?></p>
        </div>

        <div class="imageseo-block imageseo-block--secondary imageseo-mt-1">
            <div class="imageseo-block__inner" style="padding:15px;">
                <p style="margin:0px;">
                    <?php _e('You will consume one credit by Socia Media Cards created (1 page = 1 Social media card working on Twitter, Facebook and LinkedIn).', 'imageseo'); ?>
                </p>
            </div>
        </div>
    </div>
</div>

