<?php

if (! defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Helpers\TabsAdmin;
use ImageSeoWP\Helpers\AltTags;

$options_available = [
    'active_alt_rewrite' => [
        'key'         => 'active_alt_rewrite',
        'label'       => __('Automatically fill in the alternative text attribute', 'imageseo'),
        'description' => __('Automatic rewriting of the alternative text on your site if it is empty. Only works with articles published with Gutenberg.', 'imageseo'),
    ],
    'active_alt_write_upload' => [
        'key'         => 'active_alt_write_upload',
        'label'       => __('Alternative text write auto on media upload', 'imageseo'),
        'description' => __('By activating this option, we will automatically write your alternative text according to the report we generate', 'imageseo'),
    ],
    'active_alt_write_with_report' => [
        'key'         => 'active_alt_write_with_report',
        'label'       => __('Alternative text automaticcaly rewrite by report', 'imageseo'),
        'description' => __('We automatically rewrite your alternative texts as soon as you generate a report. Be careful, your old alternative texts are not saved!', 'imageseo'),
    ],
    'alt_value' => [
        'key'         => 'alt_value',
        'label'       => __('Alternative text attribute value', 'imageseo'),
        'description' => __('Enter how you would like us to rewrite your alternative texts automatically. You can use our tags and add your own text.', 'imageseo'),
    ],
    'alt_auto_percent' => [
        'key'         => 'alt_auto_percent',
        'label'       => __('Alternative text auto percent', 'imageseo'),
        'description' => __('Choose a minimum percentage (between 0 and 100) for which you want us to use our "auto" results to fill in your automatic alternative text. Example, if you put an 80 and we have two keywords between 80 and 100, we will choose the highest.', 'imageseo'),
    ]
];

$tags = AltTags::getTags();

?>


<table class="form-table">
    <tbody>

        <tr valign="top">
            <th scope="row" class="titledesc">
                <label for="<?php echo esc_attr($options_available['active_alt_rewrite']['key']); ?>">
                    <?php echo esc_html($options_available['active_alt_rewrite']['label']); ?>
                </label>

			</th>
			<td>
				<fieldset>
					<input
					name="<?php echo esc_attr(sprintf('%s[%s]', IMAGESEO_SLUG, $options_available['active_alt_rewrite']['key'])); ?>"
					type="checkbox"
					id="<?php echo esc_attr($options_available['active_alt_rewrite']['key']); ?>"
					<?php checked($this->options[ $options_available['active_alt_rewrite']['key'] ], 1); ?>
					>
					<p><?php echo $options_available['active_alt_rewrite']['description']; //phpcs:ignore?></p>
				</fieldset>
			</td>
        </tr>
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
                            <strong><?php echo AltTags::SITE_TITLE; ?> </strong>: <?php _e('Corresponds to the title of your site', 'imageseo'); ?> <em>( <?php _e('Your site title is : ', 'imageseo'); echo get_bloginfo('title') ?> )</em>
                        </li>
                        <li>
                            <strong><?php echo AltTags::ALT_AUTO_CONTEXT; ?></strong> : <?php _e('Keywords representative of the context in which Google analyzes your image.', 'imageseo'); ?>
                        </li>
                        <li>
                            <strong><?php echo AltTags::ALT_AUTO_REPRESENTATION; ?></strong> : <?php _e('Keyword proposals corresponding to the analysis of the image content. We use machine learning for these results.', 'imageseo'); ?>
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

