<?php

namespace ImageSeoWP\Admin\Settings\Fields;

class ColorPicker extends Admin_Fields {

	/**
	 * Renders field
	 */
	public function render() {
		if ( '' === $this->get_value() ) {
			$this->set_value( $this->get_default() );
		}
		?>
		<div class="imageseo-colorpicker">
			<input id="setting-<?php echo esc_attr( $this->get_id() ); ?>" type="text"
			       name="imageseo[<?php echo esc_attr( $this->get_name() ); ?>]"
			       value="<?php echo esc_attr( $this->get_value() ); ?>" />
		</div>
		<?php
	}
}
