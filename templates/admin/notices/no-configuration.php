<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use ImageSeoWP\Helpers\Pages;

?>
<div class="error settings-error notice is-dismissible">
	<p>
		<?php
			// translators: 1 HTML Tag, 2 HTML Tag
			echo sprintf( esc_html__( 'ImageSEO is installed but not yet configured, you need to configure here : %s ImageSEO configuration page %s. The configuration takes only 1 minute! ', 'imageseo' ), '<a href="' . esc_url( Pages::getFullTabs()[Pages::SETTINGS]['url'] ) . '">', '</a>' );
		?>
	</p>
</div>
