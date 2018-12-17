jQuery(document).ready(function($) {
	function imageseo_alt_field(attachment) {
		const alt_text = $('#imageseo-alt-' + attachment).val()
		$.post(ajaxurl, {
			action: 'imageseo_media_alt_update',
			post_id: attachment,
			alt: alt_text
		})
	}
	$(this)
		.on('keydown', 'input.imageseo-alt-ajax', function(event) {
			if (event.keyCode === 13) {
				$(this).blur()
				return false
			}
		})
		.on('blur', 'input.imageseo-alt-ajax', function() {
			imageseo_alt_field($(this).data('id'))
			return false
		})
})
