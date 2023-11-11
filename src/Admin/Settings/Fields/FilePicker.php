<?php

namespace ImageSeoWP\Admin\Settings\Fields;

class FilePicker extends Admin_Fields {

	/**
	 * Renders field
	 */
	public function render() {
		?>
		<div class="imageseo-file-picker-wrapper">
			<img src="<?php echo esc_url( $this->get_value() ); ?>" class="imageseo-file-picker-image">
			<a href="#"
			   class="imageseo-file-picker button button-primary"><?php esc_html_e( 'Select file', 'imageseo' ); ?> </a>
			<input id="setting-<?php echo esc_attr( $this->get_name() ); ?>"
			       class="regular-text imageseo-file-picker-src"
			       type="hidden"
			       name="imageseo[<?php echo esc_attr( $this->get_name() ); ?>]"
			       value="<?php echo esc_attr( $this->get_value() ); ?>" <?php $this->e_placeholder(); ?> />
		</div>
		<?php
	}
}
