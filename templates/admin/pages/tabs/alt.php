<?php

if (! defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Helpers\TabsAdmin;
use ImageSeoWP\Helpers\AltTags;

$options_available = [
    'active_alt_write_upload' => [
        'key'         => 'active_alt_write_upload',
        'label'       => __('Alternative text write auto on media upload', 'imageseo'),
        'description' => __('By activating this option, we will automatically write your alternative text according to the report we generate', 'imageseo'),
    ],
    'active_alt_write_with_report' => [
        'key'         => 'active_alt_write_with_report',
        'label'       => __('Automatically fill out and rewrite your alternative texts', 'imageseo'),
        'description' => __('By activating this option, we will automatically fill out and rewrite your alternative texts. We will therefore erase and replace your alternative texts (empty + existing one)', 'imageseo'),
    ],
    'alt_value' => [
        'key'         => 'alt_value',
        'label'       => __('Alternative texts value', 'imageseo'),
        'description' => __('Decide how you want ImageSEO to rewrite your alternative texts. You can use up to three tags and add your own text. However we recommend you to only use %alt_auto_context% for alternative texts.', 'imageseo'),
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
    </tbody>
</table>
