<?php

namespace ImageSeoWP\Admin\Settings\Fields;

class Select extends Admin_Fields {

	/**
	 * Renders field
	 */
	public function render() {
		?>
		<select id="setting-<?php echo esc_attr( $this->get_id() ); ?>" class="regular-text"
		        name="imageseo[<?php echo esc_attr( $this->get_name() ); ?>]"><?php
			foreach ( $this->get_options() as $key => $name ) {
				echo '<option value="' . esc_attr( $key ) . '" ' . selected( $this->get_value(), $key, false ) . '>' . esc_html( $name ) . '</option>';
			}
			?></select>
		<?php
	}
}
