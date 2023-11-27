<?php

namespace ImageSeoWP\Admin\Settings\Fields;

use ImageSeoWP\Admin\Settings\Fields\Admin_Fields;

class Title extends Admin_Fields {

	/**
	 * Renders field
	 */
	public function render() {
		?>
		<h3><?php echo esc_html( $this->get_title() ); ?></h3>
		<?php
	}

}
