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

?>


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
					>
						<?php foreach ($this->languages as $language): ?>
							<option
								<?php selected($this->options[ $options_available['default_language_ia']['key'] ], $language['code']); ?>
								value="<?php echo esc_attr($language['code']); ?>"
							>
								<?php echo esc_html($language['name']); ?>
							</option>
						<?php endforeach; ?>
					</select>
					<p><?php echo $options_available['default_language_ia']['description']; //phpcs:ignore?></p>
				</fieldset>
			</td>
        </tr>
    </tbody>
</table>
