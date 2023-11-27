<?php

namespace ImageSeoWP\Admin\Settings\Fields;

class Checkbox extends Admin_Fields {

	/**
	 * Renders field
	 */
	public function render() {
		?>
		<div>
			<div class="wpchill-toggle">
				<input class="wpchill-toggle__input" id="setting-<?php echo esc_attr( $this->get_id() ); ?>"
				       name="imageseo[<?php echo esc_attr( $this->get_name() ); ?>]" type="checkbox"
				       value="1" <?php checked( '1', $this->get_value() ); ?>>
				<div class="wpchill-toggle__items">
					<span class="wpchill-toggle__track"></span>
					<span class="wpchill-toggle__thumb"></span>
					<svg class="wpchill-toggle__off" width="6" height="6" aria-hidden="true" role="img"
					     focusable="false" viewBox="0 0 6 6">
						<path
							d="M3 1.5c.8 0 1.5.7 1.5 1.5S3.8 4.5 3 4.5 1.5 3.8 1.5 3 2.2 1.5 3 1.5M3 0C1.3 0 0 1.3 0 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3z"></path>
					</svg>
					<svg class="wpchill-toggle__on" width="2" height="6" aria-hidden="true" role="img"
					     focusable="false" viewBox="0 0 2 6">
						<path d="M0 0h2v6H0z"></path>
					</svg>
				</div>
			</div>
			<label><?php echo wp_kses_post( $this->get_cb_label() ); ?></label>
		</div>
		<?php
	}

}
