<?php

namespace ImageSeoWP\Admin\Settings\Fields;

class Hidden extends Admin_Fields {

	/**
	 * DLM_Admin_Fields_Field_Checkbox constructor.
	 *
	 * @param String $name
	 * @param String $value
	 * @param String $cb_label
	 */
	public function __construct( $name, $default ) {
		$this->default = $default;
		parent::__construct( $name, $default, '' );
	}

	public function get_default() {
		return $this->default;
	}

	/**
	 * Renders field
	 */
	public function render() {
		?>
		<input id="setting-<?php echo esc_attr( $this->get_name() ); ?>" class="regular-text" type="hidden"
		       name="_nonce"
		       value="<?php echo esc_attr( $this->get_default() ); ?>"/>
		<?php
	}
}
