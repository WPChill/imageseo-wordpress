<?php

namespace ImageSeoWP\Admin\Settings\Fields;

use ImageSeoWP\Admin\Settings\Fields\Admin_Fields;

class Title extends Admin_Fields {

	/** @var string */
	private $link;

	/** @var string */
	private $title;

	/**
	 * DLM_Admin_Fields_Field_Title constructor.
	 *
	 * @param String $title
	 */
	public function __construct( $title ) {
		$this->title = $title;
		parent::__construct( '', '', '' );
	}

	/**
	 * Renders field
	 */
	public function render() {
		?>
		<h3><?php echo esc_html( $this->title ); ?></h3>
		<?php
	}

}
