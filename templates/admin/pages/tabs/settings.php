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

$allowed = seoimage_allowed();
$class = ($allowed) ? 'seoimage-account-info--success' : 'seoimage-account-info--error';
?>

<div class="seoimage-account-info <?php echo $class; ?>">
    <?php if ($allowed): ?>
        <p><?php _e('Your account is not connected to our SeoImage application', 'seoimage'); ?></p>
    <?php else: ?>
        <p><?php _e('Your account is well connected to our SeoImage application', 'seoimage'); ?></p>
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
                    name="<?php echo esc_attr(sprintf('%s[%s]', SEOIMAGE_SLUG, $options_available['api_key']['key'])); ?>"
                    id="<?php echo esc_attr($options_available['api_key']['key']); ?>"
                    type="text"
                    class="regular-text"
                    required
                    placeholder="xxx"
                    value="<?php echo esc_attr($this->options[ $options_available['api_key']['key'] ]); ?>"
                >
                <p>
                    <?php _e('Register and log in to <a target="_blank" href="https://app.seoimage.io/register">SeoImage</a> to get your API key.', 'seoimage'); ?>
                </p>
            </td>
        </tr>
    </tbody>
</table>
