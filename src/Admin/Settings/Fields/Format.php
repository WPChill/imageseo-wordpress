<?php

namespace ImageSeoWP\Admin\Settings\Fields;

class Format extends Admin_Fields {
	/**
	 * Renders field
	 */
	public function render() {

		if ( '' === $this->get_value() ) {
			$this->set_value( $this->get_default() );
		}

		foreach ( $this->get_options() as $format ) {
			?>
			<label class="dlm-radio-label">
				<input id="setting-<?php echo esc_attr( $this->get_id() ); ?>"
				       name="imageseo[<?php echo esc_attr( $this->get_name() ); ?>]"
				       type="radio"
				       value="<?php echo esc_attr( $format['format'] ); ?>" <?php checked( $format['format'], $this->get_value() ); ?> /><span><?php echo esc_html( $format['format'] ); ?></span>
				<span><?php echo wp_kses_post( $format['description'] ); ?>
			</label>
			<?php
		}
	}
}
