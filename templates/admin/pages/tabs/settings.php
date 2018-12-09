<?php

if (! defined('ABSPATH')) {
    exit;
}

use SeoImageWP\Helpers\TabsAdminSeoImage;

$options_available = [
    'api_key' => [
        'key'         => 'api_key',
        'label'       => __('API Key', 'seoimage'),
        'description' => '',
    ],
];

?>

<table class="form-table">
    <tbody>
        <tr valign="top">
            <th scope="row" class="titledesc">
                <label for="<?php echo esc_attr($options_available['api_key']['key']); ?>">
                    <?php echo esc_html($options_available['api_key']['label']); ?>
                </label>
                <p class="sub-label"><?php echo $options_available['api_key']['description']; //phpcs:ignore?></p>
            </th>
            <td class="forminp forminp-text">
                <input
                    name="<?php echo esc_attr(sprintf('%s[%s]', SEOIMAGE_SLUG, $options_available['api_key']['key'])); ?>"
                    id="<?php echo esc_attr($options_available['api_key']['key']); ?>"
                    type="text"
                    class="regular-text"
                    required
                    placeholder="xxx"
                    value="<?php echo esc_attr($this->options[ $options_available['api_key']['key'] ]); ?>"
                >
            </td>
        </tr>
    </tbody>
</table>
