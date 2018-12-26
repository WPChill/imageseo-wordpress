<?php

if (! defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Helpers\TabsAdmin;

$options_available = [
    'api_key' => [
        'key'         => 'api_key',
        'label'       => __('API Key', 'imageseo'),
        'description' => '',
    ],
];

$allowed = imageseo_allowed();
$class = ($allowed) ? 'imageseo-account-info--success' : 'imageseo-account-info--error';

?>

<div class="imageseo-account-info <?php echo $class; ?>">
    <?php if ($allowed): ?>
        <p><?php _e('Your account is well connected to our ImageSeo application', 'imageseo'); ?></p>
    <?php else: ?>
        <p><?php _e('Your account is not connected to our ImageSeo application', 'imageseo'); ?></p>
    <?php endif; ?>
</div>

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
                    name="<?php echo esc_attr(sprintf('%s[%s]', IMAGESEO_SLUG, $options_available['api_key']['key'])); ?>"
                    id="<?php echo esc_attr($options_available['api_key']['key']); ?>"
                    type="text"
                    class="regular-text"
                    required
                    placeholder="xxx"
                    value="<?php echo esc_attr($this->options[ $options_available['api_key']['key'] ]); ?>"
                >
                <p>
					<?php
                    // _e('Register and log in to <a target="_blank" href="https://app.imageseo.io/register">ImageSeo</a> to get your API key.', 'imageseo');
                    ?>
                    <?php _e('During the Beta, please contact us to obtain an API key: <a href="mailto:contact@imageseo.io">contact@imageseo.io</a>', 'imageseo'); ?>
                </p>
            </td>
        </tr>
    </tbody>
</table>
