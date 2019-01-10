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
        'description' => __('Choose the delimiter that we should use for your file names.', 'imageseo'),
    ],
    'rename_value' => [
        'key'         => 'rename_value',
        'label'       => __('Rename value', 'imageseo'),
        'description' => __('Enter the value you want to be assigned to your files. Be careful, everything will be written in lowercase. We also use a delimiter if there are spaces.', 'im@ageseo'),
    ],
    'rename_auto_percent' => [
        'key'         => 'rename_auto_percent',
        'label'       => __('Rename auto percent', 'imageseo'),
        'description' => __('Choose a minimum percentage (between 0 and 100) for which you want us to use our "auto" results to fill in your automatic alternative text. Example, if you put an 80 and we have two keywords between 80 and 100, we will choose the highest.', 'imageseo'),
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
                            <strong><?php echo RenameTags::SITE_TITLE; ?> </strong>: <?php _e('Corresponds to the title of your site', 'imageseo'); ?> <em>( <?php _e('Your site title is : ', 'imageseo'); echo get_bloginfo('title') ?> )</em>
                        </li>
                        <li>
                            <strong><?php echo RenameTags::ALT_AUTO_CONTEXT; ?></strong> : <?php _e('Keywords representative of the context in which Google analyzes your image.', 'imageseo'); ?>
                        </li>
                        <li>
                            <strong><?php echo RenameTags::ALT_AUTO_REPRESENTATION; ?></strong> : <?php _e('Keyword proposals corresponding to the analysis of the image content. We use machine learning for these results.', 'imageseo'); ?>
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

