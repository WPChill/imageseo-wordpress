<?php

if (! defined('ABSPATH')) {
    exit;
}

?>

<div class="wrap">
	<h1><?php esc_html_e('Image SEO', 'imageseo'); ?></h1>
	<table class="wp-list-table widefat fixed striped media">
		<thead>
			<tr>
				<td id="cb" class="manage-column column-cb check-column">
					<label class="screen-reader-text" for="cb-select-all-1">Select All</label>
					<input id="cb-select-all-1" type="checkbox">
				</td>
				<th scope="col" id="title" class="manage-column column-primary">
					<span><?php _e('File'); ?></span>
				</th>
				<th scope="col" id="title" class="manage-column">
					<span><?php _e('Alternative text', 'imageseo'); ?></span>
				</th>
				<th scope="col" id="title" class="manage-column">
					<span><?php _e('Actions'); ?></span>
				</th>
			</tr>
		</thead>

		<tbody id="the-list">
			<tr id="post-20" class="author-self status-inherit">
				<th scope="row" class="check-column">
					<label class="screen-reader-text" for="cb-select-20">
						Select Capture d’écran
						2018-12-27 à 15.28.47
					</label>
					<input type="checkbox" name="media[]" id="cb-select-20" value="20">
				</th>
				<td class="title has-row-actions" data-colname="File">
					<p class="filename">
						<span class="screen-reader-text">
							File name:
						</span>
						Capture-d’écran-2018-12-27-à-15.28.47.png
					</p>
				</td>
				<td class="actions">
					Actions
				</td>
			</tr>
		</tbody>

		<tfoot>
			<tr>
				<td class="manage-column column-cb check-column">
						<label class="screen-reader-text" for="cb-select-all-2">Select All</label>
						<input id="cb-select-all-2" type="checkbox">
				</td>
				<th scope="col" class="manage-column column-primary">
					<span><?php _e('File'); ?></span>
				</th>
				<th scope="col" class="manage-column">
					<span><?php _e('Alternative text', 'imageseo'); ?></span>
				</th>
				<th scope="col" class="manage-column">
					<span><?php _e('Actions'); ?></span>
				</th>
			</tr>
		</tfoot>

	</table>
</div>
