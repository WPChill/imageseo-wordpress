<?php

namespace ImageSeoWP\Admin\Settings\Fields;

class ColorPicker extends Admin_Fields {

	/**
	 * Renders field
	 */
	public function render() {
		?>
		<div class="imageseo-colorpicker">
			<input id="setting-<?php echo esc_attr( $this->get_name() ); ?>" type="text"
			       name="imageseo[<?php echo esc_attr( $this->get_name() ); ?>]"
			       value="<?php echo esc_attr( $this->get_value() ); ?>" />
		</div>
		<?php
	}
}
