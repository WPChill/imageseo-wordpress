<?php

if (! defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Helpers\TabsAdmin;
use ImageSeoWP\Helpers\AltTags;


$options_available = [
    'active_alt_write_upload' => [
        'key'         => 'active_alt_write_upload',
        'label'       => __('Automatically fill out your ALT TAG when you upload a media', 'imageseo'),
        'description' => __('If you check this box, uploading a media in the library might be slightly longer (time for our IA to process the file). ', 'imageseo'),
    ],
    'active_rename_write_upload' => [
        'key'         => 'active_rename_write_upload',
        'label'       => __('Automatically rename your files when you upload a media', 'imageseo'),
        'description' => __('If you check this box, uploading a media in the library might be slightly longer (time for our IA to process the file).', 'imageseo'),
    ],
    'default_language_ia' => [
        'key'         => 'default_language_ia',
        'label'       => __('Language', 'imageseo'),
        'description' => __('Our Artificial lntelligence works in English. You can choose another language if you want. The ALT Tags, and filenames will be generated accordingly.', 'imageseo'),
    ]
];

$tags = AltTags::getTags();

$currentLanguage = ($this->owner['plan']['slug'] === 'free'  && $this->owner['bonus_stock_images']  === 0 ) ? 'en' : $this->options[ $options_available['default_language_ia']['key'] ];

?>

<h3><?php esc_html_e('Overview', 'imageseo'); ?></h3>
<hr>
<div class="imageseo-flex" style="margin-bottom:40px;">
	<div class="fl-2 imageseo-overview">
		<?php include_once __DIR__ . '/../_optimization.php'; ?>
	</div>
	<?php if(imageseo_allowed()): ?>
		<div class="fl-2">
			<?php include_once __DIR__ . '/../_plan_need.php'; ?>
		</div>
	<?php endif; ?>
</div>


<h3><?php esc_html_e('Options', 'imageseo'); ?></h3>
<hr>
<table class="form-table">
    <tbody>
        <tr valign="top">
            <th scope="row" class="titledesc">
                <label for="<?php echo esc_attr($options_available['active_alt_write_upload']['key']); ?>">
                    <?php echo esc_html($options_available['active_alt_write_upload']['label']); ?>
                </label>

			</th>
			<td>
				<fieldset>
					<input
						name="<?php echo esc_attr(sprintf('%s[%s]', IMAGESEO_SLUG, $options_available['active_alt_write_upload']['key'])); ?>"
						type="checkbox"
						id="<?php echo esc_attr($options_available['active_alt_write_upload']['key']); ?>"
						<?php checked($this->options[ $options_available['active_alt_write_upload']['key'] ], 1); ?>
					>
					<p><?php echo $options_available['active_alt_write_upload']['description']; //phpcs:ignore?></p>
				</fieldset>
			</td>
        </tr>
        <tr valign="top">
            <th scope="row" class="titledesc">
                <label for="<?php echo esc_attr($options_available['active_rename_write_upload']['key']); ?>">
                    <?php echo esc_html($options_available['active_rename_write_upload']['label']); ?>
                </label>

			</th>
			<td>
				<fieldset>
					<input
						name="<?php echo esc_attr(sprintf('%s[%s]', IMAGESEO_SLUG, $options_available['active_rename_write_upload']['key'])); ?>"
						type="checkbox"
						id="<?php echo esc_attr($options_available['active_rename_write_upload']['key']); ?>"
						<?php checked($this->options[ $options_available['active_rename_write_upload']['key'] ], 1); ?>
					>
					<p><?php echo $options_available['active_rename_write_upload']['description']; //phpcs:ignore?></p>
				</fieldset>
			</td>
        </tr>
        <tr valign="top">
            <th scope="row" class="titledesc">
                <label for="<?php echo esc_attr($options_available['default_language_ia']['key']); ?>">
                    <?php echo esc_html($options_available['default_language_ia']['label']); ?>
                </label>

			</th>
			<td>
				<fieldset>
					<select
						name="<?php echo esc_attr(sprintf('%s[%s]', IMAGESEO_SLUG, $options_available['default_language_ia']['key'])); ?>"
						<?php if ($this->owner === null || ($this->owner['plan']['slug'] === 'free' && $this->owner['bonus_stock_images'] === 0)): ?>
						disabled
						<?php endif;?>
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
					<?php if ($this->owner === null || ($this->owner['plan']['slug'] === 'free' && $this->owner['bonus_stock_images']  === 0) || empty( $this->options['api_key'] ) || !imageseo_allowed() ): ?>
						<div class="imageseo-account-info imageseo-account-info--error" style="background-color:#c50000;">
							 <?php _e('You need to <a target="_blank" style="color:#fff" href="https://app.imageseo.io/plan?from=wordpress">become a premium</a> to be able to change the language of the AI', 'imageseo'); ?>
						</div>
					<?php endif; ?>
				</fieldset>
			</td>
        </tr>
    </tbody>
</table>
