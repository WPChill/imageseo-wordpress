<?php

use SeoImageWP\Helpers\TabsAdminSeoImage;

if (! defined('ABSPATH')) {
    exit;
}

?>

<nav class="nav-tab-wrapper">
	<?php
    foreach ($this->tabs as $key => $tab) {
        $class_active = ($this->tab_active === $key) ? 'nav-tab-active' : ''; ?>
		<a
			href="<?php echo esc_url($tab['url']); ?>"
			class="nav-tab <?php echo esc_html($class_active); ?>">
			<?php echo esc_html($tab['title']); ?>
		</a><?php
    }
    ?>
</nav>
