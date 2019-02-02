<?php

if (! defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Helpers\TabsAdmin;
use ImageSeoWP\Helpers\RenameTags;

$options_available = [
    'rename_delimiter' => [
        'key'         => 'rename_delimiter',
        'label'       => __('Delimiter name file', 'imageseo'),
        'description' => __('Search engines dislike blank space in url. Select the type of delimiter you want to use (if needed) in your file names.', 'imageseo'),
    ],
    'rename_value' => [
        'key'         => 'rename_value',
        'label'       => __('Rename value', 'imageseo'),
        'description' => __('Decide how you want ImageSEO to rewrite your image file names. Enter the tag(s) you want to be assigned to your files. You can use up to three tags. However we recommend you to only use %alt_auto_representation% to rename your files. File names will be written in lowercase.', 'im@ageseo'),
    ],
    'rename_auto_percent' => [
        'key'         => 'rename_auto_percent',
        'label'       => __('Rename auto percent', 'imageseo'),
        'description' => __('Our plugin works with artificial intelligence. The %alt_auto_context% and %alt_auto_representation% might therefor not be 100% accurate with your images. You need to define a threshold (between 0 and 100) of accuracy. We advise you to set up the threshold at 60%. We always chose the most relevant keyword(s).', 'imageseo'),
    ]
];

$tags = RenameTags::getTags();

?>


<table class="form-table">
    <tbody>
        <tr valign="top">
            <th scope="row" class="titledesc">
                <label for="<?php echo esc_attr($options_available['rename_value']['key']); ?>">
                    <?php echo esc_html($options_available['rename_value']['label']); ?>
                </label>

            </th>
            <td class="forminp forminp-text">
                <input
                    name="<?php echo esc_attr(sprintf('%s[%s]', IMAGESEO_SLUG, $options_available['rename_value']['key'])); ?>"
                    id="<?php echo esc_attr($options_available['rename_value']['key']); ?>"
                    type="text"
                    class="regular-text"
                    required
                    placeholder="<?php esc_html_e('Your attribute value', 'imageseo'); ?>"
                    value="<?php echo esc_attr($this->options[ $options_available['rename_value']['key'] ]); ?>"
                >
                <div class="available-structure-tags hide-if-no-js">
					<p><?php echo $options_available['rename_value']['description']; //phpcs:ignore?></p>
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
                            <strong><?php echo RenameTags::SITE_TITLE; ?> </strong>: <?php _e('We use your site title to fill up alternative texts', 'imageseo'); ?> <em>( <?php _e('Your site title is : ', 'imageseo'); echo get_bloginfo('title') ?> )</em>
                        </li>
                        <li>
                            <strong><?php echo RenameTags::ALT_AUTO_CONTEXT; ?></strong> : <?php _e('We use keyword(s) representative of the context in which Google analyzes your image on your website and on internet. We use AI and machine learning for these results.', 'imageseo'); ?>
                        </li>
                        <li>
                            <strong><?php echo RenameTags::ALT_AUTO_REPRESENTATION; ?></strong> : <?php _e(': we use keywords corresponding to the analysis of the image content. We use AI and machine learning for these results.', 'imageseo'); ?>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row" class="titledesc">
                <label for="<?php echo esc_attr($options_available['rename_delimiter']['key']); ?>">
                    <?php echo esc_html($options_available['rename_delimiter']['label']); ?>
                </label>

            </th>
            <td class="forminp forminp-text">
                <select
                    name="<?php echo esc_attr(sprintf('%s[%s]', IMAGESEO_SLUG, $options_available['rename_delimiter']['key'])); ?>"
                    id="<?php echo esc_attr($options_available['rename_delimiter']['key']); ?>"
                    required
                >
					<option value="-" <?php selected('-', $this->options[ $options_available['rename_delimiter']['key'] ]); ?>>-</option>
					<option value="_" <?php selected('_', $this->options[ $options_available['rename_delimiter']['key'] ]); ?>>_ (underscore)</option>
				</select>
				<p><?php echo $options_available['rename_delimiter']['description']; //phpcs:ignore?></p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row" class="titledesc">
                <label for="<?php echo esc_attr($options_available['rename_auto_percent']['key']); ?>">
                    <?php echo esc_html($options_available['rename_auto_percent']['label']); ?>
                </label>
            </th>
            <td class="forminp forminp-text">
                <input
                    name="<?php echo esc_attr(sprintf('%s[%s]', IMAGESEO_SLUG, $options_available['rename_auto_percent']['key'])); ?>"
                    id="<?php echo esc_attr($options_available['rename_auto_percent']['key']); ?>"
                    type="number"
                    class="regular-text"
                    required
                    placeholder="<?php esc_html_e('Your attribute value', 'imageseo'); ?>"
                    value="<?php echo esc_attr($this->options[ $options_available['rename_auto_percent']['key'] ]); ?>"
                >
				<p class="sub-label"><?php echo $options_available['rename_auto_percent']['description']; //phpcs:ignore?></p>
            </td>
        </tr>
    </tbody>
</table>

<script>
    document.addEventListener('DOMContentLoaded', function(){
        document.querySelectorAll('.button-tags').forEach(function(itm){
            const id_input = '#<?php echo $options_available['rename_value']['key']; ?>'

            itm.addEventListener('click', function(e){
                e.preventDefault();
				document.querySelector(id_input).value += e.target.dataset.tag
				this.blur()
            })
        })
    })
</script>

