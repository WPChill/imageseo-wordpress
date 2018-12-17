<?php

if (! defined('ABSPATH')) {
    exit;
}

?>
<div class="error settings-error notice is-dismissible">
	<p>
		<?php
            /* translators: 1 is a plugin name, 2 is ImageSeo version, 3 is current php version. */
            echo sprintf(esc_html__('%1$s  requires PHP %2$s minimum, your website is actually running version %3$s.', 'imageseo'), '<strong>ImageSeo</strong>', '<code>' . esc_attr(IMAGESEO_PHP_MIN) . '</code>', '<code>' . esc_attr(phpversion()) . '</code>');
        ?>
	</p>
	<p>
		<?php
        echo esc_html__('If you are not able to upgrade, you can rollback to the previous version by using the button below.', 'imageseo');
        ?>
	</p>
</div>
