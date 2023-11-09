<?php

namespace ImageSeoWP\Admin\Settings\Fields;
class ActionButton extends Admin_Fields {

	/** @var string */
	private $link;

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
	public function __construct( $name, $link, $label ) {
		$this->link  = $link;
		$this->label = $label;
		$this->name  = $name;
		parent::__construct( $name, '', '' );
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
		if ( empty( $this->link ) ) {
			return '#';
		}

		return add_query_arg(
			array(
				'action' => $this->get_name(),
				'nonce'  => $this->generate_nonce()
			), $this->link
		);
	}

	/**
	 * Renders field
	 *
	 * The Button is quite an odd 'field'. It's basically just an a tag.
	 */
	public function render() {
		?>
		<a class="button button-primary" id="<?php echo esc_attr( sanitize_title( $this->name ) ); ?>"
		   href="<?php echo esc_url( $this->get_url() ); ?>"><?php echo esc_html( $this->label ); ?></a>
		<?php
	}

}
