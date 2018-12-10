<?php

if (! defined('ABSPATH')) {
    exit;
}

use SeoImageWP\Helpers\TabsAdminSeoImage;
use SeoImageWP\Helpers\AltTagsSeoImage;

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
    ],
    'alt_auto_percent' => [
        'key'         => 'alt_auto_percent',
        'label'       => __('Alt auto percent', 'seoimage'),
        'description' => '',
    ]
];

$tags = AltTagsSeoImage::getTags();

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
                    <p><?php esc_html_e('Available tags :', 'seoimage'); ?></p>
                    <ul class="tags">
                        <?php foreach ($tags as $tag) : ?>
                            <li class="tags__item">
                                <button type="button" data-tag="<?php echo esc_attr($tag); ?>" class="button button-secondary button-tags">
                                    <?php echo esc_html($tag); ?>
                                </button>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row" class="titledesc">
                <label for="<?php echo esc_attr($options_available['alt_auto_percent']['key']); ?>">
                    <?php echo esc_html($options_available['alt_auto_percent']['label']); ?>
                </label>
                <p class="sub-label"><?php echo $options_available['alt_auto_percent']['description']; //phpcs:ignore?></p>
            </th>
            <td class="forminp forminp-text">
                <input
                    name="<?php echo esc_attr(sprintf('%s[%s]', SEOIMAGE_SLUG, $options_available['alt_auto_percent']['key'])); ?>"
                    id="<?php echo esc_attr($options_available['alt_auto_percent']['key']); ?>"
                    type="number"
                    class="regular-text"
                    required
                    placeholder="<?php esc_html_e('Your attribute value', 'seoimage'); ?>"
                    value="<?php echo esc_attr($this->options[ $options_available['alt_auto_percent']['key'] ]); ?>"
                >
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

