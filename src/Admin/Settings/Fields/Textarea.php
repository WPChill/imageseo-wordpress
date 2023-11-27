<?php

namespace ImageSeoWP\Admin\Settings\Fields;

class Textarea extends Admin_Fields {

	/**
	 * Renders field
	 */
	public function render() {
		if ( '' === $this->get_value() ) {
			$this->set_value( $this->get_default() );
		}
		?>
		<textarea id="setting-<?php echo esc_attr( $this->get_id() ); ?>" class="large-text" cols="50" rows="3"
		          name="imageseo[<?php echo esc_attr( $this->get_name() ); ?>]" <?php $this->e_placeholder(); ?>><?php echo esc_textarea( $this->get_value() ); ?></textarea>
		<?php
	}

}
