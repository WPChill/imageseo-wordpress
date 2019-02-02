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
        'label'       => __('Automatically fill out and rewrite all your alternative texts', 'imageseo'),
        'description' => __('By activating this option, we will automatically fill out and rewrite ALL your alternative texts. We will therefore erase and replace all your alternative texts (empty + existing one)', 'imageseo'),
    ],
    'alt_value' => [
        'key'         => 'alt_value',
        'label'       => __('Alternative texts value', 'imageseo'),
        'description' => __('Decide how you want ImageSEO to rewrite your alternative texts. You can use up to three tags and add your own text. However we recommend you to only use %alt_auto_context% for alternative texts.', 'imageseo'),
    ],
    'alt_auto_percent' => [
        'key'         => 'alt_auto_percent',
        'label'       => __('Alternative text auto percent', 'imageseo'),
        'description' => __('Our plugin works with artificial intelligence. The %alt_auto_context% and %alt_auto_representation% might therefor not be 100% accurate with your images. You need to define a threshold (between 0 and 100) of accuracy. We advise you to set up the threshold at 60%. We always chose the most relevant keyword(s).', 'imageseo'),
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
                <label for="<?php echo esc_attr($options_available['active_alt_write_with_report']['key']); ?>">
                    <?php echo esc_html($options_available['active_alt_write_with_report']['label']); ?>
                </label>

			</th>
			<td>
				<fieldset>
					<input
					name="<?php echo esc_attr(sprintf('%s[%s]', IMAGESEO_SLUG, $options_available['active_alt_write_with_report']['key'])); ?>"
					type="checkbox"
					id="<?php echo esc_attr($options_available['active_alt_write_with_report']['key']); ?>"
					<?php checked($this->options[ $options_available['active_alt_write_with_report']['key'] ], 1); ?>
					>
					<p><?php echo $options_available['active_alt_write_with_report']['description']; //phpcs:ignore?></p>
				</fieldset>
			</td>
        </tr>
        <tr valign="top">
            <th scope="row" class="titledesc">
                <label for="<?php echo esc_attr($options_available['alt_value']['key']); ?>">
                    <?php echo esc_html($options_available['alt_value']['label']); ?>
                </label>

            </th>
            <td class="forminp forminp-text">
                <input
                    name="<?php echo esc_attr(sprintf('%s[%s]', IMAGESEO_SLUG, $options_available['alt_value']['key'])); ?>"
                    id="<?php echo esc_attr($options_available['alt_value']['key']); ?>"
                    type="text"
                    class="regular-text"
                    required
                    placeholder="<?php esc_html_e('Your attribute value', 'imageseo'); ?>"
                    value="<?php echo esc_attr($this->options[ $options_available['alt_value']['key'] ]); ?>"
                >
                <div class="available-structure-tags hide-if-no-js">
					<p><?php echo $options_available['alt_value']['description']; //phpcs:ignore?></p>
                    <p><strong><?php esc_html_e('Available tags :', 'imageseo'); ?></strong></p>
                    <ul class="tags">
                        <?php foreach ($tags as $tag) : ?>
                            <li class="tags__item">
                                <button type="button" data-tag="<?php echo esc_attr($tag); ?>" class="button button-secondary button-tags">
                                    <?php echo esc_html($tag); ?>
                                </button>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <ul>
                        <li>
                            <strong><?php echo AltTags::SITE_TITLE; ?> </strong>: <?php _e('We use your site title to fill up alternative texts', 'imageseo'); ?> <em>( <?php _e('Your site title is : ', 'imageseo'); echo get_bloginfo('title') ?> )</em>
                        </li>
                        <li>
                            <strong><?php echo AltTags::ALT_AUTO_CONTEXT; ?></strong> : <?php _e('We use keyword(s) representative of the context in which Google analyzes your image on your website and on internet. We use AI and machine learning for these results.', 'imageseo'); ?>
                        </li>
                        <li>
                            <strong><?php echo AltTags::ALT_AUTO_REPRESENTATION; ?></strong> : <?php _e(': we use keywords corresponding to the analysis of the image content. We use AI and machine learning for these results.', 'imageseo'); ?>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row" class="titledesc">
                <label for="<?php echo esc_attr($options_available['alt_auto_percent']['key']); ?>">
                    <?php echo esc_html($options_available['alt_auto_percent']['label']); ?>
                </label>
            </th>
            <td class="forminp forminp-text">
                <input
                    name="<?php echo esc_attr(sprintf('%s[%s]', IMAGESEO_SLUG, $options_available['alt_auto_percent']['key'])); ?>"
                    id="<?php echo esc_attr($options_available['alt_auto_percent']['key']); ?>"
                    type="number"
                    class="regular-text"
                    required
                    placeholder="<?php esc_html_e('Your attribute value', 'imageseo'); ?>"
                    value="<?php echo esc_attr($this->options[ $options_available['alt_auto_percent']['key'] ]); ?>"
                >
				<p class="sub-label"><?php echo $options_available['alt_auto_percent']['description']; //phpcs:ignore?></p>
            </td>
        </tr>
    </tbody>
</table>

<script>
    document.addEventListener('DOMContentLoaded', function(){
        document.querySelectorAll('.button-tags').forEach(function(itm){
            const id_input = '#<?php echo $options_available['alt_value']['key']; ?>'

            itm.addEventListener('click', function(e){
                e.preventDefault();
				document.querySelector(id_input).value += e.target.dataset.tag
				this.blur()
            })
        })
    })
</script>

