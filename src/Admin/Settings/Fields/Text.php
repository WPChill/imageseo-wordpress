<?php

namespace ImageSeoWP\Admin\Settings\Fields;

class Text extends Admin_Fields {

	/**
	 * Renders field
	 */
	public function render() {
		?>
		<input id="setting-<?php echo esc_attr( $this->get_name() ); ?>" class="regular-text" type="text"
		       name="imageseo[<?php echo esc_attr( $this->get_name() ); ?>]"
		       value="<?php echo esc_attr( $this->get_value() ); ?>" <?php $this->e_placeholder(); ?> />
		<?php
	}
}
