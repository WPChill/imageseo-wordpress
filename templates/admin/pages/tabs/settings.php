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

?>

<?php if ($allowed): ?>
<div class="imageseo-account-info imageseo-account-info--success">
	<p><?php _e('Your account is connected with ImageSEO Application', 'imageseo'); ?></p>
</div>
<?php endif; ?>


<?php if ($allowed): ?>
	 <p>
		<strong><?php esc_html_e('Current Images limit: ', 'imageseo'); ?></strong> <?php echo $this->owner['current_request_images']; ?> /<?php echo $this->owner['plan']['limit_images']; ?>
	</p>
<?php endif; ?>

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
                    <?php _e('Go on  <a target="_blank" href="https://app.imageseo.io/register-wordpress">ImageSEO.io</a> to get your key ! If you have any questions, you can reach us out at <a href="mailto:support@imageseo.io">support@imageseo.io</a> !', 'imageseo'); ?>
				</p>
            </td>
        </tr>
    </tbody>
</table>
