<?php

if (!defined('ABSPATH')) {
    exit;
}

$options_available = [
    'api_key' => [
        'key'         => 'api_key',
        'label'       => __('API Key', 'imageseo'),
        'description' => '',
    ],
];

$allowed = imageseo_allowed();
$class = ($allowed) ? 'imageseo-account-info--success' : 'imageseo-account-info--error';

$limitImages = $this->owner['plan']['limit_images'] + $this->owner['bonus_stock_images'];

?>

<?php if ($allowed || (!$allowed && !empty($this->options['api_key']))) { ?>
	<div class="imageseo-account-info <?php echo $class; ?>">
		<?php if ($allowed) { ?>
			<p><?php _e('Your account is connected with ImageSEO Application', 'imageseo'); ?></p>
		<?php } elseif (!$allowed && !empty($this->options['api_key'])) { ?>
			<p><?php _e('You account is not connected with ImageSEO Application. Get an account, it’s free.', 'imageseo'); ?></p>
		<?php } ?>
	</div>
<?php } ?>


<?php if ($allowed): ?>
	 <p>
		<strong><?php esc_html_e('Current Images limit: ', 'imageseo'); ?></strong> <?php echo $this->owner['current_request_images']; ?> /<?php echo $limitImages; ?>
	</p>
<?php endif; ?>

<div class="imageseo-flex">
    <div class="fl-2">
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
                            value="<?php echo esc_attr($this->options[$options_available['api_key']['key']]); ?>"
                        >
                        <p>
                            <?php _e('Go on  <a target="_blank" href="https://app.imageseo.io/register-wordpress">ImageSEO.io</a> to get your key ! If you have any questions, you can reach us out at <a href="mailto:support@imageseo.io">support@imageseo.io</a> !', 'imageseo'); ?>
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="fl-1">
        <?php include_once __DIR__ . '/../_register.php'; ?>
    </div>
</div>

