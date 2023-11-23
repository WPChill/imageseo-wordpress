<?php

namespace ImageSeoWP\Admin\Settings\Fields;

class Hidden extends Admin_Fields {

	/**
	 * Renders field
	 */
	public function render() {
		if ( '' === $this->get_value() ) {
			$this->set_value( $this->get_default() );
		}
		?>
		<input id="setting-<?php echo esc_attr( $this->get_id() ); ?>" class="regular-text" type="hidden"
		       name="_nonce"
		       value="<?php echo esc_attr( $this->get_default() ); ?>"/>
		<?php
	}
}
