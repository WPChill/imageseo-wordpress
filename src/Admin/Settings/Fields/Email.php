<?php

namespace ImageSeoWP\Admin\Settings\Fields;

class Email extends Admin_Fields {

	/**
	 * Renders field
	 */
	public function render() {
		if ( '' === $this->get_value() ) {
			$this->set_value( $this->get_default() );
		}
		?>
		<input id="setting-<?php echo esc_attr( $this->get_id() ); ?>" class="regular-text" type="email"
		       name="imageseo[<?php echo esc_attr( $this->get_name() ); ?>]"
		       value="<?php echo esc_attr( $this->get_value() ); ?>" <?php $this->e_placeholder(); ?> />
		<?php
	}
}
