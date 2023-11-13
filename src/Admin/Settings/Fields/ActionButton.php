<?php

namespace ImageSeoWP\Admin\Settings\Fields;
class ActionButton extends Admin_Fields {

	/** @var string */
	private $label;

	private $name;

	/**
	 * DLM_Admin_Fields_Field constructor.
	 *
	 * @param String $name
	 * @param String $link
	 * @param String $label
	 */
	public function __construct( $option, $value = false ) {
		parent::__construct( $option, '' );
	}

	/**
	 * Generate nonce
	 *
	 * @return string
	 */
	private function generate_nonce() {
		return wp_create_nonce( $this->get_name() );
	}

	/**
	 * Get prepped URL
	 *
	 * @return string
	 */
	private function get_url() {
		// Return # if no link is set
		if ( empty( $this->get_link() ) ) {
			return '#';
		}

		return add_query_arg(
			array(
				'action' => $this->get_name(),
				'nonce'  => $this->generate_nonce()
			), $this->get_link()
		);
	}

	/**
	 * Renders field
	 *
	 * The Button is quite an odd 'field'. It's basically just an a tag.
	 */
	public function render() {
		?>
		<a class="button button-primary" id="<?php echo esc_attr( sanitize_title( $this->get_id() ) ); ?>"
		   href="<?php echo esc_url( $this->get_url() ); ?>"><?php echo esc_html( $this->get_label() ); ?></a>
		<?php
	}

}
