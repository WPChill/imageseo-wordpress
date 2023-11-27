<?php

namespace ImageSeoWP\Admin\Settings\Fields;

class Radio extends Admin_Fields {
	/**
	 * Renders field
	 */
	public function render() {

		if ( '' === $this->get_value() ) {
			$this->set_value( $this->get_default() );
		}

		foreach ( $this->get_options() as $key => $name ) {
			?>
			<label class="dlm-radio-label"><input id="setting-<?php echo esc_attr( $this->get_id() ); ?>"
			                                      name="imageseo[<?php echo esc_attr( $this->get_name() ); ?>]"
			                                      type="radio"
			                                      value="<?php echo esc_attr( $name ); ?>" <?php checked( $name, $this->get_value() ); ?> /><span><?php echo esc_html( $name ); ?></span></label>
			<?php
		}

	}
}
