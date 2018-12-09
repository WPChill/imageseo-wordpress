<?php

if (! defined('ABSPATH')) {
    exit;
}

use SeoImageWP\Helpers\TabsAdminSeoImage;

$options_available = [
    'active_alt_rewrite' => [
        'key'         => 'active_alt_rewrite',
        'label'       => __('Active Alt Rewrite', 'seoimage'),
        'description' => '',
    ],
    'alt_value' => [
        'key'         => 'alt_value',
        'label'       => __('Alt attribute value', 'seoimage'),
        'description' => '',
    ]
];

?>


<table class="form-table">
    <tbody>

        <tr valign="top">
            <th scope="row" class="titledesc">
                <label for="<?php echo esc_attr($options_available['active_alt_rewrite']['key']); ?>">
                    <?php echo esc_html($options_available['active_alt_rewrite']['label']); ?>
                </label>
                <p class="sub-label"><?php echo $options_available['active_alt_rewrite']['description']; //phpcs:ignore?></p>
			</th>
			<td>
				<fieldset>
					<input
						name="<?php echo esc_attr(sprintf('%s[%s]', SEOIMAGE_SLUG, $options_available['active_alt_rewrite']['key'])); ?>"
						type="checkbox"
						id="<?php echo esc_attr($options_available['active_alt_rewrite']['key']); ?>"
						<?php checked($this->options[ $options_available['active_alt_rewrite']['key'] ], 1); ?>
					>
				</fieldset>
			</td>
        </tr>
        <tr valign="top">
            <th scope="row" class="titledesc">
                <label for="<?php echo esc_attr($options_available['alt_value']['key']); ?>">
                    <?php echo esc_html($options_available['alt_value']['label']); ?>
                </label>
                <p class="sub-label"><?php echo $options_available['alt_value']['description']; //phpcs:ignore?></p>
            </th>
            <td class="forminp forminp-text">
                <input
                    name="<?php echo esc_attr(sprintf('%s[%s]', SEOIMAGE_SLUG, $options_available['alt_value']['key'])); ?>"
                    id="<?php echo esc_attr($options_available['alt_value']['key']); ?>"
                    type="text"
                    class="regular-text"
                    required
                    placeholder="<?php esc_html_e('Your attribute value', 'seoimage'); ?>"
                    value="<?php echo esc_attr($this->options[ $options_available['alt_value']['key'] ]); ?>"
                >
                <div class="available-structure-tags hide-if-no-js">
                    <p>Available tags:</p>
                    <ul class="tags">
                        <li class="tags__item">
                            <button type="button" data-tag="%alt_auto%" class="button button-secondary button-tags">
                                %alt_auto%
                            </button>
                        </li>
                        <li class="tags__item">
                            <button type="button" data-tag="%site_title%" class="button button-secondary button-tags">
                                %site_title%
                            </button>
                        </li>
                        <li class="tags__item">
                            <button type="button" data-tag="%post_title%" class="button button-secondary button-tags">
                                %post_title%
                            </button>
                        </li>
                    </ul>
                </div>
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
            })
        })
    })
</script>

