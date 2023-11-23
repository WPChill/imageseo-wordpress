<?php

namespace ImageSeoWP\Admin\Settings\Fields;

class Select extends Admin_Fields {

	/**
	 * Renders field
	 */
	public function render() {
		$value = $this->get_value();
		if ( empty( $value ) && ! empty( $this->get_default() ) ) {
			$value = $this->get_default();
		}
		?>
		<select id="setting-<?php echo esc_attr( $this->get_id() ); ?>" class="regular-text"
		        name="imageseo[<?php echo esc_attr( $this->get_name() ); ?>]"><?php
			foreach ( $this->get_options() as $key => $name ) {
				echo '<option value="' . esc_attr( $key ) . '" ' . selected( $value, $key, false ) . '>' . esc_html( $name ) . '</option>';
			}
			?></select>
		<?php
	}
}
